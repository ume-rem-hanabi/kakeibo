<template>
    <v-dialog v-model="dialog" max-width="500">
        <template v-slot:activator="{ props }">
            <v-btn v-bind="props" color="primary" prepend-icon="mdi-plus"> グループ作成 </v-btn>
        </template>

        <v-card>
            <v-card-title>
                <span class="text-h5">グループ作成</span>
            </v-card-title>

            <v-card-text>
                <v-form ref="formRef" v-model="valid">
                    <v-text-field
                        v-model="name"
                        label="グループ名"
                        :rules="[rules.required]"
                        variant="outlined"
                        prepend-inner-icon="mdi-account-group"
                        required
                    ></v-text-field>
                </v-form>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="close" variant="text">キャンセル</v-btn>
                <v-btn
                    @click="create"
                    color="primary"
                    :loading="groupStore.loading"
                    :disabled="!valid"
                >
                    作成
                </v-btn>
            </v-card-actions>
        </v-card>

        <!-- 作成成功ダイアログ -->
        <v-dialog v-model="successDialog" max-width="500">
            <v-card>
                <v-card-title class="text-h5">グループ作成完了</v-card-title>
                <v-card-text>
                    <p class="mb-4">グループが作成されました！</p>
                    <v-alert type="info" variant="tonal">
                        <p class="font-weight-bold mb-2">招待コード:</p>
                        <div class="d-flex align-center">
                            <code class="text-h6 mr-2">{{ invitationCode }}</code>
                            <v-btn icon size="small" @click="copyInvitationCode" variant="text">
                                <v-icon>mdi-content-copy</v-icon>
                            </v-btn>
                        </div>
                    </v-alert>
                    <p class="text-caption mt-2">
                        この招待コードを共有して、メンバーを招待できます。
                    </p>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="closeSuccess" color="primary">閉じる</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- スナックバー -->
        <v-snackbar v-model="snackbar" :color="snackbarColor" timeout="3000">
            {{ snackbarText }}
        </v-snackbar>
    </v-dialog>
</template>

<script setup lang="ts">
    import { ref } from 'vue'
    import { useGroupStore } from '@/stores/group'
    import { rules } from '@/rules'

    const groupStore = useGroupStore()

    const dialog = ref(false)
    const successDialog = ref(false)
    const valid = ref(false)
    const name = ref('')
    const invitationCode = ref('')
    const snackbar = ref(false)
    const snackbarText = ref('')
    const snackbarColor = ref('success')
    const formRef = ref()

    async function create() {
        if (!formRef.value) return

        const { valid: isValid } = await formRef.value.validate()
        if (!isValid) return

        const group = await groupStore.createGroup(name.value)

        if (group) {
            invitationCode.value = group.invitation_code
            dialog.value = false
            successDialog.value = true
            name.value = ''
            formRef.value.reset()
        } else {
            snackbarText.value = groupStore.error || 'グループの作成に失敗しました'
            snackbarColor.value = 'error'
            snackbar.value = true
        }
    }

    function close() {
        dialog.value = false
        name.value = ''
        formRef.value?.reset()
    }

    function closeSuccess() {
        successDialog.value = false
        invitationCode.value = ''
    }

    async function copyInvitationCode() {
        try {
            await navigator.clipboard.writeText(invitationCode.value)
            snackbarText.value = 'コピーしました'
            snackbarColor.value = 'success'
            snackbar.value = true
        } catch (err) {
            snackbarText.value = 'コピーに失敗しました'
            snackbarColor.value = 'error'
            snackbar.value = true
        }
    }
</script>
