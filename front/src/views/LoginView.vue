<template>
    <v-app>
        <v-main>
            <v-container class="fill-height" fluid>
                <v-row align="center" justify="center">
                    <v-col cols="12" sm="8" md="5" lg="4">
                        <v-card class="elevation-12 pa-4">
                            <v-card-text class="text-center pa-8">
                                <div class="mb-8">
                                    <v-icon size="80" color="primary" class="mb-4"
                                        >mdi-wallet</v-icon
                                    >
                                    <h1 class="text-h4 font-weight-bold mb-2">家計簿システム</h1>
                                    <p class="text-subtitle-1 text-grey-darken-1">
                                        Googleアカウントでログインしてください
                                    </p>
                                </div>

                                <v-btn
                                    color="primary"
                                    size="x-large"
                                    block
                                    @click="loginWithGoogle"
                                    prepend-icon="mdi-google"
                                    class="text-none"
                                    elevation="2"
                                >
                                    Googleでログイン
                                </v-btn>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script setup lang="ts">
    const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

    function loginWithGoogle() {
        window.location.href = `${API_URL}/auth/google`
    }
</script>

<style scoped>
    .fill-height {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
