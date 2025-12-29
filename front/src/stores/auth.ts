import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

export const useAuthStore = defineStore('auth', () => {
    const token = ref<string | null>(localStorage.getItem('auth_token'))
    const user = ref<any>(null)

    const isAuthenticated = computed(() => !!token.value)

    // トークンをセット
    function setToken(newToken: string) {
        token.value = newToken
        localStorage.setItem('auth_token', newToken)
        axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
    }

    // ユーザー情報を取得
    async function fetchUser() {
        try {
            const response = await axios.get(`${API_URL}/user`)
            user.value = response.data
        } catch (error) {
            console.error('Failed to fetch user:', error)
            logout()
        }
    }

    // ログアウト
    async function logout() {
        try {
            await axios.post(`${API_URL}/logout`)
        } catch (error) {
            console.error('Logout error:', error)
        } finally {
            token.value = null
            user.value = null
            localStorage.removeItem('auth_token')
            delete axios.defaults.headers.common['Authorization']
        }
    }

    // 初期化（トークンがあればユーザー情報取得）
    if (token.value) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
        fetchUser()
    }

    return {
        token,
        user,
        isAuthenticated,
        setToken,
        fetchUser,
        logout,
    }
})
