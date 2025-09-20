<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    variant?: 'default' | 'wide';
}>();

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: '/settings/profile',
    },
    {
        title: 'Password',
        href: '/settings/password',
    },
    {
        title: 'Appearance',
        href: '/settings/appearance',
    },
    // {
    //     title: 'Users',
    //     href: '/maintainers/users',
    // },
    // {
    //     title: 'Roles',
    //     href: '/maintainers/roles',
    // },
    // {
    //     title: 'Permissions',
    //     href: '/maintainers/permissions',
    // },
    // {
    //     title: 'API Tokens: Bearer',
    //     href: '/maintainers/sanctum',
    // },
    // {
    //     title: 'API Tokens: JWT',
    //     href: '/maintainers/passport',
    // },
];

const page = usePage();

const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Settings" description="Manage your profile and account settings" />

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-y-0 lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        variant="ghost"
                        :class="['w-full justify-start', { 'bg-muted': currentPath === item.href }]"
                        as-child
                    >
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 md:hidden" />

            <div :class="['flex-1', variant === 'wide' ? 'w-full' : 'md:max-w-2xl']">
                <section :class="[variant === 'wide' ? 'w-full' : 'max-w-xl', 'space-y-12']">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
