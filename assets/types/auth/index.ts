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

export interface AuthResult {
    token: string;
    user: {
        id: string;
        email: string;
        firstName: string;
        lastName: string;
        organization: string;
    };
}
