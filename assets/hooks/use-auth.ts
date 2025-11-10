import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { authApi, ApiError } from "@/api/client";
import { AxiosError } from "axios";
import { LoginFormData, AuthResult } from "@/types/auth";
import { useAuthContext } from "@/context/AuthContext";

export function useAuth() {
    const navigate = useNavigate();
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [validationErrors, setValidationErrors] = useState<
        Record<string, string>
    >({});
    const { login: setAuthLogin, logout: setAuthLogout } = useAuthContext();

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
        handleLogin,
        logout: setAuthLogout,
    };
}
