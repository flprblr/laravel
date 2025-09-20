import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function usePermissions() {
    const page = usePage();

    const user = computed(() => page.props.auth.user);
    const permissions = computed(() => user.value?.permissions || []);
    const roles = computed(() => user.value?.roles || []);

    /**
     * Verifica si el usuario tiene un permiso específico
     */
    const can = (permission: string | string[]): boolean => {
        if (!user.value) return false;

        const userPermissions = permissions.value;

        if (Array.isArray(permission)) {
            return permission.some((p) => userPermissions.includes(p));
        }

        return userPermissions.includes(permission);
    };

    /**
     * Verifica si el usuario tiene todos los permisos especificados
     */
    const canAll = (permissionsToCheck: string[]): boolean => {
        if (!user.value) return false;

        const userPermissions = permissions.value;
        return permissionsToCheck.every((p) => userPermissions.includes(p));
    };

    /**
     * Verifica si el usuario tiene uno de los roles especificados
     */
    const is = (role: string | string[]): boolean => {
        if (!user.value) return false;

        const userRoles = roles.value;

        if (Array.isArray(role)) {
            return role.some((r) => userRoles.includes(r));
        }

        return userRoles.includes(role);
    };

    /**
     * Verifica si el usuario tiene todos los roles especificados
     */
    const isAll = (rolesToCheck: string[]): boolean => {
        if (!user.value) return false;

        const userRoles = roles.value;
        return rolesToCheck.every((r) => userRoles.includes(r));
    };

    /**
     * Verifica si el usuario tiene al menos uno de los permisos o roles especificados
     */
    const canOrIs = (permissions: string[], roles: string[]): boolean => {
        return can(permissions) || is(roles);
    };

    return {
        user,
        permissions,
        roles,
        can,
        canAll,
        is,
        isAll,
        canOrIs,
    };
}
