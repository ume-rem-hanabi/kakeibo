import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import type { FixedCost, Category } from '@/types'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

export const useFixedCostStore = defineStore('fixedCost', () => {
    const fixedCosts = ref<FixedCost[]>([])
    const categories = ref<Category[]>([])
    const loading = ref(false)

    // 固定費一覧取得
    async function fetchFixedCosts() {
        loading.value = true
        try {
            const response = await axios.get(`${API_URL}/fixed-costs`)
            fixedCosts.value = response.data
        } catch (error) {
            console.error('Failed to fetch fixed costs:', error)
            throw error
        } finally {
            loading.value = false
        }
    }

    // カテゴリ一覧取得
    async function fetchCategories() {
        try {
            const response = await axios.get(`${API_URL}/categories`)
            categories.value = response.data
        } catch (error) {
            console.error('Failed to fetch categories:', error)
            throw error
        }
    }

    // 固定費作成
    async function createFixedCost(data: any) {
        loading.value = true
        try {
            const response = await axios.post(`${API_URL}/fixed-costs`, data)
            fixedCosts.value.unshift(response.data)
            return response.data
        } catch (error) {
            console.error('Failed to create fixed cost:', error)
            throw error
        } finally {
            loading.value = false
        }
    }

    // 固定費更新
    async function updateFixedCost(id: number, data: any) {
        loading.value = true
        try {
            const response = await axios.put(`${API_URL}/fixed-costs/${id}`, data)
            const index = fixedCosts.value.findIndex(fc => fc.id === id)
            if (index !== -1) {
                fixedCosts.value[index] = response.data
            }
            return response.data
        } catch (error) {
            console.error('Failed to update fixed cost:', error)
            throw error
        } finally {
            loading.value = false
        }
    }

    // 固定費削除
    async function deleteFixedCost(id: number) {
        loading.value = true
        try {
            await axios.delete(`${API_URL}/fixed-costs/${id}`)
            fixedCosts.value = fixedCosts.value.filter(fc => fc.id !== id)
        } catch (error) {
            console.error('Failed to delete fixed cost:', error)
            throw error
        } finally {
            loading.value = false
        }
    }

    return {
        fixedCosts,
        categories,
        loading,
        fetchFixedCosts,
        fetchCategories,
        createFixedCost,
        updateFixedCost,
        deleteFixedCost,
    }
})
