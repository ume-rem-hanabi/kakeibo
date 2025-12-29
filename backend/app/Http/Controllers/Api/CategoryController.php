<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    /**
     * カテゴリ一覧取得
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getCategories();
        return response()->json($categories);
    }

    /**
     * カテゴリ詳細取得
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);

        if (!$category) {
            return response()->json(['message' => 'カテゴリが見つかりません'], 404);
        }

        return response()->json($category);
    }
}
