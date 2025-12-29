<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => '家賃・住居費', 'icon' => 'mdi-home', 'color' => '#FF6B6B', 'sort_order' => 1],
            ['name' => '光熱費', 'icon' => 'mdi-lightning-bolt', 'color' => '#4ECDC4', 'sort_order' => 2],
            ['name' => '通信費', 'icon' => 'mdi-wifi', 'color' => '#45B7D1', 'sort_order' => 3],
            ['name' => 'サブスク', 'icon' => 'mdi-play-box-multiple', 'color' => '#FFA07A', 'sort_order' => 4],
            ['name' => '保険', 'icon' => 'mdi-shield-check', 'color' => '#98D8C8', 'sort_order' => 5],
            ['name' => '交通費', 'icon' => 'mdi-train-car', 'color' => '#F7B731', 'sort_order' => 6],
            ['name' => 'その他', 'icon' => 'mdi-dots-horizontal', 'color' => '#95A5A6', 'sort_order' => 99],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
