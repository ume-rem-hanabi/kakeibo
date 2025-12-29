<?php

namespace App\Repositories;

use App\Models\FixedCost;
use Illuminate\Database\Eloquent\Collection;

class FixedCostRepository
{
    /**
     * ユーザーの固定費一覧を取得
     */
    public function getByUserId(int $userId): Collection
    {
        return FixedCost::with(['category', 'user'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * IDで固定費を取得
     */
    public function findById(int $id): ?FixedCost
    {
        return FixedCost::with(['category', 'user'])->find($id);
    }

    /**
     * 固定費を作成
     */
    public function create(array $data): FixedCost
    {
        $fixedCost = FixedCost::create($data);
        return $fixedCost->load(['category', 'user']);
    }

    /**
     * 固定費を更新
     */
    public function update(FixedCost $fixedCost, array $data): FixedCost
    {
        $fixedCost->update($data);
        return $fixedCost->fresh(['category', 'user']);
    }

    /**
     * 固定費を削除（論理削除）
     */
    public function delete(FixedCost $fixedCost): bool
    {
        return $fixedCost->delete();
    }

    /**
     * ユーザーの固定費かチェック
     */
    public function isOwnedByUser(FixedCost $fixedCost, int $userId): bool
    {
        return $fixedCost->user_id === $userId;
    }
}
