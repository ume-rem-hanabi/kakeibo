<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixed_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 誰
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // カテゴリ
            $table->string('name'); // 項目名
            $table->integer('amount'); // 金額
            $table->text('note')->nullable(); // 備考
            $table->timestamps();
            $table->softDeletes(); // 論理削除
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_costs');
    }
};
