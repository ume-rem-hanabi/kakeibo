import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/plugins/axios'

export const useAuthStore = defineStore('auth', () => {
    const user = ref<any>(null)
    const loading = ref(false)

    const isAuthenticated = computed(() => !!user.value)

    // ユーザー情報を取得
    async function fetchUser() {
        loading.value = true
        try {
            const response = await api.get('/user')
            user.value = response.data
            return true
        } catch (error) {
            console.error('Failed to fetch user:', error)
            user.value = null
            return false
        } finally {
            loading.value = false
        }
    }

    // ログアウト
    async function logout() {
        try {
            await api.post('/logout')
        } catch (error) {
            console.error('Logout error:', error)
        } finally {
            user.value = null
        }
    }

    return {
        user,
        loading,
        isAuthenticated,
        fetchUser,
        logout,
    }
})