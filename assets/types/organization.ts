import { Role } from "./auth";

export interface User {
    id: number;
    email: string;
    firstName: string;
    lastName: string;
    isActive: boolean;
    createdAt: string;
    updatedAt?: string | null;
    role: Role;
    password?: string;
    confirmPassword?: string;
    organizationId?: number;
}

export interface Organization {
    id: number;
    name: string;
    slug: string;
    isActive: boolean;
    isSystemOrganization: boolean;
    createdAt: string;
    updatedAt?: string | null;
    users: User[];
}
