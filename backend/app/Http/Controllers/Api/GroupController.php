<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\CreateGroupRequest;
use App\Http\Requests\Group\JoinGroupRequest;
use App\Services\GroupService;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    protected GroupService $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * 参加しているグループ一覧
     */
    public function index(Request $request)
    {
        $groups = $this->groupService->getGroupsByUserId($request->user()->id);
        return response()->json($groups);
    }

    /**
     * グループを作成
     */
    public function store(CreateGroupRequest $request)
    {
        $group = $this->groupService->createGroup(
            $request->user()->id,
            $request->validated()
        );

        return response()->json($group, 201);
    }

    /**
     * グループに参加
     */
    public function join(JoinGroupRequest $request)
    {
        $group = $this->groupService->joinGroup(
            $request->user()->id,
            $request->validated()['invitation_code']
        );

        return response()->json($group, 200);
    }

    /**
     * グループから退出
     */
    public function leave(Request $request, int $id)
    {
        $this->groupService->leaveGroup($id, $request->user()->id);
        return response()->json(['message' => 'グループから退出しました。'], 200);
    }

    /**
     * グループを削除（オーナーのみ）
     */
    public function destroy(Request $request, int $id)
    {
        $this->groupService->deleteGroup($id, $request->user()->id);
        return response()->json(['message' => 'グループを削除しました。'], 200);
    }

    /**
     * グループのメンバー一覧
     */
    public function members(Request $request, int $id)
    {
        $members = $this->groupService->getGroupMembers($id, $request->user()->id);
        return response()->json($members);
    }

    /**
     * メンバーを削除（オーナーのみ）
     */
    public function removeMember(Request $request, int $id, int $userId)
    {
        $this->groupService->removeMember($id, $userId, $request->user()->id);
        return response()->json(['message' => 'メンバーを削除しました。'], 200);
    }
}
