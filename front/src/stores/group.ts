import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/plugins/axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

export interface Group {
    id: number
    name: string
    owner_id: number
    invitation_code: string
    created_at: string
    updated_at: string
    owner: {
        id: number
        name: string
        email: string
        avatar?: string
    }
    members: GroupMember[]
}

export interface GroupMember {
    id: number
    group_id: number
    user_id: number
    role: 'owner' | 'member'
    joined_at: string
    user: {
        id: number
        name: string
        email: string
        avatar?: string
    }
}

export const useGroupStore = defineStore('group', () => {
    const groups = ref<Group[]>([])
    const selectedGroupId = ref<number | null>(null)
    const loading = ref(false)
    const error = ref<string | null>(null)

    // 選択中のグループ
    const selectedGroup = computed(() => {
        if (selectedGroupId.value === null) return null
        return groups.value.find(g => g.id === selectedGroupId.value) || null
    })

    // 個人モードか確認
    const isPersonalMode = computed(() => selectedGroupId.value === null)

    /**
     * グループ一覧を取得
     */
    async function fetchGroups() {
        loading.value = true
        error.value = null
        try {
            const token = localStorage.getItem('token')
            const response = await api.get(`${API_URL}/groups`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
            groups.value = response.data
        } catch (err: any) {
            error.value = err.response?.data?.message || 'グループの取得に失敗しました'
            console.error('Failed to fetch groups:', err)
        } finally {
            loading.value = false
        }
    }

    /**
     * グループを作成
     */
    async function createGroup(name: string): Promise<Group | null> {
        loading.value = true
        error.value = null
        try {
            const token = localStorage.getItem('token')
            const response = await api.post(
                `${API_URL}/groups`,
                { name },
                {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                }
            )
            const newGroup = response.data
            groups.value.push(newGroup)
            return newGroup
        } catch (err: any) {
            error.value = err.response?.data?.message || 'グループの作成に失敗しました'
            console.error('Failed to create group:', err)
            return null
        } finally {
            loading.value = false
        }
    }

    /**
     * グループに参加
     */
    async function joinGroup(invitationCode: string): Promise<boolean> {
        loading.value = true
        error.value = null
        try {
            const token = localStorage.getItem('token')
            const response = await api.post(
                `${API_URL}/groups/join`,
                { invitation_code: invitationCode },
                {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                }
            )
            const group = response.data
            groups.value.push(group)
            return true
        } catch (err: any) {
            error.value = err.response?.data?.message || 'グループへの参加に失敗しました'
            console.error('Failed to join group:', err)
            return false
        } finally {
            loading.value = false
        }
    }

    /**
     * グループから退出
     */
    async function leaveGroup(groupId: number): Promise<boolean> {
        loading.value = true
        error.value = null
        try {
            const token = localStorage.getItem('token')
            await api.delete(`${API_URL}/groups/${groupId}/leave`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
            groups.value = groups.value.filter(g => g.id !== groupId)
            if (selectedGroupId.value === groupId) {
                selectedGroupId.value = null
            }
            return true
        } catch (err: any) {
            error.value = err.response?.data?.message || 'グループからの退出に失敗しました'
            console.error('Failed to leave group:', err)
            return false
        } finally {
            loading.value = false
        }
    }

    /**
     * グループを削除
     */
    async function deleteGroup(groupId: number): Promise<boolean> {
        loading.value = true
        error.value = null
        try {
            const token = localStorage.getItem('token')
            await api.delete(`${API_URL}/groups/${groupId}`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
            groups.value = groups.value.filter(g => g.id !== groupId)
            if (selectedGroupId.value === groupId) {
                selectedGroupId.value = null
            }
            return true
        } catch (err: any) {
            error.value = err.response?.data?.message || 'グループの削除に失敗しました'
            console.error('Failed to delete group:', err)
            return false
        } finally {
            loading.value = false
        }
    }

    /**
     * メンバーを削除
     */
    async function removeMember(groupId: number, userId: number): Promise<boolean> {
        loading.value = true
        error.value = null
        try {
            const token = localStorage.getItem('token')
            await api.delete(`${API_URL}/groups/${groupId}/members/${userId}`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            })
            // グループ一覧を再取得
            await fetchGroups()
            return true
        } catch (err: any) {
            error.value = err.response?.data?.message || 'メンバーの削除に失敗しました'
            console.error('Failed to remove member:', err)
            return false
        } finally {
            loading.value = false
        }
    }

    /**
     * グループを選択
     */
    function selectGroup(groupId: number | null) {
        selectedGroupId.value = groupId
        localStorage.setItem('selectedGroupId', String(groupId))
    }

    /**
     * 個人モードに切り替え
     */
    function selectPersonalMode() {
        selectedGroupId.value = null
        localStorage.removeItem('selectedGroupId')
    }

    /**
     * 初期化時に選択状態を復元
     */
    function restoreSelectedGroup() {
        const saved = localStorage.getItem('selectedGroupId')
        if (saved && saved !== 'null') {
            selectedGroupId.value = Number(saved)
        }
    }

    return {
        groups,
        selectedGroupId,
        selectedGroup,
        isPersonalMode,
        loading,
        error,
        fetchGroups,
        createGroup,
        joinGroup,
        leaveGroup,
        deleteGroup,
        removeMember,
        selectGroup,
        selectPersonalMode,
        restoreSelectedGroup,
    }
})
