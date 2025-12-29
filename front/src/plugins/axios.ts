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

// CSRF トークン取得済みフラグ
let csrfTokenFetched = false

// リクエストインターセプター（CSRF トークン取得）
api.interceptors.request.use(
    async (config) => {
        // 最初のリクエスト時のみ CSRF トークンを取得
        if (!csrfTokenFetched) {
            try {
                await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
                    withCredentials: true,
                })
                csrfTokenFetched = true
            } catch (error) {
                console.error('Failed to fetch CSRF token:', error)
            }
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
        // 401 エラーでも自動リダイレクトしない（router で制御）
        return Promise.reject(error)
    }
)

export default api