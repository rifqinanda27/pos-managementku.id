<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    MinusIcon,
    PlusIcon,
    ShoppingCartIcon,
    TrashIcon,
    XIcon,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

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

interface CartItem {
    id: number;
    user_id: number;
    product_id: number;
    quantity: number;
    price: number;
    product: Product;
    created_at?: string;
    updated_at?: string;
}

const props = defineProps<{ cartItems: CartItem[] }>();
const isProcessing = ref(false);

// Computed properties
const userId = computed(() => props.cartItems[0]?.user_id || 'me');

const cartTotal = computed(() => {
    return props.cartItems.reduce((sum, item) => {
        return sum + parseFloat((item.price * item.quantity).toFixed(2));
    }, 0);
});

const itemCount = computed(() => {
    return props.cartItems.reduce((sum, item) => sum + item.quantity, 0);
});

const isEmpty = computed(() => props.cartItems.length === 0);

// Methods
const updateQuantity = (itemId: number, newQuantity: number) => {
    if (newQuantity < 1) return;
    if (isProcessing.value) return;

    isProcessing.value = true;
    router.put(
        `/pos-terminal/${userId.value}/cart/${itemId}`,
        { quantity: newQuantity },
        {
            preserveState: true,
            onFinish: () => {
                isProcessing.value = false;
            },
        },
    );
};

const removeItem = (itemId: number) => {
    if (isProcessing.value) return;

    isProcessing.value = true;
    router.delete(`/pos-terminal/${userId.value}/cart/${itemId}`, {
        preserveState: false,
        onFinish: () => {
            isProcessing.value = false;
        },
    });
};

const clearCart = () => {
    if (isProcessing.value) return;
    if (!confirm('Are you sure you want to clear the entire cart?')) return;

    isProcessing.value = true;
    router.delete(`/pos-terminal/${userId.value}/cart/clear`, {
        preserveState: false,
        onFinish: () => {
            isProcessing.value = false;
        },
    });
};

const checkout = () => {
    if (isProcessing.value || isEmpty.value) return;

    isProcessing.value = true;
    router.post(
        `/pos-terminal/${userId.value}/cart/checkout`,
        {},
        {
            preserveState: false,
            onFinish: () => {
                isProcessing.value = false;
            },
        },
    );
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(amount);
};
</script>

<template>
    <Head title="Shopping Cart" />

    <AppLayout>
        <div class="flex h-screen flex-col overflow-hidden bg-background">
            <!-- Header -->
            <div
                class="border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60"
            >
                <div class="flex items-center gap-4 px-4 py-4 sm:px-6">
                    <Link
                        href="/pos-terminal"
                        class="text-muted-foreground hover:text-foreground"
                    >
                        <ArrowLeftIcon class="h-5 w-5" />
                    </Link>
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold">Shopping Cart</h1>
                        <p class="text-sm text-muted-foreground">
                            {{ itemCount }} item{{ itemCount !== 1 ? 's' : '' }}
                            in cart
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div
                class="flex flex-1 flex-col gap-4 overflow-hidden p-4 sm:flex-row sm:p-6"
            >
                <!-- Cart Items Section -->
                <div class="flex-1 overflow-y-auto">
                    <!-- Empty State -->
                    <div
                        v-if="isEmpty"
                        class="flex h-full flex-col items-center justify-center text-center"
                    >
                        <div class="mb-4 rounded-full bg-muted p-6">
                            <ShoppingCartIcon
                                class="h-12 w-12 text-muted-foreground"
                            />
                        </div>
                        <h3 class="mb-2 text-lg font-semibold">
                            Cart is empty
                        </h3>
                        <p class="mb-6 max-w-sm text-sm text-muted-foreground">
                            Add products from the POS terminal to get started
                        </p>
                        <Link href="/pos-terminal" class="inline-block">
                            <Button>
                                <ArrowLeftIcon class="mr-2 h-4 w-4" />
                                Back to Shopping
                            </Button>
                        </Link>
                    </div>

                    <!-- Cart Items List -->
                    <div v-else class="space-y-3">
                        <div
                            v-for="item in cartItems"
                            :key="item.id"
                            class="group flex gap-4 rounded-lg border bg-card p-4 transition-all hover:shadow-md"
                        >
                            <!-- Product Image -->
                            <div
                                class="hidden h-24 w-24 flex-shrink-0 sm:block"
                            >
                                <img
                                    :src="
                                        item.product?.image_url ||
                                        '/assets/no-image.png'
                                    "
                                    :alt="item.product?.name"
                                    class="h-full w-full rounded-md object-cover"
                                />
                            </div>

                            <!-- Product Details -->
                            <div class="min-w-0 flex-1">
                                <div
                                    class="mb-2 flex items-start justify-between gap-2"
                                >
                                    <div>
                                        <h3 class="line-clamp-2 font-semibold">
                                            {{ item.product?.name }}
                                        </h3>
                                        <Badge variant="outline" class="mt-1">
                                            {{ item.product?.sku }}
                                        </Badge>
                                    </div>
                                    <button
                                        @click="removeItem(item.id)"
                                        :disabled="isProcessing"
                                        class="flex-shrink-0 rounded-md p-2 text-muted-foreground transition-colors hover:bg-destructive/10 hover:text-destructive disabled:opacity-50"
                                        :title="`Remove ${item.product?.name}`"
                                    >
                                        <TrashIcon class="h-4 w-4" />
                                    </button>
                                </div>

                                <!-- Price and Description -->
                                <div class="mb-3 text-sm text-muted-foreground">
                                    <div>
                                        Price: {{ formatCurrency(item.price) }}
                                    </div>
                                    <div
                                        v-if="item.product?.description"
                                        class="mt-1 line-clamp-1"
                                    >
                                        {{ item.product.description }}
                                    </div>
                                </div>

                                <!-- Quantity Controls and Subtotal -->
                                <div
                                    class="flex flex-col items-center justify-between gap-3 sm:flex-row"
                                >
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="
                                                updateQuantity(
                                                    item.id,
                                                    item.quantity - 1,
                                                )
                                            "
                                            :disabled="
                                                isProcessing ||
                                                item.quantity <= 1
                                            "
                                            class="rounded-md border p-1 transition-colors hover:bg-muted disabled:opacity-50"
                                        >
                                            <MinusIcon class="h-4 w-4" />
                                        </button>
                                        <Input
                                            type="number"
                                            min="1"
                                            :model-value="item.quantity"
                                            @change="
                                                (e: Event) => {
                                                    const target =
                                                        e.target as HTMLInputElement;
                                                    updateQuantity(
                                                        item.id,
                                                        parseInt(
                                                            target.value,
                                                        ) || 1,
                                                    );
                                                }
                                            "
                                            class="h-10 w-16 text-center"
                                            :disabled="isProcessing"
                                        />
                                        <button
                                            @click="
                                                updateQuantity(
                                                    item.id,
                                                    item.quantity + 1,
                                                )
                                            "
                                            :disabled="isProcessing"
                                            class="rounded-md border p-1 transition-colors hover:bg-muted disabled:opacity-50"
                                        >
                                            <PlusIcon class="h-4 w-4" />
                                        </button>
                                    </div>

                                    <div class="text-right">
                                        <div
                                            class="text-sm text-muted-foreground"
                                        >
                                            Subtotal
                                        </div>
                                        <div class="text-lg font-semibold">
                                            {{
                                                formatCurrency(
                                                    item.price * item.quantity,
                                                )
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Sidebar -->
                <div
                    class="sticky top-4 h-fit w-full flex-shrink-0 rounded-lg border bg-card p-6 sm:w-80"
                >
                    <div class="mb-6">
                        <h2 class="mb-4 text-lg font-semibold">
                            Order Summary
                        </h2>

                        <!-- Summary Details -->
                        <div class="space-y-3 border-b pb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-muted-foreground">Items</span>
                                <span>{{ itemCount }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-muted-foreground"
                                    >Subtotal</span
                                >
                                <span>{{ formatCurrency(cartTotal) }}</span>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between py-4">
                            <span class="font-semibold">Total</span>
                            <span class="text-2xl font-bold">
                                {{ formatCurrency(cartTotal) }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2">
                        <Button
                            @click="checkout"
                            :disabled="isEmpty || isProcessing"
                            class="w-full"
                            size="lg"
                        >
                            <ShoppingCartIcon class="mr-2 h-4 w-4" />
                            {{ isProcessing ? 'Processing...' : 'Checkout' }}
                        </Button>

                        <Button
                            @click="clearCart"
                            :disabled="isEmpty || isProcessing"
                            variant="outline"
                            class="w-full"
                        >
                            <XIcon class="mr-2 h-4 w-4" />
                            Clear Cart
                        </Button>

                        <Link href="/pos-terminal" class="block">
                            <Button
                                variant="ghost"
                                class="w-full"
                                :disabled="isProcessing"
                            >
                                <ArrowLeftIcon class="mr-2 h-4 w-4" />
                                Continue Shopping
                            </Button>
                        </Link>
                    </div>

                    <!-- Info Section -->
                    <div
                        class="mt-6 rounded-lg bg-muted/50 p-3 text-xs text-muted-foreground"
                    >
                        <p class="mb-2 font-semibold">💡 Tips</p>
                        <ul class="space-y-1">
                            <li>• Use +/- buttons to adjust quantity</li>
                            <li>• Click trash icon to remove item</li>
                            <li>• Review cart before checkout</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
