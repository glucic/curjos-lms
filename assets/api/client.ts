import axios, { AxiosError } from "axios";
import { RegisterFormData, LoginFormData } from "../types/auth";
const API_BASE_URL = "/api";

const apiClient = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        "Content-Type": "application/json",
    },
});

apiClient.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem("jwt_token");
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

apiClient.interceptors.response.use(
    (response) => response,
    (error: AxiosError) => {
        if (error.response?.status === 401) {
            localStorage.removeItem("jwt_token");
            window.location.href = "/login";
        }
        return Promise.reject(error);
    }
);

export default apiClient;

export const authApi = {
    register: (data: RegisterFormData) =>
        apiClient.post("/auth/register", data),

    login: (data: LoginFormData) => apiClient.post("/auth/login", data),

    me: () => apiClient.get("/auth/me"),
};

export interface ApiError {
    error: string;
    message: string;
    details?: Array<{
        property: string;
        value: any;
        message: string;
    }>;
}
