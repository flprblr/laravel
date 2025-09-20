<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import AppLayout from '@/layouts/AppLayout.vue';

import { type BreadcrumbItem } from '@/types';

import HeadingSmall from '@/components/HeadingSmall.vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Pagination, PaginationContent, PaginationEllipsis, PaginationItem, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

import { Download, Eye, Plus, SquarePen, Trash2, Upload } from 'lucide-vue-next';

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

const form = useForm({});
const isDeleteDialogOpen = ref(false);
const itemToDelete = ref<number | string | null>(null);

const openDeleteDialog = (id: number | string) => {
    itemToDelete.value = id;
    isDeleteDialogOpen.value = true;
};

const destroyItem = () => {
    if (itemToDelete.value) {
        form.delete(route('maintainers.roles.destroy', itemToDelete.value), {
            preserveScroll: true,
            onError: (errors) => console.log('Error deleting:', errors),
            onSuccess: () => {
                isDeleteDialogOpen.value = false;
                itemToDelete.value = null;
            },
        });
    }
};

const downloadExport = (type: string) => {
    const link = document.createElement('a');
    link.href = route(`maintainers.${type}.export`);
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

const cancelDelete = () => {
    isDeleteDialogOpen.value = false;
    itemToDelete.value = null;
};

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
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="breadcrumbs[0].title" />

        <div class="p-4 space-y-3">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <HeadingSmall :title="breadcrumbs[0].title" :description="breadcrumbs[0].description" />
                <div class="flex gap-2">
                    <Button 
                        variant="outline" 
                        class="cursor-pointer"
                        v-can="'roles.create'"
                        @click="router.visit(route('maintainers.roles.create'))"
                    >
                        <Plus class="mr-2 h-4 w-4" />
                        Create
                    </Button>
                    <Button 
                        variant="outline" 
                        class="cursor-pointer"
                        v-can="'roles.export'"
                        @click="downloadExport('roles')"
                    >
                        <Download class="mr-2 h-4 w-4" />
                        Export
                    </Button>
                    <Button 
                        variant="outline" 
                        class="cursor-pointer"
                        v-can="'roles.import'"
                        @click="router.visit(route('maintainers.roles.import.form'))"
                    >
                        <Upload class="mr-2 h-4 w-4" />
                        Import
                    </Button>
                </div>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Created At</TableHead>
                        <TableHead>Updated At</TableHead>
                        <TableHead v-role="'Administrator'" class="text-right">Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="r in props.roles.data" v-bind:key="r.id">
                        <TableCell>{{ r.id }}</TableCell>
                        <TableCell>{{ r.name }}</TableCell>
                        <TableCell>{{ r.created_at }}</TableCell>
                        <TableCell>{{ r.updated_at }}</TableCell>
                        <TableCell v-role="'Administrator'" class="text-right">
                            <div class="flex items-center justify-end gap-3">
                                <Button 
                                    variant="outline" 
                                    size="sm" 
                                    class="cursor-pointer"
                                    v-can="'roles.show'"
                                    @click="router.visit(route('maintainers.roles.show', r.id))"
                                >
                                    <Eye class="mr-1 h-4 w-4" />
                                    Show
                                </Button>
                                <Button 
                                    variant="outline" 
                                    size="sm" 
                                    class="cursor-pointer"
                                    v-can="'roles.edit'"
                                    @click="router.visit(route('maintainers.roles.edit', r.id))"
                                >
                                    <SquarePen class="mr-1 h-4 w-4" />
                                    Edit
                                </Button>
                                <AlertDialog v-model:open="isDeleteDialogOpen" v-can="'roles.destroy'">
                                    <AlertDialogTrigger as-child>
                                        <Button 
                                            variant="outline" 
                                            size="sm" 
                                            class="cursor-pointer"
                                            @click="openDeleteDialog(r.id)"
                                        >
                                            <Trash2 class="mr-1 h-4 w-4" />
                                            Delete
                                        </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>¿Estás seguro?</AlertDialogTitle>
                                            <AlertDialogDescription>
                                                Esta acción no se puede deshacer. Esto eliminará permanentemente el rol seleccionado.
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel @click="cancelDelete" class="cursor-pointer">Cancelar</AlertDialogCancel>
                                            <AlertDialogAction @click="destroyItem" class="cursor-pointer">Eliminar</AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <Pagination
                v-slot="{ page }"
                :items-per-page="props.roles.per_page || 10"
                :total="props.roles.total || 0"
                :default-page="props.roles.current_page || 1"
                @update:page="onPageChange"
            >
                <PaginationContent v-slot="{ items }">
                    <PaginationPrevious />

                    <template v-for="(item, index) in items" :key="index">
                        <PaginationItem v-if="item.type === 'page'" :value="item.value" :is-active="item.value === page">
                            {{ item.value }}
                        </PaginationItem>
                        <PaginationEllipsis v-else-if="item.type === 'ellipsis'" :index="index" />
                    </template>

                    <PaginationNext />
                </PaginationContent>
            </Pagination>
        </div>
    </AppLayout>
</template>
