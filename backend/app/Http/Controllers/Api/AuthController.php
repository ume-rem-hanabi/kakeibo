<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Googleログインへリダイレクト
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->redirect();
    }

    /**
     * Googleからのコールバック処理
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // ユーザーを検索または作成
            $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'avatar' => $googleUser->getAvatar(),
                ]
            );

            // Laravel Sanctum の Cookie ベース認証でログイン
            Auth::login($user);

            // フロントエンドにリダイレクト
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect()->away("{$frontendUrl}/auth/callback");
        } catch (\Exception $e) {
            \Log::error('Google OAuth failed: ' . $e->getMessage());
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect()->away("{$frontendUrl}/login?error=authentication_failed");
        }
    }

    /**
     * ログアウト
     */
    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json([
            'message' => 'ログアウトしました'
        ]);
    }

    /**
     * 認証ユーザー情報取得
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
