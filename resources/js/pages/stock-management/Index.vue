<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

interface StockHistory {
    id: number;
    product: {
        name: string;
        sku: string;
    };
    user: {
        name: string;
    };
    type: string;
    quantity: number;
    notes: string | null;
    created_at: string;
}

interface Product {
    id: number;
    name: string;
    sku: string;
}

interface User {
    id: number;
    name: string;
}

interface Props {
    stockHistories: {
        data: StockHistory[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    products: Product[];
    users: User[];
    filters: {
        product_id?: number;
        user_id?: number;
        type?: string;
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Stock Management', href: '/stock-management' },
];

// const filterDialog = ref(false);

const truncateText = (text: string | null, length: number = 50) => {
    if (!text) return '-';
    return text.length > length ? text.substring(0, length) + '...' : text;
};
</script>

<template>
    <Head title="Stock Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Stock Management</h1>
                <Link href="/stock-management/update-stock">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Update Stock
                    </Button>
                </Link>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Product</TableHead>
                            <TableHead>SKU</TableHead>
                            <TableHead>Updated By</TableHead>
                            <TableHead>Type</TableHead>
                            <TableHead>Quantity</TableHead>
                            <TableHead>Notes</TableHead>
                            <TableHead>Date</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="history in stockHistories.data"
                            :key="history.id"
                        >
                            <TableCell>{{ history.product.name }}</TableCell>
                            <TableCell>{{ history.product.sku }}</TableCell>
                            <TableCell>{{ history.user.name }}</TableCell>
                            <TableCell>
                                <span
                                    :class="[
                                        'rounded px-2 py-1 text-xs',
                                        history.type === 'increase'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800',
                                    ]"
                                >
                                    {{ history.type }}
                                </span>
                            </TableCell>
                            <TableCell>{{ history.quantity }}</TableCell>
                            <TableCell>{{
                                truncateText(history.notes)
                            }}</TableCell>
                            <TableCell>
                                {{
                                    new Date(
                                        history.created_at,
                                    ).toLocaleDateString()
                                }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div class="text-sm text-muted-foreground">
                Showing {{ stockHistories.data.length }} of
                {{ stockHistories.total }} records
            </div>
        </div>
    </AppLayout>
</template>
