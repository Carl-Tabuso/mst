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
    items?: {
        title: string,
        href: string,
    }[];
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
}

export interface User {
    employee_id: any;
    id: number;
    email: string;
    avatar?: string;
    emailVerifiedAt?: string;
    createdAt: string;
    updatedAt: string;
    deletedAt?: string;
    employee: Employee,
}

export interface Employee {
    id: number;
    firstName: string;
    middleName?: string;
    lastName: string;
    suffix?: string;
    fullName: string;
    positionId: number;
    createdAt: string;
    updatedAt: string,
    account?: User,
}

export interface JobOrder {
    machineInfos: never[];
    id: number;
    serviceableId: number;
    serviceableType: string;
    dateTime: string;
    client: string;
    address: string;
    department: string;
    contactNo: string;
    contactPerson: string;
    createdBy: number;
    status: string;
    errorCount: number;
    createdAt: string;
    updatedAt: string;
    creator?: Employee;
    service?: any;
}

export interface EloquentCollection {
    current_page: number,
    last_page: number,
    per_page: number,
    from: number,
    to: number,
    total: number,
}
