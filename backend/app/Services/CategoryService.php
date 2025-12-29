<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Category;

class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository
    ) {}

    /**
     * カテゴリ一覧を取得
     */
    public function getCategories(): Collection
    {
        return $this->categoryRepository->getAll();
    }

    /**
     * カテゴリ詳細を取得
     */
    public function getCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }
}
