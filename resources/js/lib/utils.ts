// resources/js/utils.ts
import type { Updater } from '@tanstack/vue-table';
import type { Ref } from 'vue';

// Junta clases ignorando falsy
export function cn(...classes: Array<string | false | null | undefined>) {
    return classes.filter(Boolean).join(' ');
}

// Helper para estados de TanStack Table con refs de Vue
export function valueUpdater<T>(updaterOrValue: T | Updater<T>, target: Ref<T>) {
    if (typeof updaterOrValue === 'function') {
        // @ts-expect-error: TanStack usa una firma Updater genérica
        target.value = updaterOrValue(target.value);
    } else {
        target.value = updaterOrValue;
    }
}
