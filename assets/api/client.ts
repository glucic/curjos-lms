import axios, { AxiosError } from "axios";
import { RegisterFormData, LoginFormData } from "@/types/auth";
import { User } from "@/types/organization";
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
        }
        return Promise.reject(error);
    }
);

export default apiClient;

export const authApi = {
    login: (data: LoginFormData) => apiClient.post("/auth/login", data),
    me: () => apiClient.get("/auth/me"),
};

export const courseApi = {
    getCourses: () => apiClient.get("/courses"),
    getCourse: (id: number) => apiClient.get(`/courses/${id}`),
    createCourse: (data: any) => apiClient.post("/courses", data),
    updateCourse: (id: number, data: any) =>
        apiClient.put(`/courses/${id}`, data),
    deleteCourse: (id: number) => apiClient.delete(`/courses/${id}`),
    getLesson: (courseId: number, lessonId: number) =>
        apiClient.get(`/courses/${courseId}/lessons/${lessonId}`),
    createLesson: (courseId: number, data: any) =>
        apiClient.post(`/courses/${courseId}/lessons`, data),
    updateLesson: (courseId: number, lessonId: number, data: any) =>
        apiClient.put(`/courses/${courseId}/lessons/${lessonId}`, data),
    deleteLesson: (courseId: number, lessonId: number) =>
        apiClient.delete(`/courses/${courseId}/lessons/${lessonId}`),
};

export const organizationApi = {
    getOrganizations: () => apiClient.get("/organizations"),
    getOrganization: (id: number) => apiClient.get(`/organizations/${id}`),
    createOrganization: (data: { name: string; isActive?: boolean }) =>
        apiClient.post("/organizations", data),
    updateOrganization: (
        id: number,
        data: { name?: string; isActive?: boolean }
    ) => apiClient.put(`/organizations/${id}`, data),
    deleteOrganization: (id: number) =>
        apiClient.delete(`/organizations/${id}`),
};

export const userApi = {
    getUser: (userId: number) =>
        apiClient.get<{ data: User }>(`/users/${userId}`),
    createUser: (data: Partial<User>) =>
        apiClient.post<{ data: User }>(`/users`, data),
    updateUser: (userId: number, data: Partial<User>) =>
        apiClient.put<{ data: User }>(`/users/${userId}`, data),
    deleteUser: (userId: number) => apiClient.delete(`/users/${userId}`),
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
