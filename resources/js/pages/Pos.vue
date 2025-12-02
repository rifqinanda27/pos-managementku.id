<template>
    <div class="min-h-screen bg-gray-100 p-6">
        <div class="mx-auto max-w-7xl">
            <div class="rounded-lg bg-white p-6 shadow-md">
                <h1 class="mb-6 text-3xl font-bold">Point of Sales Terminal</h1>
                <div class="py-12 text-center">
                    <p class="text-lg text-gray-500">
                        POS interface will be implemented here
                    </p>
                    <p class="mt-2 text-gray-400">
                        This terminal is for cashier operations
                    </p>
                </div>
                <div class="mt-6">
                    <form @submit.prevent="logout" method="POST">
                        <button
                            type="submit"
                            class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                        >
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';

const logout = () => {
    router.post('/logout');
};
</script>
