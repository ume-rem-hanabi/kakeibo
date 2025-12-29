<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FixedCost\StoreFixedCostRequest;
use App\Http\Requests\FixedCost\UpdateFixedCostRequest;
use App\Services\FixedCostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FixedCostController extends Controller
{
    public function __construct(
        private FixedCostService $fixedCostService
    ) {}

    /**
     * 固定費一覧取得
     */
    public function index(Request $request): JsonResponse
    {
        $fixedCosts = $this->fixedCostService->getFixedCostsByUserId($request->user()->id);
        return response()->json($fixedCosts);
    }

    /**
     * 固定費登録
     */
    public function store(StoreFixedCostRequest $request): JsonResponse
    {
        $fixedCost = $this->fixedCostService->createFixedCost(
            $request->user()->id,
            $request->validated()
        );

        return response()->json($fixedCost, 201);
    }

    /**
     * 固定費詳細取得
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $fixedCost = $this->fixedCostService->getFixedCostById($id, $request->user()->id);
        return response()->json($fixedCost);
    }

    /**
     * 固定費更新
     */
    public function update(UpdateFixedCostRequest $request, int $id): JsonResponse
    {
        $fixedCost = $this->fixedCostService->updateFixedCost(
            $id,
            $request->user()->id,
            $request->validated()
        );

        return response()->json($fixedCost);
    }

    /**
     * 固定費削除
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->fixedCostService->deleteFixedCost($id, $request->user()->id);
        return response()->json(['message' => '固定費を削除しました']);
    }
}
