<?php

namespace App\Repositories;

use App\Models\GroupMember;

class GroupMemberRepository
{
    /**
     * グループIDでメンバー一覧を取得
     */
    public function getByGroupId(int $groupId)
    {
        return GroupMember::where('group_id', $groupId)
            ->with('user')
            ->orderBy('joined_at', 'asc')
            ->get();
    }

    /**
     * グループメンバーを作成
     */
    public function create(array $data): GroupMember
    {
        return GroupMember::create($data);
    }

    /**
     * グループメンバーを削除（論理削除）
     */
    public function delete(GroupMember $groupMember): bool
    {
        return $groupMember->delete();
    }

    /**
     * ユーザーとグループIDでメンバーを取得
     */
    public function findByUserAndGroup(int $userId, int $groupId): ?GroupMember
    {
        return GroupMember::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->first();
    }

    /**
     * グループメンバーが既に存在するか確認
     */
    public function exists(int $userId, int $groupId): bool
    {
        return GroupMember::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->exists();
    }
}
