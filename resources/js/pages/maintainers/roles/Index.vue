<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';

import { type BreadcrumbItem, type RowAction, type TableColumn } from '@/types';
import { Eye, SquarePen, Trash2 } from 'lucide-vue-next';

import HeaderTable from '@/components/HeaderTable.vue';
import SimpleTable from '@/components/SimpleTable.vue';

const props = defineProps<{
    roles: TablePaginator;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'List Roles',
        description: 'List roles in the table below.',
        href: '/maintainers/roles',
    },
];

const ROUTE_BASE = 'maintainers.roles' as const;

const columns: TableColumn[] = [
    { label: 'ID', field: 'id' },
    { label: 'Name', field: 'name' },
    { label: 'Created At', field: 'created_at' },
    { label: 'Updated At', field: 'updated_at' },
];

const headerActions = ['create', 'export', 'import'] as const;

const rowActions: RowAction[] = [
    { key: 'show', label: 'Show', icon: Eye, can: 'roles.show', type: 'route', route: 'maintainers.roles.show', paramFrom: 'id' },
    { key: 'edit', label: 'Edit', icon: SquarePen, can: 'roles.edit', type: 'route', route: 'maintainers.roles.edit', paramFrom: 'id' },
    {
        key: 'delete',
        label: 'Delete',
        icon: Trash2,
        can: 'roles.destroy',
        type: 'emit',
        confirm: { title: '¿Estás seguro?', description: 'Esto eliminará permanentemente el rol seleccionado.' },
    },
];

const onPageChange = (p: number) => {
    if (p === (props.roles?.current_page ?? 1)) return;
    router.get(
        route('maintainers.roles.index'),
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
            <HeaderTable :title="breadcrumbs[0].title" :description="breadcrumbs[0].description" :actions="headerActions" resource="roles" />
            <SimpleTable
                :columns="columns"
                :items="props.roles.data"
                :items-per-page="props.roles.per_page || 10"
                :total="props.roles.total || 0"
                :current-page="props.roles.current_page || 1"
                row-key="id"
                actions-label="Action"
                :row-actions="rowActions"
                @update:page="onPageChange"
                @row:action="onRowAction"
            />
        </div>
    </AppLayout>
</template>
