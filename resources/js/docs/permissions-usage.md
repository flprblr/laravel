# Sistema de Permisos en Frontend

Este documento explica cómo usar el sistema de permisos de Laravel en el frontend Vue/Inertia.

## Configuración

El sistema está configurado para compartir automáticamente los permisos y roles del usuario autenticado a través de Inertia.

## Opciones de Uso

### 1. Composable usePermissions (Recomendado)

```vue
<script setup>
import { usePermissions } from '@/composables/usePermissions';

const { can, canAll, is, isAll, canOrIs } = usePermissions();

// Verificar un permiso específico
if (can('users.index')) {
    // Usuario tiene el permiso users.index
}

// Verificar múltiples permisos (al menos uno)
if (can(['users.index', 'users.create'])) {
    // Usuario tiene al menos uno de los permisos
}

// Verificar todos los permisos
if (canAll(['users.index', 'users.create'])) {
    // Usuario tiene todos los permisos
}

// Verificar un rol específico
if (is('Administrator')) {
    // Usuario tiene el rol Administrator
}

// Verificar múltiples roles (al menos uno)
if (is(['Administrator', 'Editor'])) {
    // Usuario tiene al menos uno de los roles
}

// Verificar todos los roles
if (isAll(['Administrator', 'SuperAdmin'])) {
    // Usuario tiene todos los roles
}

// Verificar permisos O roles
if (canOrIs(['users.index'], ['Administrator'])) {
    // Usuario tiene el permiso users.index O el rol Administrator
}
</script>
```

### 2. Directivas en Templates

#### v-can

```vue
<template>
    <!-- Mostrar solo si tiene el permiso users.index -->
    <div v-can="'users.index'">
        <h2>Lista de Usuarios</h2>
    </div>

    <!-- Mostrar si tiene al menos uno de los permisos -->
    <div v-can="['users.index', 'users.create']">
        <h2>Gestión de Usuarios</h2>
    </div>
</template>
```

#### v-role

```vue
<template>
    <!-- Mostrar solo si es Administrator -->
    <div v-role="'Administrator'">
        <h2>Panel de Administración</h2>
    </div>

    <!-- Mostrar si tiene al menos uno de los roles -->
    <div v-role="['Administrator', 'Editor']">
        <h2>Panel de Edición</h2>
    </div>
</template>
```

#### v-can-or-role

```vue
<template>
    <!-- Mostrar si tiene el permiso O el rol -->
    <div v-can-or-role="{ permissions: ['users.index'], roles: ['Administrator'] }">
        <h2>Acceso Especial</h2>
    </div>
</template>
```

### 3. Uso en Componentes Reactivos

```vue
<script setup>
import { usePermissions } from '@/composables/usePermissions';

const { can, is } = usePermissions();

// Usar en computed properties
const canManageUsers = computed(() => can('users.index'));
const isAdmin = computed(() => is('Administrator'));

// Usar en métodos
const handleUserAction = () => {
    if (!can('users.edit')) {
        alert('No tienes permisos para editar usuarios');
        return;
    }
    // Lógica de edición
};
</script>

<template>
    <div>
        <button v-if="canManageUsers" @click="handleUserAction">Editar Usuario</button>

        <div v-if="isAdmin" class="admin-panel">Panel de Administración</div>
    </div>
</template>
```

### 4. Uso en Navegación

```vue
<script setup>
import { usePermissions } from '@/composables/usePermissions';

const { can, is } = usePermissions();

const navItems = computed(() => {
    const items = [{ title: 'Dashboard', href: '/dashboard' }];

    // Agregar items basados en permisos
    if (can('users.index')) {
        items.push({ title: 'Usuarios', href: '/users' });
    }

    if (is('Administrator')) {
        items.push({ title: 'Configuración', href: '/settings' });
    }

    return items;
});
</script>
```

## Permisos Disponibles

Basado en tu configuración actual, estos son los permisos disponibles:

- `users.index` - Ver lista de usuarios
- `users.show` - Ver detalles de usuario
- `users.create` - Crear usuario
- `users.edit` - Editar usuario
- `users.destroy` - Eliminar usuario
- `users.import` - Importar usuarios
- `users.export` - Exportar usuarios
- `roles.index` - Ver lista de roles
- `roles.show` - Ver detalles de rol
- `roles.create` - Crear rol
- `roles.edit` - Editar rol
- `roles.destroy` - Eliminar rol
- `roles.import` - Importar roles
- `roles.export` - Exportar roles
- `permissions.index` - Ver lista de permisos
- `permissions.show` - Ver detalles de permiso
- `permissions.create` - Crear permiso
- `permissions.edit` - Editar permiso
- `permissions.destroy` - Eliminar permiso
- `permissions.import` - Importar permisos
- `permissions.export` - Exportar permisos
- `api.sanctum` - Acceso API Sanctum
- `api.passport` - Acceso API Passport

## Roles Disponibles

- `Administrator` - Administrador con todos los permisos

## Notas Importantes

1. **Seguridad**: Los permisos en el frontend son solo para UX. La seguridad real debe implementarse en el backend.

2. **Rendimiento**: Los permisos se cargan una vez por sesión y se comparten a través de Inertia.

3. **Reactividad**: Los composables son reactivos y se actualizan automáticamente cuando cambia el usuario.

4. **Tipos**: Los tipos TypeScript están configurados para incluir `permissions` y `roles` en la interfaz `User`.
