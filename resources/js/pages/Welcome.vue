<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { dashboard, login } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import {
    BarChart3,
    Bot,
    CheckCircle2,
    Package,
    ShoppingCart,
    Users,
} from 'lucide-vue-next';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const features = [
    {
        icon: ShoppingCart,
        title: 'POS Terminal',
        description:
            'Sell quickly with a clean product grid, instant add-to-cart, and smooth checkout.',
    },
    {
        icon: Package,
        title: 'Product Management',
        description:
            'Keep your catalog tidy—names, SKUs, prices, stock, and images in one place.',
    },
    {
        icon: Users,
        title: 'User Management',
        description:
            'Give the right access to Super Admins, Admins, and Cashiers—simple and safe.',
    },
    {
        icon: BarChart3,
        title: 'Stock & Reports',
        description:
            'Know what moved and when. Filter stock changes and review transaction history.',
    },
    {
        icon: Bot,
        title: 'AI Chatbot Assistant',
        description:
            'Get quick answers about products, sales, and inventory with an intelligent helper.',
    },
];

const flow = [
    {
        title: 'Set up products',
        detail: 'Add items with prices, SKUs, stock, and images—ready for the floor.',
    },
    {
        title: 'Start selling',
        detail: 'Cashiers use the POS to add items to the cart and checkout fast.',
    },
    {
        title: 'Track stock',
        detail: 'Every change is recorded. See increases and decreases at a glance.',
    },
    {
        title: 'Review sales',
        detail: 'Browse transactions with filters to find the moments that matter.',
    },
    {
        title: 'Ask the AI',
        detail: 'Chat with your intelligent assistant for instant insights and help.',
    },
];

const stats = [
    {
        label: 'User Roles',
        value: '3',
        detail: 'Super Admin · Admin · Cashier',
    },
    {
        label: 'Core Areas',
        value: '5',
        detail: 'POS · Products · Users · Stock · AI Chat',
    },
];
</script>

<template>
    <Head title="Welcome" />
    <div class="min-h-screen bg-background">
        <!-- Header -->
        <header
            class="sticky top-0 z-50 border-b border-border bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60"
        >
            <div
                class="container mx-auto flex h-16 items-center justify-between px-4"
            >
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10"
                    >
                        <ShoppingCart class="h-6 w-6 text-primary" />
                    </div>
                    <span class="text-xl font-bold">POS Management</span>
                </div>
                <nav class="flex items-center gap-3">
                    <Badge variant="outline" class="hidden sm:inline-flex"
                        >Retail-ready</Badge
                    >
                    <Link href="/setup">
                        <Button variant="outline" size="sm">Setup Guide</Button>
                    </Link>
                    <Link v-if="$page.props.auth.user" :href="dashboard()">
                        <Button size="sm">Dashboard</Button>
                    </Link>
                    <template v-else>
                        <Link :href="login()">
                            <Button size="sm">Log in</Button>
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero: focus on outcomes, not tech -->
        <section
            class="border-b border-border bg-gradient-to-b from-primary/5 via-background to-background py-16"
        >
            <div class="container mx-auto px-4 text-center">
                <div class="mx-auto max-w-3xl space-y-6">
                    <div
                        class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold text-muted-foreground"
                    >
                        <CheckCircle2 class="h-3.5 w-3.5 text-primary" />
                        Sell smarter. Manage easier.
                    </div>
                    <h1 class="text-4xl leading-tight font-bold sm:text-5xl">
                        Everything you need to run the counter
                    </h1>
                    <p class="text-lg text-muted-foreground">
                        A friendly POS that helps you sell, keep stock honest,
                        and understand your sales—without the clutter.
                    </p>
                    <div class="flex flex-wrap justify-center gap-3">
                        <Link v-if="!$page.props.auth.user" :href="login()">
                            <Button size="lg">Get started</Button>
                        </Link>
                        <Link v-else :href="dashboard()">
                            <Button size="lg">Open dashboard</Button>
                        </Link>
                        <Button
                            variant="outline"
                            size="lg"
                            as="a"
                            href="#modules"
                            >Explore features</Button
                        >
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="border-b border-border py-12">
            <div class="container mx-auto px-4">
                <div class="mx-auto max-w-4xl">
                    <div class="mx-auto grid gap-6 sm:grid-cols-2">
                        <Card
                            v-for="stat in stats"
                            :key="stat.label"
                            class="border-muted text-center"
                        >
                            <CardHeader class="pb-2">
                                <CardDescription
                                    class="text-xs tracking-wide uppercase"
                                >
                                    {{ stat.label }}
                                </CardDescription>
                                <CardTitle class="text-2xl">{{
                                    stat.value
                                }}</CardTitle>
                                <p class="text-xs text-muted-foreground">
                                    {{ stat.detail }}
                                </p>
                            </CardHeader>
                        </Card>
                    </div>

                    <Card
                        class="mt-8 border-primary/10 shadow-lg shadow-primary/5"
                    >
                        <CardHeader class="text-center">
                            <CardTitle class="text-xl"
                                >What you can do today</CardTitle
                            >
                            <CardDescription>
                                No manuals needed—just clear steps from setup to
                                sale.
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="grid gap-6 sm:grid-cols-2">
                            <div class="flex items-start gap-3">
                                <div
                                    class="mt-0.5 flex-shrink-0 rounded-full bg-primary/10 p-2"
                                >
                                    <Users class="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p class="font-medium">Invite your team</p>
                                    <p class="text-sm text-muted-foreground">
                                        Add admins and cashiers. Everyone sees
                                        the tools they need.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div
                                    class="mt-0.5 flex-shrink-0 rounded-full bg-primary/10 p-2"
                                >
                                    <Package class="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p class="font-medium">
                                        Create products fast
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        Names, SKUs, prices, stock, and
                                        images—kept consistent.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div
                                    class="mt-0.5 flex-shrink-0 rounded-full bg-primary/10 p-2"
                                >
                                    <ShoppingCart
                                        class="h-4 w-4 text-primary"
                                    />
                                </div>
                                <div>
                                    <p class="font-medium">Start selling</p>
                                    <p class="text-sm text-muted-foreground">
                                        Cashiers add items to the cart and
                                        checkout in seconds.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div
                                    class="mt-0.5 flex-shrink-0 rounded-full bg-primary/10 p-2"
                                >
                                    <Bot class="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p class="font-medium">Chat with AI</p>
                                    <p class="text-sm text-muted-foreground">
                                        Ask questions about products, sales, and
                                        inventory anytime.
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Modules: simple and benefit-focused -->
        <section id="modules" class="container mx-auto px-4 py-16">
            <div class="mx-auto max-w-5xl space-y-10">
                <div class="space-y-3 text-center">
                    <h2 class="text-3xl font-bold tracking-tight">
                        All the essentials built in
                    </h2>
                    <p class="text-muted-foreground">
                        Five areas that make day‑to‑day work easier.
                    </p>
                </div>
                <div class="mx-auto grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Card
                        v-for="feature in features"
                        :key="feature.title"
                        class="h-full"
                    >
                        <CardHeader>
                            <component
                                :is="feature.icon"
                                class="mb-3 h-10 w-10 text-primary"
                            />
                            <CardTitle class="text-xl">{{
                                feature.title
                            }}</CardTitle>
                            <CardDescription>{{
                                feature.description
                            }}</CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Flow: explain the journey clearly -->
        <section class="bg-muted/40 py-16">
            <div class="container mx-auto px-4">
                <div class="mx-auto max-w-6xl">
                    <div class="space-y-8 text-center">
                        <h2 class="text-3xl font-bold tracking-tight">
                            A simple path from setup to sale
                        </h2>
                        <p class="text-muted-foreground">
                            No complex steps—just the right tools at the right
                            time.
                        </p>
                    </div>
                    <div class="mt-10 grid gap-4 md:grid-cols-3">
                        <Card
                            v-for="step in flow"
                            :key="step.title"
                            class="h-full"
                        >
                            <CardHeader>
                                <CardTitle class="text-base">{{
                                    step.title
                                }}</CardTitle>
                                <CardDescription>{{
                                    step.detail
                                }}</CardDescription>
                            </CardHeader>
                        </Card>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action: warm and inviting -->
        <section class="container mx-auto px-4 py-16">
            <div class="mx-auto max-w-5xl">
                <Card class="overflow-hidden border-primary/20">
                    <CardContent
                        class="grid gap-8 p-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center"
                    >
                        <div class="space-y-4">
                            <Badge variant="outline">Ready to try</Badge>
                            <h3 class="text-2xl font-bold">
                                Start with what matters—we'll keep it simple
                            </h3>
                            <p class="text-muted-foreground">
                                Log in, add products, invite your team, and
                                begin selling. The rest—stock and reports—stays
                                tidy for you.
                            </p>
                            <div class="flex flex-wrap gap-3">
                                <Link
                                    v-if="!$page.props.auth.user"
                                    :href="login()"
                                >
                                    <Button size="lg">Log in</Button>
                                </Link>
                                <Link v-else :href="dashboard()">
                                    <Button size="lg">Open dashboard</Button>
                                </Link>
                            </div>
                        </div>
                        <div class="grid gap-3 text-sm text-muted-foreground">
                            <div class="flex items-start gap-3">
                                <CheckCircle2
                                    class="mt-0.5 h-4 w-4 text-primary"
                                />
                                <div>
                                    <p class="font-medium text-foreground">
                                        Cashiers sell faster
                                    </p>
                                    <p>
                                        Quick product picks, easy cart updates,
                                        and checkout in seconds.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <CheckCircle2
                                    class="mt-0.5 h-4 w-4 text-primary"
                                />
                                <div>
                                    <p class="font-medium text-foreground">
                                        Stock stays honest
                                    </p>
                                    <p>
                                        Updates are recorded and reports keep
                                        the story clear.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <CheckCircle2
                                    class="mt-0.5 h-4 w-4 text-primary"
                                />
                                <div>
                                    <p class="font-medium text-foreground">
                                        You stay in control
                                    </p>
                                    <p>
                                        Roles keep the right doors open for
                                        admins and super admins.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-border py-8">
            <div
                class="container mx-auto px-4 text-center text-sm text-muted-foreground"
            >
                <p>© {{ new Date().getFullYear() }} POS Management System.</p>
            </div>
        </footer>
    </div>
</template>
