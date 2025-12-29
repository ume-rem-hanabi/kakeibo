<template>
    <v-app-bar color="primary" dark>
        <v-app-bar-title class="cursor-pointer" @click="goHome"> 家計簿システム </v-app-bar-title>

        <!-- グループセレクター -->
        <GroupSelector v-if="authStore.user" />

        <v-spacer></v-spacer>

        <v-menu v-if="authStore.user">
            <template v-slot:activator="{ props }">
                <v-btn icon v-bind="props">
                    <v-avatar size="40">
                        <v-img v-if="authStore.user.avatar" :src="authStore.user.avatar"></v-img>
                        <v-icon v-else>mdi-account-circle</v-icon>
                    </v-avatar>
                </v-btn>
            </template>

            <v-list>
                <v-list-item>
                    <v-list-item-title>{{ authStore.user.name }}</v-list-item-title>
                    <v-list-item-subtitle>{{ authStore.user.email }}</v-list-item-subtitle>
                </v-list-item>
                <v-divider></v-divider>
                <v-list-item @click="handleLogout">
                    <template v-slot:prepend>
                        <v-icon>mdi-logout</v-icon>
                    </template>
                    <v-list-item-title>ログアウト</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-menu>
    </v-app-bar>
</template>

<script setup lang="ts">
    import { useRouter } from 'vue-router'
    import { useAuthStore } from '@/stores/auth'
    import GroupSelector from '@/components/groups/GroupSelector.vue'

    const router = useRouter()
    const authStore = useAuthStore()

    function goHome() {
        router.push('/')
    }

    async function handleLogout() {
        await authStore.logout()
        router.push('/login')
    }
</script>

<style scoped>
    .cursor-pointer {
        cursor: pointer;
    }
</style>
