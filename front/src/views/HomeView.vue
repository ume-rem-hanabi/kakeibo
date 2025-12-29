<template>
    <v-container class="py-8">
        <!-- ヘッダーセクション -->
        <v-row class="mb-6">
            <v-col cols="12">
                <div class="d-flex align-center justify-space-between">
                    <div>
                        <h1 class="text-h4 font-weight-bold mb-2">
                            <v-icon left size="large" color="primary">mdi-view-dashboard</v-icon>
                            ダッシュボード
                        </h1>
                        <p class="text-subtitle-1 text-grey">家計簿の管理と確認ができます</p>
                    </div>
                </div>
            </v-col>
        </v-row>

        <!-- グループモード表示 -->
        <v-alert v-if="!groupStore.isPersonalMode" type="info" variant="tonal" class="mb-6" prominent border="start">
            <template v-slot:prepend>
                <v-icon size="large">mdi-account-group</v-icon>
            </template>
            <div class="d-flex align-center">
                <div>
                    <div class="text-h6 mb-1">グループモード</div>
                    <div class="text-body-2">{{ groupStore.selectedGroup?.name }}</div>
                </div>
            </div>
        </v-alert>

        <!-- 機能カード -->
        <v-row class="mb-6">
            <v-col cols="12">
                <v-card>
                    <v-card-title class="bg-grey-lighten-4 pa-4">
                        <v-icon left>mdi-view-module</v-icon>
                        機能メニュー
                    </v-card-title>
                    <v-card-text class="pa-4">
                        <v-row>
                            <!-- 固定費管理カード -->
                            <v-col cols="12" md="6">
                                <v-card @click="goTo('/fixed-costs')" class="feature-card" hover elevation="0" border>
                                    <v-card-text class="pa-6">
                                        <div class="d-flex align-center mb-4">
                                            <v-avatar size="56" color="primary" variant="tonal">
                                                <v-icon size="32" color="primary">
                                                    mdi-cash-multiple
                                                </v-icon>
                                            </v-avatar>
                                        </div>
                                        <h2 class="text-h5 font-weight-bold mb-2">固定費管理</h2>
                                        <p class="text-body-2 text-grey mb-4">
                                            毎月の固定費を登録・管理できます
                                        </p>
                                        <v-btn variant="text" color="primary" append-icon="mdi-arrow-right"
                                            class="px-0">
                                            管理画面へ
                                        </v-btn>
                                    </v-card-text>
                                </v-card>
                            </v-col>

                            <!-- グループ管理カード -->
                            <v-col cols="12" md="6">
                                <v-card @click="goTo('/groups')" class="feature-card" hover elevation="0" border>
                                    <v-card-text class="pa-6">
                                        <div class="d-flex align-center mb-4">
                                            <v-avatar size="56" color="secondary" variant="tonal">
                                                <v-icon size="32" color="secondary">
                                                    mdi-account-group
                                                </v-icon>
                                            </v-avatar>
                                        </div>
                                        <h2 class="text-h5 font-weight-bold mb-2">グループ管理</h2>
                                        <p class="text-body-2 text-grey mb-4">
                                            家族やパートナーとデータを共有できます
                                        </p>
                                        <v-btn variant="text" color="secondary" append-icon="mdi-arrow-right"
                                            class="px-0">
                                            管理画面へ
                                        </v-btn>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- サマリーセクション -->
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-title class="bg-grey-lighten-4 pa-4">
                        <v-icon left>mdi-chart-box</v-icon>
                        今月のサマリー
                    </v-card-title>
                    <v-card-text class="pa-4">
                        <v-row>
                            <v-col cols="12" sm="6" md="3">
                                <v-card elevation="0" border>
                                    <v-card-text class="text-center pa-6">
                                        <v-icon size="48" color="success" class="mb-2">
                                            mdi-trending-up
                                        </v-icon>
                                        <div class="text-h4 font-weight-bold">-</div>
                                        <div class="text-body-2 text-grey">収入</div>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                                <v-card elevation="0" border>
                                    <v-card-text class="text-center pa-6">
                                        <v-icon size="48" color="error" class="mb-2">
                                            mdi-trending-down
                                        </v-icon>
                                        <div class="text-h4 font-weight-bold">-</div>
                                        <div class="text-body-2 text-grey">支出</div>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                                <v-card elevation="0" border>
                                    <v-card-text class="text-center pa-6">
                                        <v-icon size="48" color="primary" class="mb-2">
                                            mdi-cash-multiple
                                        </v-icon>
                                        <div class="text-h4 font-weight-bold">-</div>
                                        <div class="text-body-2 text-grey">固定費</div>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                                <v-card elevation="0" border>
                                    <v-card-text class="text-center pa-6">
                                        <v-icon size="48" color="info" class="mb-2">
                                            mdi-wallet
                                        </v-icon>
                                        <div class="text-h4 font-weight-bold">-</div>
                                        <div class="text-body-2 text-grey">残高</div>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                        <v-alert type="info" variant="tonal" class="mt-4">
                            <v-icon start>mdi-information</v-icon>
                            収支記録機能は開発中です
                        </v-alert>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useGroupStore } from '@/stores/group'

const router = useRouter()
const groupStore = useGroupStore()

onMounted(async () => {
    await groupStore.fetchGroups()
    groupStore.restoreSelectedGroup()
})

function goTo(path: string) {
    router.push(path)
}
</script>

<style scoped>
.feature-card {
    cursor: pointer;
    transition: all 0.3s ease;
    height: 100%;
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
}

.feature-card:active {
    transform: translateY(-2px);
}
</style>