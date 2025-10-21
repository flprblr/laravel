<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { usePermissions } from '@/composables/usePermissions';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Link2, UserRound, UserRoundCheck, UserRoundCog } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const { can, is } = usePermissions();

// Ejemplo usando el composable usePermissions
const mainNavItems: NavItem[] = [
    {
        title: 'Link',
        href: '/dashboard',
        icon: Link2,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Users',
        href: '/maintainers/users',
        icon: UserRound,
    },
    {
        title: 'Roles',
        href: '/maintainers/roles',
        icon: UserRoundCog,
    },
    {
        title: 'Permissions',
        href: '/maintainers/permissions',
        icon: UserRoundCheck,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
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
            <NavFooter :items="footerNavItems" v-role="'Administrator'" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
