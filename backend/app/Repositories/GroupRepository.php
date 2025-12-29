<?php

namespace App\Repositories;

use App\Models\Group;

class GroupRepository
{
    /**
     * ユーザーが参加しているグループ一覧を取得
     */
    public function getByUserId(int $userId)
    {
        return Group::whereHas('members', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with(['owner', 'members.user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * グループIDで取得
     */
    public function findById(int $id): ?Group
    {
        return Group::with(['owner', 'members.user'])->find($id);
    }

    /**
     * 招待コードでグループを取得
     */
    public function findByInvitationCode(string $invitationCode): ?Group
    {
        return Group::where('invitation_code', $invitationCode)->first();
    }

    /**
     * グループを作成
     */
    public function create(array $data): Group
    {
        return Group::create($data);
    }

    /**
     * グループを更新
     */
    public function update(Group $group, array $data): bool
    {
        return $group->update($data);
    }

    /**
     * グループを削除（論理削除）
     */
    public function delete(Group $group): bool
    {
        return $group->delete();
    }

    /**
     * ユーザーがグループのオーナーか確認
     */
    public function isOwner(Group $group, int $userId): bool
    {
        return $group->owner_id === $userId;
    }

    /**
     * ユーザーがグループのメンバーか確認
     */
    public function isMember(Group $group, int $userId): bool
    {
        return $group->members()->where('user_id', $userId)->exists();
    }
}
