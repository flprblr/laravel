<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';

import { type BreadcrumbItem, type RowAction, type TableColumn } from '@/types';
import { Eye, SquarePen, Trash2 } from 'lucide-vue-next';

import HeaderTable from '@/components/HeaderTable.vue';
import SimpleTable from '@/components/SimpleTable.vue';

const props = defineProps<{
    users: TablePaginator;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'List Users',
        description: 'List users in the table below.',
        href: '/maintainers/users',
    },
];

const ROUTE_BASE = 'maintainers.users' as const;

const columns: TableColumn[] = [
    { label: 'ID', field: 'id' },
    { label: 'Name', field: 'name' },
    { label: 'Email', field: 'email' },
    { label: 'Created At', field: 'created_at' },
    { label: 'Updated At', field: 'updated_at' },
];

const headerActions = ['create', 'export', 'import'] as const;

const rowActions: RowAction[] = [
    { key: 'show', label: 'Show', icon: Eye, can: 'users.show', type: 'route', route: 'maintainers.users.show', paramFrom: 'id' },
    { key: 'edit', label: 'Edit', icon: SquarePen, can: 'users.edit', type: 'route', route: 'maintainers.users.edit', paramFrom: 'id' },
    {
        key: 'delete',
        label: 'Delete',
        icon: Trash2,
        can: 'users.destroy',
        type: 'emit',
        confirm: { title: '¿Estás seguro?', description: 'Esto eliminará permanentemente el usuario seleccionado.' },
    },
];

const onPageChange = (p: number) => {
    if (p === (props.users?.current_page ?? 1)) return;
    router.get(
        route('maintainers.users.index'),
        { page: p },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const onRowAction = ({ key, id }: { key: string; id: string | number }) => {
    if (key === 'delete') {
        router.visit(route(`${ROUTE_BASE}.destroy`, id), { method: 'delete', preserveScroll: true });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="breadcrumbs[0].title" />
        <div class="space-y-3 p-4">
            <HeaderTable :title="breadcrumbs[0].title" :description="breadcrumbs[0].description" :actions="headerActions" resource="users" />
            <SimpleTable
                :columns="columns"
                :items="props.users.data"
                :items-per-page="props.users.per_page || 10"
                :total="props.users.total || 0"
                :current-page="props.users.current_page || 1"
                row-key="id"
                actions-label="Action"
                :row-actions="rowActions"
                @update:page="onPageChange"
                @row:action="onRowAction"
            />
        </div>
    </AppLayout>
</template>
