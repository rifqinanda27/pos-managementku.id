<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
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
import { Head, Link, router } from '@inertiajs/vue3';
import { MoreVertical, Plus, Search } from 'lucide-vue-next';
import { ref } from 'vue';

interface Product {
    id: number;
    name: string;
    sku: string;
    current_stock: number;
    total_sold: number;
}

interface Props {
    products: {
        data: Product[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    filters: {
        search?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Product Management', href: '/product-management' },
];

const searchQuery = ref(props.filters.search || '');
const deleteDialog = ref(false);
const productToDelete = ref<Product | null>(null);

const handleSearch = () => {
    router.get(
        '/product-management',
        { search: searchQuery.value },
        { preserveState: true },
    );
};

const openDeleteDialog = (product: Product) => {
    productToDelete.value = product;
    deleteDialog.value = true;
};

const deleteProduct = () => {
    if (productToDelete.value) {
        router.delete(`/product-management/${productToDelete.value.id}`, {
            onSuccess: () => {
                deleteDialog.value = false;
                productToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Product Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Product Management</h1>
                <Link href="/product-management/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Create Product
                    </Button>
                </Link>
            </div>

            <div class="mb-4 flex gap-2">
                <Input
                    v-model="searchQuery"
                    placeholder="Search products..."
                    class="max-w-sm"
                    @keyup.enter="handleSearch"
                />
                <Button @click="handleSearch">
                    <Search class="h-4 w-4" />
                </Button>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>SKU</TableHead>
                            <TableHead>Current Stock</TableHead>
                            <TableHead>Total Sold</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="product in products.data"
                            :key="product.id"
                        >
                            <TableCell>{{ product.name }}</TableCell>
                            <TableCell>{{ product.sku }}</TableCell>
                            <TableCell>{{ product.current_stock }}</TableCell>
                            <TableCell>{{ product.total_sold }}</TableCell>
                            <TableCell class="text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="sm">
                                            <MoreVertical class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem as-child>
                                            <Link
                                                :href="`/product-management/${product.id}/edit`"
                                            >
                                                Edit
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            @click="openDeleteDialog(product)"
                                        >
                                            Delete
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div class="text-sm text-muted-foreground">
                Showing {{ products.data.length }} of
                {{ products.total }} products
            </div>
        </div>

        <AlertDialog :open="deleteDialog" @update:open="deleteDialog = $event">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                    <AlertDialogDescription>
                        This will delete the product "{{
                            productToDelete?.name
                        }}". This action cannot be undone.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction @click="deleteProduct"
                        >Delete</AlertDialogAction
                    >
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>
