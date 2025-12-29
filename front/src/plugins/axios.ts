import axios from 'axios'

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
    withCredentials: true, // Cookie を送信
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
})

// リクエストインターセプター（CSRF トークン取得）
api.interceptors.request.use(
    async (config) => {
        // 最初のリクエスト時に CSRF トークンを取得
        if (!document.cookie.includes('XSRF-TOKEN')) {
            await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
                withCredentials: true,
            })
        }
        return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

// レスポンスインターセプター（エラーハンドリング）
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // 未認証エラー → ログイン画面にリダイレクト
            window.location.href = '/login'
        }
        return Promise.reject(error)
    }
)

export default api