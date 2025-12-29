<template>
    <v-dialog v-model="dialog" max-width="500">
        <template v-slot:activator="{ props }">
            <v-btn v-bind="props" color="secondary" prepend-icon="mdi-account-plus">
                グループ参加
            </v-btn>
        </template>

        <v-card>
            <v-card-title>
                <span class="text-h5">グループに参加</span>
            </v-card-title>

            <v-card-text>
                <v-form ref="formRef" v-model="valid">
                    <v-text-field
                        v-model="invitationCode"
                        label="招待コード"
                        :rules="[rules.required]"
                        variant="outlined"
                        prepend-inner-icon="mdi-key"
                        placeholder="例: ABC12345"
                        required
                    ></v-text-field>
                </v-form>

                <v-alert v-if="groupStore.error" type="error" variant="tonal" class="mt-4">
                    {{ groupStore.error }}
                </v-alert>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="close" variant="text">キャンセル</v-btn>
                <v-btn
                    @click="join"
                    color="primary"
                    :loading="groupStore.loading"
                    :disabled="!valid"
                >
                    参加
                </v-btn>
            </v-card-actions>
        </v-card>

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
    const valid = ref(false)
    const invitationCode = ref('')
    const snackbar = ref(false)
    const snackbarText = ref('')
    const snackbarColor = ref('success')
    const formRef = ref()

    async function join() {
        if (!formRef.value) return

        const { valid: isValid } = await formRef.value.validate()
        if (!isValid) return

        const success = await groupStore.joinGroup(invitationCode.value)

        if (success) {
            snackbarText.value = 'グループに参加しました'
            snackbarColor.value = 'success'
            snackbar.value = true
            dialog.value = false
            invitationCode.value = ''
            formRef.value.reset()
        } else {
            snackbarText.value = groupStore.error || 'グループへの参加に失敗しました'
            snackbarColor.value = 'error'
            snackbar.value = true
        }
    }

    function close() {
        dialog.value = false
        invitationCode.value = ''
        formRef.value?.reset()
    }
</script>
