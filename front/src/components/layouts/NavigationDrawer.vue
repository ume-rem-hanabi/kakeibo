<template>
    <v-navigation-drawer v-model="drawer" temporary location="left" width="280">
        <v-list density="compact" nav>
            <v-list-item
                prepend-icon="mdi-view-dashboard"
                title="ダッシュボード"
                value="home"
                to="/"
            ></v-list-item>

            <v-list-item
                prepend-icon="mdi-cash-multiple"
                title="固定費管理"
                value="fixed-costs"
                to="/fixed-costs"
            ></v-list-item>

            <v-divider class="my-2"></v-divider>

            <v-list-item prepend-icon="mdi-cog" title="設定" value="settings"></v-list-item>
        </v-list>
    </v-navigation-drawer>
</template>

<script setup lang="ts">
    import { ref, watch } from 'vue'

    const props = defineProps<{
        modelValue: boolean
    }>()

    const emit = defineEmits<{
        (e: 'update:modelValue', value: boolean): void
    }>()

    const drawer = ref(props.modelValue)

    watch(
        () => props.modelValue,
        newVal => {
            drawer.value = newVal
        }
    )

    watch(drawer, newVal => {
        emit('update:modelValue', newVal)
    })
</script>
