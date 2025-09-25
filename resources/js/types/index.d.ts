import type { LucideIcon } from 'lucide-vue-next';
import type { Component } from 'vue';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User | null;
}

export interface BreadcrumbItem {
    title: string;
    description?: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
    items?: NavItem[];
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    dni: string;
    phone: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    permissions: string[];
    roles: string[];
}

export type BreadcrumbItemType = BreadcrumbItem;

// Generic table types
export type TableColumn<Row = Record<string, unknown>> = {
    label: string;
    field: keyof Row | string;
    class?: string;
    formatter?: (value: unknown, row: Row) => unknown;
};

export type RowAction<Row = Record<string, unknown>> = {
    key: 'show' | 'edit' | 'delete' | string;
    label: string;
    can?: string;
    type: 'route' | 'emit';
    route?: string;
    paramFrom?: keyof Row | string;
    icon?: Component;
    confirm?: { title: string; description: string };
    method?: 'get' | 'post' | 'put' | 'patch' | 'delete';
};
