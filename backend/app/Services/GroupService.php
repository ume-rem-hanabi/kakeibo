<?php

namespace App\Services;

use App\Repositories\GroupRepository;
use App\Repositories\GroupMemberRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class GroupService
{
    protected GroupRepository $groupRepository;
    protected GroupMemberRepository $groupMemberRepository;

    public function __construct(
        GroupRepository $groupRepository,
        GroupMemberRepository $groupMemberRepository
    ) {
        $this->groupRepository = $groupRepository;
        $this->groupMemberRepository = $groupMemberRepository;
    }

    /**
     * ユーザーが参加しているグループ一覧を取得
     */
    public function getGroupsByUserId(int $userId)
    {
        return $this->groupRepository->getByUserId($userId);
    }

    /**
     * グループを作成
     */
    public function createGroup(int $userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            // グループを作成
            $group = $this->groupRepository->create([
                'name' => $data['name'],
                'owner_id' => $userId,
            ]);

            // オーナーをメンバーとして追加
            $this->groupMemberRepository->create([
                'group_id' => $group->id,
                'user_id' => $userId,
                'role' => 'owner',
            ]);

            return $group->load(['owner', 'members.user']);
        });
    }

    /**
     * グループに参加
     */
    public function joinGroup(int $userId, string $invitationCode)
    {
        // グループを取得
        $group = $this->groupRepository->findByInvitationCode($invitationCode);

        if (!$group) {
            throw new HttpResponseException(response()->json([
                'message' => '招待コードが見つかりません。',
            ], 404));
        }

        // 既に参加済みか確認
        if ($this->groupMemberRepository->exists($userId, $group->id)) {
            throw new HttpResponseException(response()->json([
                'message' => '既にこのグループに参加しています。',
            ], 400));
        }

        // メンバーとして追加
        $this->groupMemberRepository->create([
            'group_id' => $group->id,
            'user_id' => $userId,
            'role' => 'member',
        ]);

        return $group->load(['owner', 'members.user']);
    }

    /**
     * グループから退出
     */
    public function leaveGroup(int $groupId, int $userId)
    {
        $group = $this->groupRepository->findById($groupId);

        if (!$group) {
            throw new HttpResponseException(response()->json([
                'message' => 'グループが見つかりません。',
            ], 404));
        }

        // オーナーは退出できない
        if ($this->groupRepository->isOwner($group, $userId)) {
            throw new HttpResponseException(response()->json([
                'message' => 'オーナーは退出できません。グループを削除してください。',
            ], 403));
        }

        // メンバーを取得
        $member = $this->groupMemberRepository->findByUserAndGroup($userId, $groupId);

        if (!$member) {
            throw new HttpResponseException(response()->json([
                'message' => 'グループメンバーではありません。',
            ], 404));
        }

        $this->groupMemberRepository->delete($member);

        return true;
    }

    /**
     * グループを削除（オーナーのみ）
     */
    public function deleteGroup(int $groupId, int $userId)
    {
        $group = $this->groupRepository->findById($groupId);

        if (!$group) {
            throw new HttpResponseException(response()->json([
                'message' => 'グループが見つかりません。',
            ], 404));
        }

        // オーナー確認
        if (!$this->groupRepository->isOwner($group, $userId)) {
            throw new HttpResponseException(response()->json([
                'message' => 'グループを削除する権限がありません。',
            ], 403));
        }

        return $this->groupRepository->delete($group);
    }

    /**
     * グループのメンバー一覧を取得
     */
    public function getGroupMembers(int $groupId, int $userId)
    {
        $group = $this->groupRepository->findById($groupId);

        if (!$group) {
            throw new HttpResponseException(response()->json([
                'message' => 'グループが見つかりません。',
            ], 404));
        }

        // メンバー確認
        if (!$this->groupRepository->isMember($group, $userId)) {
            throw new HttpResponseException(response()->json([
                'message' => 'グループメンバーではありません。',
            ], 403));
        }

        return $this->groupMemberRepository->getByGroupId($groupId);
    }

    /**
     * メンバーを削除（オーナーのみ）
     */
    public function removeMember(int $groupId, int $targetUserId, int $userId)
    {
        $group = $this->groupRepository->findById($groupId);

        if (!$group) {
            throw new HttpResponseException(response()->json([
                'message' => 'グループが見つかりません。',
            ], 404));
        }

        // オーナー確認
        if (!$this->groupRepository->isOwner($group, $userId)) {
            throw new HttpResponseException(response()->json([
                'message' => 'メンバーを削除する権限がありません。',
            ], 403));
        }

        // オーナー自身は削除できない
        if ($targetUserId === $userId) {
            throw new HttpResponseException(response()->json([
                'message' => 'オーナー自身は削除できません。',
            ], 400));
        }

        // メンバーを取得
        $member = $this->groupMemberRepository->findByUserAndGroup($targetUserId, $groupId);

        if (!$member) {
            throw new HttpResponseException(response()->json([
                'message' => 'メンバーが見つかりません。',
            ], 404));
        }

        return $this->groupMemberRepository->delete($member);
    }
}
