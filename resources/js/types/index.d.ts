import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Weather {
    data: Data;
}
export interface Data {
    id: number;
    name: string;
    country: string;
    temperature: number;
    condition: string;
    humidity: number;
    wind_speed: number;
    visibility: number;
    feels_like: number;
    icon: string;
    created_at: string;
    updated_at: string;
    forecast?: (ForecastEntity)[] | null;
}
export interface ForecastEntity {
    id: number;
    day: string;
    condition: string;
    high: number;
    low: number;
    icon: string;
    created_at: string;
    updated_at: string;
    weather_id: number;
}
