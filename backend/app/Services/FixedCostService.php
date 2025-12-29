<?php

namespace App\Services;

use App\Models\FixedCost;
use App\Repositories\FixedCostRepository;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FixedCostService
{
    public function __construct(
        private FixedCostRepository $fixedCostRepository
    ) {}

    /**
     * ユーザーの固定費一覧を取得
     */
    public function getFixedCostsByUserId(int $userId): Collection
    {
        return $this->fixedCostRepository->getByUserId($userId);
    }

    /**
     * 固定費を作成
     */
    public function createFixedCost(int $userId, array $data): FixedCost
    {
        $data['user_id'] = $userId;
        return $this->fixedCostRepository->create($data);
    }

    /**
     * 固定費詳細を取得（権限チェック付き）
     */
    public function getFixedCostById(int $id, int $userId): FixedCost
    {
        $fixedCost = $this->fixedCostRepository->findById($id);

        if (!$fixedCost) {
            throw new NotFoundHttpException('固定費が見つかりません');
        }

        if (!$this->fixedCostRepository->isOwnedByUser($fixedCost, $userId)) {
            throw new AccessDeniedHttpException('この固定費にアクセスする権限がありません');
        }

        return $fixedCost;
    }

    /**
     * 固定費を更新
     */
    public function updateFixedCost(int $id, int $userId, array $data): FixedCost
    {
        $fixedCost = $this->getFixedCostById($id, $userId);
        return $this->fixedCostRepository->update($fixedCost, $data);
    }

    /**
     * 固定費を削除
     */
    public function deleteFixedCost(int $id, int $userId): void
    {
        $fixedCost = $this->getFixedCostById($id, $userId);
        $this->fixedCostRepository->delete($fixedCost);
    }
}
