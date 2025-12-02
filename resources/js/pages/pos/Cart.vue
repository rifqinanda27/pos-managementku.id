<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{ cartItems: Array<any> }>();

const form = useForm({});

const updateQuantity = (itemId: number, qty: number) => {
    if (qty < 1) return;
    form.put(
        `/pos-terminal/${props.cartItems[0]?.user_id}/cart/${itemId}`,
        { quantity: qty },
        { preserveState: true },
    );
};

const removeItem = (itemId: number) => {
    form.delete(`/pos-terminal/${props.cartItems[0]?.user_id}/cart/${itemId}`);
};

const clearCart = () => {
    form.delete(`/pos-terminal/${props.cartItems[0]?.user_id}/cart/clear`);
};

const checkout = () => {
    form.post(`/pos-terminal/${props.cartItems[0]?.user_id}/cart/checkout`);
};
</script>

<template>
    <Head title="Cart" />

    <AppLayout>
        <div class="p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Cart</h1>
                <div>
                    <Link href="/pos-terminal" class="btn">Back to POS</Link>
                </div>
            </div>

            <div class="rounded-md border p-4">
                <div
                    v-if="!props.cartItems || props.cartItems.length === 0"
                    class="p-6 text-center text-muted-foreground"
                >
                    Cart is empty.
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="item in props.cartItems"
                        :key="item.id"
                        class="flex items-center justify-between gap-4"
                    >
                        <div class="flex items-center gap-4">
                            <img
                                :src="
                                    item.product?.image_url ||
                                    '/placeholder-80x80.png'
                                "
                                class="h-20 w-20 rounded-md object-cover"
                            />
                            <div>
                                <div class="font-medium">
                                    {{ item.product?.name }}
                                </div>
                                <div class="text-sm text-muted-foreground">
                                    SKU: {{ item.product?.sku }}
                                </div>
                                <div class="text-sm">
                                    Price: {{ item.price }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <input
                                type="number"
                                min="1"
                                :value="item.quantity"
                                @change="
                                    (e) =>
                                        updateQuantity(
                                            item.id,
                                            parseInt(e.target.value),
                                        )
                                "
                                class="input w-20"
                            />
                            <div class="text-sm">
                                Subtotal:
                                {{ (item.price * item.quantity).toFixed(2) }}
                            </div>
                            <button
                                @click="() => removeItem(item.id)"
                                class="btn btn-ghost"
                            >
                                Remove
                            </button>
                        </div>
                    </div>

                    <div class="text-right font-semibold">
                        Total:
                        {{
                            props.cartItems
                                .reduce((s, i) => s + i.price * i.quantity, 0)
                                .toFixed(2)
                        }}
                    </div>

                    <div class="flex justify-end gap-2">
                        <button @click="clearCart" class="btn">
                            Clear Cart
                        </button>
                        <button @click="checkout" class="btn btn-primary">
                            Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
