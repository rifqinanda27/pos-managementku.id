<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { History, LayoutGrid, Package, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

// Main navigation items based on user role
const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    // Dashboard - for super-admin and admin
    if (user.value?.role === 'super-admin' || user.value?.role === 'admin') {
        items.push({
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        });

        // User Management - for super-admin and admin
        items.push({
            title: 'User Management',
            href: '/user-management',
            icon: Users,
        });

        // Product Management - for super-admin and admin
        items.push({
            title: 'Product Management',
            href: '/product-management',
            icon: Package,
        });

        // Stock Management - for super-admin and admin
        items.push({
            title: 'Stock Management',
            href: '/stock-management',
            icon: History,
        });
    }

    // POS - for cashier
    if (user.value?.role === 'cashier') {
        items.push({
            title: 'POS Terminal',
            href: '/pos',
            icon: LayoutGrid,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
