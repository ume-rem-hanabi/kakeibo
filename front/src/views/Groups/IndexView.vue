<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <h1 class="text-h4 mb-6">グループ管理</h1>

                <div class="d-flex gap-2 mb-6">
                    <CreateGroupDialog />
                    <JoinGroupDialog />
                </div>

                <!-- グループ一覧 -->
                <v-card v-if="groupStore.groups.length > 0">
                    <v-list>
                        <v-list-item
                            v-for="group in groupStore.groups"
                            :key="group.id"
                            :title="group.name"
                            :subtitle="`メンバー: ${group.members.length}人`"
                        >
                            <template v-slot:prepend>
                                <v-avatar color="primary">
                                    <v-icon>mdi-account-group</v-icon>
                                </v-avatar>
                            </template>

                            <template v-slot:append>
                                <div class="d-flex gap-2">
                                    <v-chip v-if="isOwner(group)" size="small" color="primary">
                                        オーナー
                                    </v-chip>
                                    <v-btn
                                        icon
                                        size="small"
                                        @click="showMembers(group)"
                                        variant="text"
                                    >
                                        <v-icon>mdi-account-details</v-icon>
                                    </v-btn>
                                    <v-btn
                                        icon
                                        size="small"
                                        @click="copyInvitationCode(group.invitation_code)"
                                        variant="text"
                                    >
                                        <v-icon>mdi-content-copy</v-icon>
                                    </v-btn>
                                    <v-btn
                                        v-if="!isOwner(group)"
                                        icon
                                        size="small"
                                        @click="confirmLeave(group)"
                                        variant="text"
                                        color="error"
                                    >
                                        <v-icon>mdi-exit-to-app</v-icon>
                                    </v-btn>
                                    <v-btn
                                        v-if="isOwner(group)"
                                        icon
                                        size="small"
                                        @click="confirmDelete(group)"
                                        variant="text"
                                        color="error"
                                    >
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </div>
                            </template>
                        </v-list-item>
                    </v-list>
                </v-card>

                <!-- グループがない場合 -->
                <v-card v-else>
                    <v-card-text class="text-center py-12">
                        <v-icon size="64" color="grey">mdi-account-group-outline</v-icon>
                        <p class="text-h6 mt-4">まだグループがありません</p>
                        <p class="text-body-2 text-grey">
                            グループを作成するか、招待コードで参加してください
                        </p>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- メンバー一覧ダイアログ -->
        <v-dialog v-model="membersDialog" max-width="600">
            <v-card v-if="selectedGroup">
                <v-card-title>
                    <span class="text-h5">{{ selectedGroup.name }} - メンバー</span>
                </v-card-title>

                <v-card-text>
                    <v-list>
                        <v-list-item
                            v-for="member in selectedGroup.members"
                            :key="member.id"
                            :title="member.user.name"
                            :subtitle="member.user.email"
                        >
                            <template v-slot:prepend>
                                <v-avatar>
                                    <v-img
                                        v-if="member.user.avatar"
                                        :src="member.user.avatar"
                                    ></v-img>
                                    <v-icon v-else>mdi-account-circle</v-icon>
                                </v-avatar>
                            </template>

                            <template v-slot:append>
                                <v-chip v-if="member.role === 'owner'" size="small" color="primary">
                                    オーナー
                                </v-chip>
                                <v-btn
                                    v-else-if="isOwner(selectedGroup)"
                                    icon
                                    size="small"
                                    @click="confirmRemoveMember(member)"
                                    variant="text"
                                    color="error"
                                >
                                    <v-icon>mdi-account-remove</v-icon>
                                </v-btn>
                            </template>
                        </v-list-item>
                    </v-list>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="membersDialog = false" color="primary">閉じる</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- 退出確認ダイアログ -->
        <v-dialog v-model="leaveDialog" max-width="400">
            <v-card>
                <v-card-title class="text-h5">グループから退出</v-card-title>
                <v-card-text>
                    <p>本当に「{{ targetGroup?.name }}」から退出しますか？</p>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="leaveDialog = false" variant="text">キャンセル</v-btn>
                    <v-btn @click="leave" color="error" :loading="groupStore.loading"> 退出 </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- 削除確認ダイアログ -->
        <v-dialog v-model="deleteDialog" max-width="400">
            <v-card>
                <v-card-title class="text-h5">グループを削除</v-card-title>
                <v-card-text>
                    <p>本当に「{{ targetGroup?.name }}」を削除しますか？</p>
                    <v-alert type="warning" variant="tonal" class="mt-4">
                        この操作は取り消せません。
                    </v-alert>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="deleteDialog = false" variant="text">キャンセル</v-btn>
                    <v-btn @click="deleteGroupAction" color="error" :loading="groupStore.loading">
                        削除
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- メンバー削除確認ダイアログ -->
        <v-dialog v-model="removeMemberDialog" max-width="400">
            <v-card>
                <v-card-title class="text-h5">メンバーを削除</v-card-title>
                <v-card-text>
                    <p> 本当に「{{ targetMember?.user.name }}」をグループから削除しますか？ </p>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="removeMemberDialog = false" variant="text"> キャンセル </v-btn>
                    <v-btn @click="removeMemberAction" color="error" :loading="groupStore.loading">
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
    import { ref, onMounted } from 'vue'
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

    onMounted(async () => {
        await groupStore.fetchGroups()
    })

    function isOwner(group: Group): boolean {
        return group.owner_id === authStore.user?.id
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
