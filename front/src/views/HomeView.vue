<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <h1 class="text-h4 mb-6">ダッシュボード</h1>

                <!-- グループモード表示 -->
                <v-alert v-if="!groupStore.isPersonalMode" type="info" variant="tonal" class="mb-6">
                    グループモード: {{ groupStore.selectedGroup?.name }}
                </v-alert>

                <v-row>
                    <!-- 固定費管理カード -->
                    <v-col cols="12" md="6">
                        <v-card @click="goTo('/fixed-costs')" class="hover-card" hover>
                            <v-card-title class="d-flex align-center">
                                <v-icon left size="large" color="primary">
                                    mdi-cash-multiple
                                </v-icon>
                                <span class="ml-2">固定費管理</span>
                            </v-card-title>
                            <v-card-text> 毎月の固定費を登録・管理できます </v-card-text>
                        </v-card>
                    </v-col>

                    <!-- グループ管理カード -->
                    <v-col cols="12" md="6">
                        <v-card @click="goTo('/groups')" class="hover-card" hover>
                            <v-card-title class="d-flex align-center">
                                <v-icon left size="large" color="secondary">
                                    mdi-account-group
                                </v-icon>
                                <span class="ml-2">グループ管理</span>
                            </v-card-title>
                            <v-card-text> 家族やパートナーとデータを共有できます </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
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
    .hover-card {
        cursor: pointer;
        transition: transform 0.2s;
    }

    .hover-card:hover {
        transform: translateY(-4px);
    }
</style>
