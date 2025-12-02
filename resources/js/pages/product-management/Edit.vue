<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';

interface Product {
    id: number;
    name: string;
    sku: string;
    current_stock: number;
}

interface Props {
    product: Product;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Product Management', href: '/product-management' },
    {
        title: 'Edit Product',
        href: `/product-management/${props.product.id}/edit`,
    },
];

const form = useForm({
    name: props.product.name,
    sku: props.product.sku,
});

const submit = () => {
    form.put(`/product-management/${props.product.id}`);
};

const goBack = () => {
    router.visit('/product-management');
};
</script>

<template>
    <Head title="Edit Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold">Edit Product</h1>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Product Information</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="space-y-2">
                            <Label for="name">Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                            />
                            <p
                                v-if="form.errors.name"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="sku">SKU</Label>
                            <Input id="sku" v-model="form.sku" type="text" />
                            <p
                                v-if="form.errors.sku"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.sku }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Current Stock</Label>
                            <p class="text-sm text-muted-foreground">
                                {{ product.current_stock }} units
                            </p>
                        </div>

                        <div class="flex gap-2 pt-4">
                            <Button type="submit" :disabled="form.processing"
                                >Save</Button
                            >
                            <Button
                                type="button"
                                variant="outline"
                                @click="goBack"
                                >Back</Button
                            >
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
