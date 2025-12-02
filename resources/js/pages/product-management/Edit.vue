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
    price?: number;
    description?: string;
    image_url?: string | null;
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

interface FormData {
    name: string;
    sku: string;
    price: number;
    description: string;
    image: File | null;
}

const form = useForm<FormData>({
    name: props.product.name,
    sku: props.product.sku,
    price: props.product.price ?? 0.0,
    description: props.product.description ?? '',
    image: null,
});

// use simple numeric input and textarea
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
                                {{ props.product.current_stock }} units
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

                            <div class="flex items-start gap-4">
                                <div>
                                    <input
                                        id="image"
                                        type="file"
                                        accept="image/*"
                                        @change="onFileChange"
                                    />
                                    <p
                                        v-if="form.errors.image"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.image }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <div v-if="preview">
                                        <span class="text-sm">New preview</span>
                                        <img
                                            :src="preview"
                                            class="h-32 w-40 rounded-md object-cover"
                                        />
                                    </div>
                                    <div v-else-if="props.product.image_url">
                                        <span class="text-sm"
                                            >Current image</span
                                        >
                                        <img
                                            :src="props.product.image_url"
                                            class="h-32 w-40 rounded-md object-cover"
                                        />
                                    </div>
                                </div>
                            </div>
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
