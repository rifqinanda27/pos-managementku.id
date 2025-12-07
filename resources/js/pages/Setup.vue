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
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    CheckCircle2,
    Database,
    FolderGit2,
    Rocket,
    Settings,
    Terminal,
} from 'lucide-vue-next';

const steps = [
    {
        icon: FolderGit2,
        title: 'Clone the repository',
        description: 'Get the code on your machine',
        commands: ['git clone <repository-url>', 'cd pos-managementku.id'],
    },
    {
        icon: Terminal,
        title: 'Install dependencies',
        description: 'Backend and frontend packages',
        commands: ['composer install', 'npm install'],
    },
    {
        icon: Settings,
        title: 'Configure environment',
        description: 'Set up your database and keys',
        commands: ['cp .env.example .env', 'php artisan key:generate'],
        note: 'Edit .env with your database credentials (DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD) and Gemini API key (GEMINI_API_KEY) if using the chatbot.',
    },
    {
        icon: Database,
        title: 'Set up database',
        description: 'Run migrations and link storage',
        commands: ['php artisan migrate', 'php artisan storage:link'],
    },
    {
        icon: Rocket,
        title: 'Create super admin',
        description: 'Your first user account',
        commands: ['php artisan create:super-admin'],
        note: 'Follow the prompts to enter name, username, and password.',
    },
    {
        icon: Rocket,
        title: 'Build and run',
        description: 'Compile assets and start the server',
        commands: ['npm run dev', 'php artisan serve'],
        note: 'Open http://localhost:8000 in your browser.',
    },
];

const prerequisites = [
    { name: 'PHP', version: '8.4 or higher' },
    { name: 'Composer', version: 'Latest' },
    { name: 'Node.js', version: '22+' },
    { name: 'npm', version: 'Latest' },
    { name: 'Database', version: 'PostgreSQL' },
];
</script>

<template>
    <Head title="Setup Guide" />
    <div class="min-h-screen bg-background">
        <!-- Header -->
        <header
            class="sticky top-0 z-50 border-b border-border bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60"
        >
            <div
                class="container mx-auto flex h-16 items-center justify-between px-4"
            >
                <Link
                    href="/"
                    class="flex items-center gap-2 text-muted-foreground hover:text-foreground"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                    <span class="text-sm font-medium">Back to home</span>
                </Link>
            </div>
        </header>

        <!-- Hero -->
        <section
            class="border-b border-border bg-gradient-to-b from-primary/5 via-background to-background py-16"
        >
            <div class="container mx-auto px-4 text-center">
                <Badge variant="outline" class="mb-4">Setup Guide</Badge>
                <h1 class="mb-4 text-4xl font-bold sm:text-5xl">
                    Get started in minutes
                </h1>
                <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                    Follow this step-by-step guide to install and run the POS
                    Management System on your local machine.
                </p>
            </div>
        </section>

        <!-- Prerequisites -->
        <section class="container mx-auto px-4 py-12">
            <div class="mx-auto max-w-4xl">
                <h2 class="mb-6 text-2xl font-bold">Before you begin</h2>
                <Card>
                    <CardHeader>
                        <CardTitle>Prerequisites</CardTitle>
                        <CardDescription>
                            Make sure you have these installed on your system
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <div
                                v-for="prereq in prerequisites"
                                :key="prereq.name"
                                class="flex items-start gap-3 rounded-lg border p-3"
                            >
                                <CheckCircle2
                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-primary"
                                />
                                <div>
                                    <p class="font-medium">{{ prereq.name }}</p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ prereq.version }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </section>

        <!-- Steps -->
        <section class="container mx-auto px-4 py-12">
            <div class="mx-auto max-w-4xl space-y-6">
                <h2 class="mb-6 text-2xl font-bold">Installation steps</h2>
                <div
                    v-for="(step, index) in steps"
                    :key="step.title"
                    class="relative"
                >
                    <!-- Connector line -->
                    <div
                        v-if="index < steps.length - 1"
                        class="absolute top-14 left-6 h-full w-0.5 bg-border"
                    ></div>

                    <Card class="relative">
                        <CardHeader>
                            <div class="flex items-start gap-4">
                                <div
                                    class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary/10"
                                >
                                    <component
                                        :is="step.icon"
                                        class="h-6 w-6 text-primary"
                                    />
                                </div>
                                <div class="flex-1">
                                    <div class="mb-1 flex items-center gap-2">
                                        <Badge
                                            variant="secondary"
                                            class="text-xs"
                                        >
                                            Step {{ index + 1 }}
                                        </Badge>
                                    </div>
                                    <CardTitle class="text-xl">
                                        {{ step.title }}
                                    </CardTitle>
                                    <CardDescription>
                                        {{ step.description }}
                                    </CardDescription>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div
                                v-if="step.commands"
                                class="space-y-2 rounded-lg bg-muted p-4"
                            >
                                <code
                                    v-for="(cmd, cmdIndex) in step.commands"
                                    :key="cmdIndex"
                                    class="block font-mono text-sm text-foreground"
                                >
                                    $ {{ cmd }}
                                </code>
                            </div>
                            <div
                                v-if="step.note"
                                class="mt-4 rounded-lg bg-primary/5 p-4 text-sm text-muted-foreground"
                            >
                                <p class="mb-1 font-semibold text-foreground">
                                    💡 Note:
                                </p>
                                <p>{{ step.note }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- What's next -->
        <section class="bg-muted/40 py-16">
            <div class="container mx-auto px-4">
                <div class="mx-auto max-w-4xl">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-2xl">What's next?</CardTitle>
                            <CardDescription>
                                Once your server is running, here's what you can
                                do
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="flex items-start gap-3">
                                <CheckCircle2
                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-primary"
                                />
                                <div>
                                    <p class="font-medium">
                                        Log in as Super Admin
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        Use the credentials you created to
                                        access the dashboard at
                                        <code
                                            class="rounded bg-muted px-1 py-0.5"
                                            >/login</code
                                        >
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <CheckCircle2
                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-primary"
                                />
                                <div>
                                    <p class="font-medium">Add products</p>
                                    <p class="text-sm text-muted-foreground">
                                        Navigate to Product Management to create
                                        your catalog with names, SKUs, prices,
                                        and stock
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <CheckCircle2
                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-primary"
                                />
                                <div>
                                    <p class="font-medium">Invite your team</p>
                                    <p class="text-sm text-muted-foreground">
                                        Create Admin and Cashier accounts in
                                        User Management
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <CheckCircle2
                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-primary"
                                />
                                <div>
                                    <p class="font-medium">Start selling</p>
                                    <p class="text-sm text-muted-foreground">
                                        Cashiers can log in and use the POS
                                        Terminal to add items to the cart and
                                        checkout
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <CheckCircle2
                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-primary"
                                />
                                <div>
                                    <p class="font-medium">
                                        Try the AI Chatbot (optional)
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        Add your Gemini API key to
                                        <code
                                            class="rounded bg-muted px-1 py-0.5"
                                            >.env</code
                                        >
                                        (GEMINI_API_KEY) to enable the chatbot
                                        assistant
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link href="/">
                            <Button size="lg">Back to home</Button>
                        </Link>
                        <Button
                            variant="outline"
                            size="lg"
                            as="a"
                            href="https://github.com/Akmal-Keisin/pos-managementku.id"
                            target="_blank"
                        >
                            View on GitHub
                        </Button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-border py-8">
            <div
                class="container mx-auto px-4 text-center text-sm text-muted-foreground"
            >
                <p>
                    Need help? Check the README.md in the project root or open
                    an issue on GitHub.
                </p>
            </div>
        </footer>
    </div>
</template>
