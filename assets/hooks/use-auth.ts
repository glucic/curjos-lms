import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { authApi, ApiError } from "@/api/client";
import { AxiosError, AxiosResponse } from "axios";
import { RegisterFormData, LoginFormData, AuthResult } from "@/types/auth";
import { useAuthContext } from "@/context/AuthContext";

export function useAuth() {
    const navigate = useNavigate();
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [validationErrors, setValidationErrors] = useState<
        Record<string, string>
    >({});
    const { login: setAuthLogin, logout: setAuthLogout } = useAuthContext();

    const handleRegister = async (formData: RegisterFormData) => {
        setError(null);
        setValidationErrors({});
        if (formData.password !== formData.confirmPassword) {
            setValidationErrors({ confirmPassword: "Passwords do not match" });
            return;
        }
        setLoading(true);
        try {
            const { confirmPassword, ...registerData } = formData;
            await authApi.register(registerData);
            alert("Registration successful! Please login.");
            navigate("/login");
        } catch (err) {
            const axiosError = err as AxiosError<ApiError>;
            if (axiosError.response?.data) {
                const apiError = axiosError.response.data;
                if (apiError.details && Array.isArray(apiError.details)) {
                    const errors: Record<string, string> = {};
                    apiError.details.forEach((detail) => {
                        errors[detail.property] = detail.message;
                    });
                    setValidationErrors(errors);
                } else {
                    setError(apiError.message || "Registration failed");
                }
            } else {
                setError("Network error. Please try again.");
            }
        } finally {
            setLoading(false);
        }
    };

    const handleLogin = async (
        formData: LoginFormData
    ): Promise<AuthResult | void> => {
        setError(null);
        setValidationErrors({});
        setLoading(true);
        try {
            const result = await authApi.login(formData);
            setAuthLogin(result.data);
            navigate("/");
        } catch (err) {
            const axiosError = err as AxiosError<ApiError>;
            if (axiosError.response?.data) {
                const apiError = axiosError.response.data;
                if (apiError.message) {
                    setValidationErrors({
                        email: apiError.message,
                        password: apiError.message,
                    });
                } else {
                    setError("Login failed");
                }
            } else {
                setError("Network error. Please try again.");
            }
        } finally {
            setLoading(false);
        }
    };

    return {
        loading,
        error,
        validationErrors,
        handleRegister,
        handleLogin,
        logout: setAuthLogout,
    };
}
