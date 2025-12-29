<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FixedCostController;
use App\Http\Controllers\Api\GroupController;

// 公開ルート（認証不要）
Route::prefix('auth')->group(function () {
    Route::get('google', [AuthController::class, 'redirectToGoogle']);
    Route::get('google/callback', [AuthController::class, 'handleGoogleCallback']);
});

// 認証が必要なルート
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    // カテゴリ
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);

    // 固定費
    Route::apiResource('fixed-costs', FixedCostController::class);

    // グループ
    Route::prefix('groups')->group(function () {
        Route::get('/', [GroupController::class, 'index']); // グループ一覧
        Route::post('/', [GroupController::class, 'store']); // グループ作成
        Route::post('/join', [GroupController::class, 'join']); // グループ参加
        Route::delete('/{id}/leave', [GroupController::class, 'leave']); // グループ退出
        Route::delete('/{id}', [GroupController::class, 'destroy']); // グループ削除
        Route::get('/{id}/members', [GroupController::class, 'members']); // メンバー一覧
        Route::delete('/{id}/members/{userId}', [GroupController::class, 'removeMember']); // メンバー削除
    });
});
