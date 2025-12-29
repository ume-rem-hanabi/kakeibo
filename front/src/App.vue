<template>
    <v-app>
        <!-- ログイン画面以外で AppBar と NavigationDrawer を表示 -->
        <template v-if="!isAuthPage">
            <AppBar @toggle-drawer="drawerOpen = !drawerOpen" />
            <NavigationDrawer v-model="drawerOpen" />
        </template>

        <v-main>
            <router-view />
        </v-main>
    </v-app>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import AppBar from '@/components/layouts/AppBar.vue'
import NavigationDrawer from '@/components/layouts/NavigationDrawer.vue'

const route = useRoute()
const drawerOpen = ref(false)

// ログイン画面と認証コールバック画面では AppBar を非表示
const isAuthPage = computed(() => {
    return route.path === '/login' || route.path === '/auth/callback'
})
</script>