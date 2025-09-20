<script setup lang="ts">
import { useFlashWatcher } from '@/composables/useFlashWatcher';
import AppLayout from '@/layouts/AppLayout.vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Head, useForm } from '@inertiajs/vue3';

import Button from '@/components/ui/button/Button.vue';
import Label from '@/components/ui/label/Label.vue';

const form = useForm({ orders: '' });

useFlashWatcher();

function submit() {
    form.post(route('superga.update.orders.status'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => form.reset(),
        onError: () => console.error(form.errors),
    });
}

// Evita submit con Enter fuera de <textarea>
function onKeydown(e: KeyboardEvent) {
    const target = e.target as HTMLElement | null;
    if (e.key === 'Enter' && target && target.tagName !== 'TEXTAREA') {
        e.preventDefault();
    }
}
</script>

<template>
    <AppLayout>
        <Head title="Cambio de Estado superga.cl" />

        <div class="space-y-6 p-6">
            <HeadingSmall
                title="Cambio de Estado superga.cl"
                description="Ingresa el/los números de pedido (uno por línea) y presiona el botón Actualizar Pedidos."
            />
            <form @submit.prevent="submit" @keydown="onKeydown" class="max-w-xs space-y-3">
                <div class="space-y-3">
                    <Label for="orderNumber">Número(s) de Pedido</Label>

                    <textarea
                        id="orderNumber"
                        v-model="form.orders"
                        rows="25"
                        class="block w-full rounded-md border border-gray-300 p-2 text-sm focus:ring-2 focus:ring-primary focus:outline-none"
                    ></textarea>

                    <p v-if="form.errors.orders" class="text-sm text-red-500">
                        {{ form.errors.orders }}
                    </p>
                </div>

                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Actualizando...' : 'Actualizar Pedidos' }}
                </Button>
            </form>
        </div>
    </AppLayout>
</template>
