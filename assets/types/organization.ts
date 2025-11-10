export interface User {
    id: number;
    email: string;
    firstName: string;
    lastName: string;
    isActive: boolean;
    createdAt: string;
    updatedAt?: string | null;
    role: string;
    password?: string;
    confirmPassword?: string;
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
