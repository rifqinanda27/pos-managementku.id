<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Product Management', href: '/product-management' },
    { title: 'Create Product', href: '/product-management/create' },
];

interface FormData {
    name: string;
    sku: string;
    starting_stock: number;
    price: number;
    description: string;
    image: File | null;
}

const form = useForm<FormData>({
    name: '',
    sku: '',
    starting_stock: 0,
    price: 0.0,
    description: '',
    image: null,
});

import { ref } from 'vue';

const preview = ref<string | null>(null);

const onFileChange = (e: Event) => {
    const input = e.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;
    form.image = file;
    if (file) {
        preview.value = URL.createObjectURL(file);
    } else {
        preview.value = null;
    }
};

// simple numeric input for price and textarea for description

const submit = () => {
    form.post('/product-management');
};

const goBack = () => {
    router.visit('/product-management');
};
</script>

<template>
    <Head title="Create Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold">Create Product</h1>

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
                            <Label for="sku"
                                >SKU (optional - auto-generated if empty)</Label
                            >
                            <Input id="sku" v-model="form.sku" type="text" />
                            <p
                                v-if="form.errors.sku"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.sku }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="starting_stock">Starting Stock</Label>
                            <Input
                                id="starting_stock"
                                v-model.number="form.starting_stock"
                                type="number"
                                min="0"
                            />
                            <p
                                v-if="form.errors.starting_stock"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.starting_stock }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="price">Price</Label>
                            <Input
                                id="price"
                                v-model.number="form.price"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                            />
                            <p
                                v-if="form.errors.price"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.price }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                            />
                            <p
                                v-if="form.errors.description"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="image">Image</Label>
                            <input
                                id="image"
                                type="file"
                                accept="image/*"
                                @change="onFileChange"
                            />
                            <div v-if="preview" class="mt-2">
                                <img
                                    :src="preview"
                                    class="h-32 w-40 rounded-md object-cover"
                                />
                            </div>
                            <p
                                v-if="form.errors.image"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.image }}
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
