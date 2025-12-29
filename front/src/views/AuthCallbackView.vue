<template>
    <v-app>
        <v-main>
            <v-container class="fill-height" fluid>
                <v-row align="center" justify="center">
                    <v-col cols="12" class="text-center">
                        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                        <p class="mt-4 text-h6">ログイン処理中...</p>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

onMounted(async () => {
    const error = route.query.error as string

    if (error) {
        alert('ログインに失敗しました')
        router.push('/login')
        return
    }

    // Cookie ベース認証なので、ユーザー情報を取得するだけ
    const success = await authStore.fetchUser()
    if (success) {
        router.push('/')
    } else {
        alert('ログインに失敗しました')
        router.push('/login')
    }
})
</script>