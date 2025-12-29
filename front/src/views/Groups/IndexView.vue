<template>
    <v-container class="py-8">
        <!-- ヘッダーセクション -->
        <v-row class="mb-4">
            <v-col cols="12">
                <div class="d-flex align-center justify-space-between flex-wrap">
                    <div class="mb-4 mb-md-0">
                        <h1 class="text-h4 font-weight-bold mb-2">
                            <v-icon left size="large" color="primary">mdi-account-group</v-icon>
                            グループ管理
                        </h1>
                        <p class="text-subtitle-1 text-grey">
                            家族やパートナーとデータを共有できます
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <CreateGroupDialog />
                        <JoinGroupDialog />
                    </div>
                </div>
            </v-col>
        </v-row>

        <!-- 統計カード -->
        <v-row class="mb-4" v-if="groupStore.groups.length > 0">
            <v-col cols="12" sm="6" md="3">
                <v-card>
                    <v-card-text class="text-center pa-6">
                        <v-icon size="48" color="primary" class="mb-2">mdi-account-group</v-icon>
                        <div class="text-h4 font-weight-bold">{{ groupStore.groups.length }}</div>
                        <div class="text-body-2 text-grey">参加中のグループ</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" md="3">
                <v-card>
                    <v-card-text class="text-center pa-6">
                        <v-icon size="48" color="secondary" class="mb-2">
                            mdi-account-multiple
                        </v-icon>
                        <div class="text-h4 font-weight-bold">{{ totalMembers }}</div>
                        <div class="text-body-2 text-grey">総メンバー数</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" md="3">
                <v-card>
                    <v-card-text class="text-center pa-6">
                        <v-icon size="48" color="success" class="mb-2">mdi-crown</v-icon>
                        <div class="text-h4 font-weight-bold">{{ ownedGroupsCount }}</div>
                        <div class="text-body-2 text-grey">オーナーのグループ</div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" md="3">
                <v-card>
                    <v-card-text class="text-center pa-6">
                        <v-icon size="48" color="info" class="mb-2">mdi-account-plus</v-icon>
                        <div class="text-h4 font-weight-bold">{{ memberGroupsCount }}</div>
                        <div class="text-body-2 text-grey">参加中のグループ</div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- グループ一覧 -->
        <v-row>
            <v-col cols="12">
                <!-- グループがある場合 -->
                <v-card v-if="groupStore.groups.length > 0">
                    <v-card-title class="bg-grey-lighten-4 pa-4">
                        <v-icon left>mdi-format-list-bulleted</v-icon>
                        グループ一覧
                    </v-card-title>
                    <v-card-text class="pa-4">
                        <v-row>
                            <v-col v-for="group in groupStore.groups" :key="group.id" cols="12" md="6">
                                <v-card elevation="0" border class="group-card">
                                    <v-card-text class="pa-4">
                                        <!-- グループヘッダー -->
                                        <div class="d-flex align-center mb-3">
                                            <v-avatar size="48" :color="getGroupColor(group)">
                                                <v-icon color="white">mdi-account-group</v-icon>
                                            </v-avatar>
                                            <div class="ml-3 flex-grow-1">
                                                <h3 class="text-h6 font-weight-bold mb-1">
                                                    {{ group.name }}
                                                </h3>
                                                <p class="text-body-2 text-grey mb-0">
                                                    {{ group.members.length }}人のメンバー
                                                </p>
                                            </div>
                                            <v-chip v-if="isOwner(group)" size="small" color="primary" variant="flat">
                                                オーナー
                                            </v-chip>
                                        </div>

                                        <!-- オーナー情報 -->
                                        <v-divider class="my-3"></v-divider>
                                        <div class="d-flex align-center mb-3">
                                            <v-avatar size="32">
                                                <v-img v-if="group.owner.avatar" :src="group.owner.avatar"></v-img>
                                                <v-icon v-else size="20">mdi-account-circle</v-icon>
                                            </v-avatar>
                                            <div class="ml-2">
                                                <p class="text-caption text-grey mb-0">オーナー</p>
                                                <p class="text-body-2 font-weight-medium mb-0">
                                                    {{ group.owner.name }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- アクションボタン -->
                                        <div class="d-flex gap-2">
                                            <v-btn variant="tonal" color="primary" size="small"
                                                prepend-icon="mdi-account-details" @click="showMembers(group)" block>
                                                メンバー
                                            </v-btn>
                                            <v-btn variant="tonal" color="secondary" size="small"
                                                icon="mdi-content-copy" @click="
                                                    copyInvitationCode(group.invitation_code)
                                                    "></v-btn>
                                            <v-menu>
                                                <template v-slot:activator="{ props }">
                                                    <v-btn v-bind="props" variant="tonal" color="grey" size="small"
                                                        icon="mdi-dots-vertical"></v-btn>
                                                </template>
                                                <v-list>
                                                    <v-list-item v-if="!isOwner(group)" @click="confirmLeave(group)">
                                                        <template v-slot:prepend>
                                                            <v-icon color="error">
                                                                mdi-exit-to-app
                                                            </v-icon>
                                                        </template>
                                                        <v-list-item-title>退出</v-list-item-title>
                                                    </v-list-item>
                                                    <v-list-item v-if="isOwner(group)" @click="confirmDelete(group)">
                                                        <template v-slot:prepend>
                                                            <v-icon color="error">
                                                                mdi-delete
                                                            </v-icon>
                                                        </template>
                                                        <v-list-item-title>削除</v-list-item-title>
                                                    </v-list-item>
                                                </v-list>
                                            </v-menu>
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>

                <!-- グループがない場合 -->
                <v-card v-else>
                    <v-card-text class="text-center py-12">
                        <v-icon size="80" color="grey-lighten-2">
                            mdi-account-group-outline
                        </v-icon>
                        <p class="text-h6 mt-4">まだグループがありません</p>
                        <p class="text-body-2 text-grey mb-4">
                            グループを作成するか、招待コードで参加してください
                        </p>
                        <div class="d-flex gap-2 justify-center">
                            <CreateGroupDialog />
                            <JoinGroupDialog />
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- メンバー一覧ダイアログ -->
        <v-dialog v-model="membersDialog" max-width="600">
            <v-card v-if="selectedGroup">
                <v-card-title class="bg-grey-lighten-4 pa-4">
                    <v-icon left color="primary">mdi-account-group</v-icon>
                    {{ selectedGroup.name }} - メンバー
                </v-card-title>

                <v-card-text class="pa-4">
                    <v-list lines="two">
                        <template v-for="(member, index) in selectedGroup.members" :key="member.id">
                            <v-list-item class="px-0">
                                <template v-slot:prepend>
                                    <v-avatar size="40">
                                        <v-img v-if="member.user.avatar" :src="member.user.avatar"></v-img>
                                        <v-icon v-else>mdi-account-circle</v-icon>
                                    </v-avatar>
                                </template>

                                <v-list-item-title class="font-weight-medium">
                                    {{ member.user.name }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    {{ member.user.email }}
                                </v-list-item-subtitle>

                                <template v-slot:append>
                                    <v-chip v-if="member.role === 'owner'" size="small" color="primary" variant="flat">
                                        オーナー
                                    </v-chip>
                                    <v-btn v-else-if="isOwner(selectedGroup)" icon size="small" variant="text"
                                        color="error" @click="confirmRemoveMember(member)">
                                        <v-icon>mdi-account-remove</v-icon>
                                    </v-btn>
                                </template>
                            </v-list-item>
                            <v-divider v-if="index < selectedGroup.members.length - 1"></v-divider>
                        </template>
                    </v-list>
                </v-card-text>

                <v-card-actions class="pa-4">
                    <v-spacer></v-spacer>
                    <v-btn @click="membersDialog = false" color="primary" variant="flat">
                        閉じる
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- 退出確認ダイアログ -->
        <v-dialog v-model="leaveDialog" max-width="400">
            <v-card>
                <v-card-title class="pa-4">
                    <v-icon left color="warning">mdi-alert</v-icon>
                    グループから退出
                </v-card-title>
                <v-card-text class="pa-4">
                    <p>本当に「{{ targetGroup?.name }}」から退出しますか？</p>
                </v-card-text>
                <v-card-actions class="pa-4">
                    <v-spacer></v-spacer>
                    <v-btn @click="leaveDialog = false" variant="text">キャンセル</v-btn>
                    <v-btn @click="leave" color="error" variant="flat" :loading="groupStore.loading">
                        退出
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- 削除確認ダイアログ -->
        <v-dialog v-model="deleteDialog" max-width="400">
            <v-card>
                <v-card-title class="pa-4">
                    <v-icon left color="error">mdi-alert-circle</v-icon>
                    グループを削除
                </v-card-title>
                <v-card-text class="pa-4">
                    <p class="mb-3">本当に「{{ targetGroup?.name }}」を削除しますか？</p>
                    <v-alert type="warning" variant="tonal" density="compact">
                        この操作は取り消せません。
                    </v-alert>
                </v-card-text>
                <v-card-actions class="pa-4">
                    <v-spacer></v-spacer>
                    <v-btn @click="deleteDialog = false" variant="text">キャンセル</v-btn>
                    <v-btn @click="deleteGroupAction" color="error" variant="flat" :loading="groupStore.loading">
                        削除
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- メンバー削除確認ダイアログ -->
        <v-dialog v-model="removeMemberDialog" max-width="400">
            <v-card>
                <v-card-title class="pa-4">
                    <v-icon left color="error">mdi-account-remove</v-icon>
                    メンバーを削除
                </v-card-title>
                <v-card-text class="pa-4">
                    <p>本当に「{{ targetMember?.user.name }}」をグループから削除しますか？</p>
                </v-card-text>
                <v-card-actions class="pa-4">
                    <v-spacer></v-spacer>
                    <v-btn @click="removeMemberDialog = false" variant="text">
                        キャンセル
                    </v-btn>
                    <v-btn @click="removeMemberAction" color="error" variant="flat" :loading="groupStore.loading">
                        削除
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- スナックバー -->
        <v-snackbar v-model="snackbar" :color="snackbarColor" timeout="3000">
            {{ snackbarText }}
        </v-snackbar>
    </v-container>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useGroupStore } from '@/stores/group'
import { useAuthStore } from '@/stores/auth'
import CreateGroupDialog from '@/components/Groups/CreateGroupDialog.vue'
import JoinGroupDialog from '@/components/Groups/JoinGroupDialog.vue'
import type { Group, GroupMember } from '@/stores/group'

const groupStore = useGroupStore()
const authStore = useAuthStore()

const membersDialog = ref(false)
const leaveDialog = ref(false)
const deleteDialog = ref(false)
const removeMemberDialog = ref(false)
const selectedGroup = ref<Group | null>(null)
const targetGroup = ref<Group | null>(null)
const targetMember = ref<GroupMember | null>(null)
const snackbar = ref(false)
const snackbarText = ref('')
const snackbarColor = ref('success')

const totalMembers = computed(() => {
    return groupStore.groups.reduce((sum, group) => sum + group.members.length, 0)
})

const ownedGroupsCount = computed(() => {
    return groupStore.groups.filter(g => g.owner_id === authStore.user?.id).length
})

const memberGroupsCount = computed(() => {
    return groupStore.groups.filter(g => g.owner_id !== authStore.user?.id).length
})

onMounted(async () => {
    await groupStore.fetchGroups()
})

function isOwner(group: Group): boolean {
    return group.owner_id === authStore.user?.id
}

function getGroupColor(group: Group): string {
    const colors = ['primary', 'secondary', 'success', 'info', 'warning']
    return colors[group.id % colors.length]
}

function showMembers(group: Group) {
    selectedGroup.value = group
    membersDialog.value = true
}

async function copyInvitationCode(code: string) {
    try {
        await navigator.clipboard.writeText(code)
        snackbarText.value = '招待コードをコピーしました'
        snackbarColor.value = 'success'
        snackbar.value = true
    } catch (err) {
        snackbarText.value = 'コピーに失敗しました'
        snackbarColor.value = 'error'
        snackbar.value = true
    }
}

function confirmLeave(group: Group) {
    targetGroup.value = group
    leaveDialog.value = true
}

async function leave() {
    if (!targetGroup.value) return

    const success = await groupStore.leaveGroup(targetGroup.value.id)

    if (success) {
        snackbarText.value = 'グループから退出しました'
        snackbarColor.value = 'success'
        snackbar.value = true
        leaveDialog.value = false
    } else {
        snackbarText.value = groupStore.error || '退出に失敗しました'
        snackbarColor.value = 'error'
        snackbar.value = true
    }
}

function confirmDelete(group: Group) {
    targetGroup.value = group
    deleteDialog.value = true
}

async function deleteGroupAction() {
    if (!targetGroup.value) return

    const success = await groupStore.deleteGroup(targetGroup.value.id)

    if (success) {
        snackbarText.value = 'グループを削除しました'
        snackbarColor.value = 'success'
        snackbar.value = true
        deleteDialog.value = false
    } else {
        snackbarText.value = groupStore.error || '削除に失敗しました'
        snackbarColor.value = 'error'
        snackbar.value = true
    }
}

function confirmRemoveMember(member: GroupMember) {
    targetMember.value = member
    removeMemberDialog.value = true
}

async function removeMemberAction() {
    if (!targetMember.value || !selectedGroup.value) return

    const success = await groupStore.removeMember(
        selectedGroup.value.id,
        targetMember.value.user_id
    )

    if (success) {
        snackbarText.value = 'メンバーを削除しました'
        snackbarColor.value = 'success'
        snackbar.value = true
        removeMemberDialog.value = false
        // グループ情報を更新
        selectedGroup.value =
            groupStore.groups.find(g => g.id === selectedGroup.value?.id) || null
    } else {
        snackbarText.value = groupStore.error || 'メンバー削除に失敗しました'
        snackbarColor.value = 'error'
        snackbar.value = true
    }
}
</script>

<style scoped>
.group-card {
    transition: all 0.2s ease;
}

.group-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
}
</style>