import { usePermissions } from '@/composables/usePermissions';
import type { App, DirectiveBinding } from 'vue';

/**
 * Directiva v-can para verificar permisos
 * Uso: v-can="'users.index'" o v-can="['users.index', 'users.create']"
 */
export const vCan = {
    mounted(el: HTMLElement, binding: DirectiveBinding) {
        const { can } = usePermissions();

        if (!can(binding.value)) {
            el.style.display = 'none';
        }
    },

    updated(el: HTMLElement, binding: DirectiveBinding) {
        const { can } = usePermissions();

        if (can(binding.value)) {
            el.style.display = '';
        } else {
            el.style.display = 'none';
        }
    },
};

/**
 * Directiva v-role para verificar roles
 * Uso: v-role="'Administrator'" o v-role="['Administrator', 'Editor']"
 */
export const vRole = {
    mounted(el: HTMLElement, binding: DirectiveBinding) {
        const { is } = usePermissions();

        if (!is(binding.value)) {
            el.style.display = 'none';
        }
    },

    updated(el: HTMLElement, binding: DirectiveBinding) {
        const { is } = usePermissions();

        if (is(binding.value)) {
            el.style.display = '';
        } else {
            el.style.display = 'none';
        }
    },
};

/**
 * Directiva v-can-or-role para verificar permisos o roles
 * Uso: v-can-or-role="{ permissions: ['users.index'], roles: ['Administrator'] }"
 */
export const vCanOrRole = {
    mounted(el: HTMLElement, binding: DirectiveBinding) {
        const { canOrIs } = usePermissions();
        const { permissions = [], roles = [] } = binding.value || {};

        if (!canOrIs(permissions, roles)) {
            el.style.display = 'none';
        }
    },

    updated(el: HTMLElement, binding: DirectiveBinding) {
        const { canOrIs } = usePermissions();
        const { permissions = [], roles = [] } = binding.value || {};

        if (canOrIs(permissions, roles)) {
            el.style.display = '';
        } else {
            el.style.display = 'none';
        }
    },
};

export function registerPermissionDirectives(app: App) {
    app.directive('can', vCan);
    app.directive('role', vRole);
    app.directive('can-or-role', vCanOrRole);
}
