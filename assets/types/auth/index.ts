export interface RegisterFormData {
    firstName?: string;
    lastName?: string;
    email: string;
    password: string;
    confirmPassword?: string;
    organization?: string;
}

export interface LoginFormData {
    email: string;
    password: string;
}

export interface Organization {
    id: number;
    name: string;
    slug: string;
    isSystemOrganization: boolean;
}

export interface User {
    id: number;
    email: string;
    firstName: string;
    lastName: string;
    fullName?: string;
    isActive: boolean;
    createdAt: string;
    organization: Organization;
    roles: string[];
    permissions: string[];
}

export interface AuthResult {
    token: string;
    user: User;
}
