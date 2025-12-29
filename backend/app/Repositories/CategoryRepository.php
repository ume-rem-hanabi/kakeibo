<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    /**
     * カテゴリ一覧を取得
     */
    public function getAll(): Collection
    {
        return Category::orderBy('sort_order')->get();
    }

    /**
     * IDでカテゴリを取得
     */
    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    /**
     * カテゴリを作成
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * カテゴリを更新
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category->fresh();
    }

    /**
     * カテゴリを削除（論理削除）
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
