<template>
    <v-dialog v-model="dialog" max-width="600px" persistent>
        <v-card>
            <v-card-title class="text-h5 bg-primary text-white">
                <v-icon left class="mr-2">mdi-cash-multiple</v-icon>
                {{ isEdit ? '固定費編集' : '固定費登録' }}
            </v-card-title>

            <v-card-text class="pt-6">
                <v-form ref="formRef" v-model="valid">
                    <v-select
                        v-model="formData.category_id"
                        :items="categories"
                        item-title="name"
                        item-value="id"
                        label="カテゴリ *"
                        :rules="[rules.required]"
                        prepend-icon="mdi-shape"
                        required
                    >
                        <template v-slot:item="{ props, item }">
                            <v-list-item v-bind="props">
                                <template v-slot:prepend>
                                    <v-icon :color="item.raw.color">{{ item.raw.icon }}</v-icon>
                                </template>
                            </v-list-item>
                        </template>
                        <template v-slot:selection="{ item }">
                            <v-chip :color="item.raw.color" size="small" class="mr-2">
                                <v-icon left size="small">{{ item.raw.icon }}</v-icon>
                                {{ item.raw.name }}
                            </v-chip>
                        </template>
                    </v-select>

                    <v-text-field
                        v-model="formData.name"
                        label="項目名 *"
                        :rules="[rules.required]"
                        prepend-icon="mdi-format-title"
                        counter="255"
                        required
                    ></v-text-field>

                    <v-text-field
                        v-model.number="formData.amount"
                        label="金額 *"
                        :rules="[rules.required, rules.number]"
                        prepend-icon="mdi-currency-jpy"
                        type="number"
                        suffix="円"
                        required
                    ></v-text-field>

                    <v-textarea
                        v-model="formData.note"
                        label="備考"
                        prepend-icon="mdi-note-text"
                        rows="3"
                        counter="1000"
                    ></v-textarea>
                </v-form>
            </v-card-text>

            <v-card-actions class="px-6 pb-4">
                <v-spacer></v-spacer>
                <v-btn color="grey" variant="text" @click="close" :disabled="loading">
                    キャンセル
                </v-btn>
                <v-btn
                    color="primary"
                    variant="elevated"
                    @click="save"
                    :loading="loading"
                    :disabled="!valid"
                >
                    {{ isEdit ? '更新' : '登録' }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup lang="ts">
    import { ref, watch, computed } from 'vue'
    import { useFixedCostStore } from '@/stores/fixedCost'
    import { rules } from '@/rules'
    import type { FixedCost, Category, FixedCostFormData } from '@/types'

    const props = defineProps<{
        modelValue: boolean
        editItem?: FixedCost | null
        categories: Category[]
    }>()

    const emit = defineEmits<{
        (e: 'update:modelValue', value: boolean): void
        (e: 'saved'): void
    }>()

    const fixedCostStore = useFixedCostStore()

    const dialog = ref(props.modelValue)
    const valid = ref(false)
    const formRef = ref()
    const loading = ref(false)

    const isEdit = computed(() => !!props.editItem)

    const formData = ref<FixedCostFormData>({
        category_id: null,
        name: '',
        amount: null,
        note: '',
    })

    watch(
        () => props.modelValue,
        newVal => {
            dialog.value = newVal
            if (newVal) {
                resetForm()
            }
        }
    )

    watch(dialog, newVal => {
        emit('update:modelValue', newVal)
    })

    watch(
        () => props.editItem,
        newVal => {
            if (newVal) {
                formData.value = {
                    category_id: newVal.category_id,
                    name: newVal.name,
                    amount: newVal.amount,
                    note: newVal.note || '',
                }
            }
        },
        { immediate: true }
    )

    function resetForm() {
        if (!props.editItem) {
            formData.value = {
                category_id: null,
                name: '',
                amount: null,
                note: '',
            }
        }
        formRef.value?.resetValidation()
    }

    async function save() {
        const { valid: isValid } = await formRef.value.validate()
        if (!isValid) return

        loading.value = true
        try {
            if (isEdit.value && props.editItem) {
                await fixedCostStore.updateFixedCost(props.editItem.id, formData.value)
            } else {
                await fixedCostStore.createFixedCost(formData.value)
            }
            emit('saved')
            close()
        } catch (error) {
            console.error('保存エラー:', error)
            alert('保存に失敗しました')
        } finally {
            loading.value = false
        }
    }

    function close() {
        dialog.value = false
        resetForm()
    }
</script>
