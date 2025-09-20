<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Head, router } from '@inertiajs/vue3';

import { Pagination, PaginationContent, PaginationEllipsis, PaginationItem, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const props = defineProps<{
    pendingOrders: TablePaginator;
}>();

const onPageChange = (p: number) => {
    if (p === (props.pendingOrders?.current_page ?? 1)) return;
    router.get(
        route('pending.orders'),
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
    <AppLayout>
        <Head title="Pedidos Pendientes" />

        <div class="space-y-6 p-6">
            <HeadingSmall title="Pedidos Pendientes" description="Lista de pedidos pendientes de actualización." />

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Tienda</TableHead>
                        <TableHead>Número Pedido</TableHead>
                        <TableHead>Estado</TableHead>
                        <TableHead>Intentos</TableHead>
                        <TableHead>Último Error</TableHead>
                        <TableHead>Procesado</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="p in props.pendingOrders.data" v-bind:key="p.id">
                        <TableCell>{{ p.ecommerce }}</TableCell>
                        <TableCell>{{ p.order_number }}</TableCell>
                        <TableCell>{{ p.status }}</TableCell>
                        <TableCell>{{ p.attempts }}</TableCell>
                        <TableCell>{{ p.last_error }}</TableCell>
                        <TableCell>{{ p.processed_at }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <Pagination
                v-slot="{ page }"
                :items-per-page="props.pendingOrders.per_page || 10"
                :total="props.pendingOrders.total || 0"
                :default-page="props.pendingOrders.current_page || 1"
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
