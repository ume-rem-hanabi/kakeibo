<template>
    <v-app>
        <AppBar @toggle-drawer="drawerOpen = !drawerOpen" />
        <NavigationDrawer v-model="drawerOpen" />

        <v-main>
            <v-container class="py-8">
                <!-- ヘッダー -->
                <v-row class="mb-4">
                    <v-col cols="12">
                        <div class="d-flex align-center justify-space-between">
                            <div>
                                <h1 class="text-h4 font-weight-bold mb-2">
                                    <v-icon left size="large" color="primary"
                                        >mdi-cash-multiple</v-icon
                                    >
                                    固定費管理
                                </h1>
                                <p class="text-subtitle-1 text-grey">
                                    毎月の固定費を登録・管理できます
                                </p>
                            </div>
                            <v-btn
                                color="primary"
                                size="large"
                                prepend-icon="mdi-plus"
                                @click="openCreateDialog"
                            >
                                新規登録
                            </v-btn>
                        </div>
                    </v-col>
                </v-row>

                <!-- 合計金額カード -->
                <v-row class="mb-4">
                    <v-col cols="12" md="6">
                        <v-card color="primary" dark>
                            <v-card-text>
                                <div class="d-flex align-center justify-space-between">
                                    <div>
                                        <p class="text-subtitle-2 mb-1">月間固定費合計</p>
                                        <h2 class="text-h3 font-weight-bold">
                                            ¥{{ totalAmount.toLocaleString() }}
                                        </h2>
                                    </div>
                                    <v-icon size="80" class="opacity-50">mdi-wallet</v-icon>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-card>
                            <v-card-text>
                                <div class="d-flex align-center justify-space-between">
                                    <div>
                                        <p class="text-subtitle-2 mb-1 text-grey">登録件数</p>
                                        <h2 class="text-h3 font-weight-bold">
                                            {{ fixedCostStore.fixedCosts.length }}件
                                        </h2>
                                    </div>
                                    <v-icon size="80" color="grey-lighten-2"
                                        >mdi-format-list-numbered</v-icon
                                    >
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>

                <!-- 固定費リスト -->
                <v-row>
                    <v-col cols="12">
                        <v-card>
                            <v-card-title class="bg-grey-lighten-4">
                                <v-icon left>mdi-format-list-bulleted</v-icon>
                                固定費一覧
                            </v-card-title>

                            <v-card-text v-if="fixedCostStore.loading" class="text-center py-8">
                                <v-progress-circular
                                    indeterminate
                                    color="primary"
                                ></v-progress-circular>
                            </v-card-text>

                            <v-card-text
                                v-else-if="fixedCostStore.fixedCosts.length === 0"
                                class="text-center py-8"
                            >
                                <v-icon size="80" color="grey-lighten-2" class="mb-4"
                                    >mdi-inbox</v-icon
                                >
                                <p class="text-h6 text-grey">固定費が登録されていません</p>
                                <v-btn
                                    color="primary"
                                    class="mt-4"
                                    prepend-icon="mdi-plus"
                                    @click="openCreateDialog"
                                >
                                    最初の固定費を登録
                                </v-btn>
                            </v-card-text>

                            <v-list v-else lines="three">
                                <template
                                    v-for="(item, index) in fixedCostStore.fixedCosts"
                                    :key="item.id"
                                >
                                    <v-list-item>
                                        <template v-slot:prepend>
                                            <v-avatar :color="item.category?.color" size="48">
                                                <v-icon color="white">{{
                                                    item.category?.icon
                                                }}</v-icon>
                                            </v-avatar>
                                        </template>

                                        <v-list-item-title class="text-h6 font-weight-bold">
                                            {{ item.name }}
                                        </v-list-item-title>

                                        <v-list-item-subtitle class="mt-1">
                                            <v-chip
                                                size="small"
                                                :color="item.category?.color"
                                                class="mr-2"
                                            >
                                                {{ item.category?.name }}
                                            </v-chip>
                                            <span v-if="item.note" class="text-grey">{{
                                                item.note
                                            }}</span>
                                        </v-list-item-subtitle>

                                        <template v-slot:append>
                                            <div class="d-flex align-center">
                                                <div class="text-right mr-4">
                                                    <p
                                                        class="text-h5 font-weight-bold primary--text"
                                                    >
                                                        ¥{{ item.amount.toLocaleString() }}
                                                    </p>
                                                </div>
                                                <v-btn
                                                    icon="mdi-pencil"
                                                    variant="text"
                                                    color="primary"
                                                    @click="openEditDialog(item)"
                                                ></v-btn>
                                                <v-btn
                                                    icon="mdi-delete"
                                                    variant="text"
                                                    color="error"
                                                    @click="confirmDelete(item)"
                                                ></v-btn>
                                            </div>
                                        </template>
                                    </v-list-item>

                                    <v-divider
                                        v-if="index < fixedCostStore.fixedCosts.length - 1"
                                    ></v-divider>
                                </template>
                            </v-list>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>

        <!-- 登録・編集ダイアログ -->
        <FixedCostDialog
            v-model="dialogOpen"
            :edit-item="editItem"
            :categories="fixedCostStore.categories"
            @saved="handleSaved"
        />

        <!-- 削除確認ダイアログ -->
        <v-dialog v-model="deleteDialogOpen" max-width="400">
            <v-card>
                <v-card-title class="text-h6">
                    <v-icon left color="error">mdi-alert</v-icon>
                    削除確認
                </v-card-title>
                <v-card-text>
                    <p>「{{ deleteTarget?.name }}」を削除してもよろしいですか？</p>
                    <p class="text-caption text-grey mt-2">この操作は取り消せません。</p>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" variant="text" @click="deleteDialogOpen = false">
                        キャンセル
                    </v-btn>
                    <v-btn
                        color="error"
                        variant="elevated"
                        @click="handleDelete"
                        :loading="deleting"
                    >
                        削除
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-app>
</template>

<script setup lang="ts">
    import { ref, computed, onMounted } from 'vue'
    import { useFixedCostStore } from '@/stores/fixedCost'
    import AppBar from '@/components/layouts/AppBar.vue'
    import NavigationDrawer from '@/components/layouts/NavigationDrawer.vue'
    import FixedCostDialog from '@/components/FixedCosts/FixedCostDialog.vue'
    import type { FixedCost } from '@/types'

    const fixedCostStore = useFixedCostStore()

    const drawerOpen = ref(false)
    const dialogOpen = ref(false)
    const deleteDialogOpen = ref(false)
    const editItem = ref<FixedCost | null>(null)
    const deleteTarget = ref<FixedCost | null>(null)
    const deleting = ref(false)

    const totalAmount = computed(() => {
        return fixedCostStore.fixedCosts.reduce((sum, item) => sum + item.amount, 0)
    })

    onMounted(async () => {
        await Promise.all([fixedCostStore.fetchFixedCosts(), fixedCostStore.fetchCategories()])
    })

    function openCreateDialog() {
        editItem.value = null
        dialogOpen.value = true
    }

    function openEditDialog(item: FixedCost) {
        editItem.value = item
        dialogOpen.value = true
    }

    function confirmDelete(item: FixedCost) {
        deleteTarget.value = item
        deleteDialogOpen.value = true
    }

    async function handleDelete() {
        if (!deleteTarget.value) return

        deleting.value = true
        try {
            await fixedCostStore.deleteFixedCost(deleteTarget.value.id)
            deleteDialogOpen.value = false
            deleteTarget.value = null
        } catch (error) {
            console.error('削除エラー:', error)
            alert('削除に失敗しました')
        } finally {
            deleting.value = false
        }
    }

    function handleSaved() {
        fixedCostStore.fetchFixedCosts()
    }
</script>

<style scoped>
    .primary--text {
        color: rgb(var(--v-theme-primary));
    }
</style>
