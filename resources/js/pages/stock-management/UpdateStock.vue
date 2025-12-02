<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
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
    products: Product[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Stock Management', href: '/stock-management' },
    { title: 'Update Stock', href: '/stock-management/update-stock' },
];

const form = useForm({
    product: '',
    type: 'increase',
    update_stock: 0,
    notes: '',
});

const submit = () => {
    form.post('/stock-management/update-stock');
};

const goBack = () => {
    router.visit('/stock-management');
};
</script>

<template>
    <Head title="Update Stock" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold">Update Stock</h1>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Stock Update Information</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="space-y-2">
                            <Label for="product">Product</Label>
                            <select
                                id="product"
                                v-model="form.product"
                                class="w-full rounded-md border border-input bg-background px-3 py-2"
                                required
                            >
                                <option value="">Select a product</option>
                                <option
                                    v-for="product in products"
                                    :key="product.id"
                                    :value="product.id"
                                >
                                    {{ product.name }} ({{ product.sku }}) -
                                    Stock: {{ product.current_stock }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.product"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.product }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="type">Type</Label>
                            <select
                                id="type"
                                v-model="form.type"
                                class="w-full rounded-md border border-input bg-background px-3 py-2"
                                required
                            >
                                <option value="increase">Increase</option>
                                <option value="decrease">Decrease</option>
                            </select>
                            <p
                                v-if="form.errors.type"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.type }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="update_stock">Quantity</Label>
                            <Input
                                id="update_stock"
                                v-model.number="form.update_stock"
                                type="number"
                                min="1"
                                required
                            />
                            <p
                                v-if="form.errors.update_stock"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.update_stock }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="notes">Notes (optional)</Label>
                            <Textarea
                                id="notes"
                                v-model="form.notes"
                                placeholder="Add any notes about this stock update..."
                            />
                            <p
                                v-if="form.errors.notes"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.notes }}
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
