<template>
    <v-menu offset-y>
        <template v-slot:activator="{ props }">
            <v-btn v-bind="props" variant="text" class="text-none">
                <v-icon left>mdi-account-group</v-icon>
                {{ currentGroupName }}
                <v-icon right>mdi-menu-down</v-icon>
            </v-btn>
        </template>

        <v-list>
            <!-- 個人モード -->
            <v-list-item @click="selectPersonal" :active="groupStore.isPersonalMode">
                <template v-slot:prepend>
                    <v-icon>mdi-account</v-icon>
                </template>
                <v-list-item-title>個人</v-list-item-title>
            </v-list-item>

            <v-divider v-if="groupStore.groups.length > 0"></v-divider>

            <!-- グループ一覧 -->
            <v-list-item
                v-for="group in groupStore.groups"
                :key="group.id"
                @click="selectGroup(group.id)"
                :active="groupStore.selectedGroupId === group.id"
            >
                <template v-slot:prepend>
                    <v-icon>mdi-account-group</v-icon>
                </template>
                <v-list-item-title>{{ group.name }}</v-list-item-title>
                <template v-slot:append>
                    <v-chip v-if="isOwner(group)" size="x-small" color="primary"> オーナー </v-chip>
                </template>
            </v-list-item>

            <v-divider></v-divider>

            <!-- グループ管理 -->
            <v-list-item @click="goToGroups">
                <template v-slot:prepend>
                    <v-icon>mdi-cog</v-icon>
                </template>
                <v-list-item-title>グループ管理</v-list-item-title>
            </v-list-item>
        </v-list>
    </v-menu>
</template>

<script setup lang="ts">
    import { computed } from 'vue'
    import { useRouter } from 'vue-router'
    import { useGroupStore } from '@/stores/group'
    import { useAuthStore } from '@/stores/auth'
    import type { Group } from '@/stores/group'

    const router = useRouter()
    const groupStore = useGroupStore()
    const authStore = useAuthStore()

    const currentGroupName = computed(() => {
        if (groupStore.isPersonalMode) {
            return '個人'
        }
        return groupStore.selectedGroup?.name || '個人'
    })

    function selectPersonal() {
        groupStore.selectPersonalMode()
    }

    function selectGroup(groupId: number) {
        groupStore.selectGroup(groupId)
    }

    function isOwner(group: Group): boolean {
        return group.owner_id === authStore.user?.id
    }

    function goToGroups() {
        router.push('/groups')
    }
</script>
