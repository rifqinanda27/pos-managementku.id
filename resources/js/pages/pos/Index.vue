<script setup lang="ts">
import ProductCard from '@/components/pos/ProductCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Product {
    id: number;
    name: string;
    sku: string;
    price?: number;
    current_stock?: number;
    total_sold?: number;
    description?: string;
    image_url?: string | null;
}

const props = defineProps<{
    products: {
        data: Product[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    auth?: { user?: { id?: number } };
}>();

const search = ref('');
</script>

<template>
    <Head title="POS Terminal" />

    <AppLayout>
        <div class="p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">POS Terminal</h1>
                <div>
                    <a
                        :href="`/pos-terminal/${props.auth?.user?.id ?? 'me'}/cart`"
                        class="btn"
                        >Open Cart</a
                    >
                </div>
            </div>

            <div class="mb-4">
                <input
                    v-model="search"
                    placeholder="Search products..."
                    class="input max-w-sm"
                />
            </div>

            <div
                class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
            >
                <ProductCard
                    v-for="product in props.products.data"
                    :key="product.id"
                    :product="product"
                />
            </div>

            <div class="mt-4 text-sm text-muted-foreground">
                Showing {{ props.products.data.length }} of
                {{ props.products.total }} products
            </div>
        </div>
    </AppLayout>
</template>
