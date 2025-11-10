import { useState, useCallback } from "react";
import { ApiError, userApi } from "@/api/client";
import { User } from "@/types/organization";

export const useUsers = () => {
    const [users, setUsers] = useState<User[] | null>(null);
    const [user, setUser] = useState<User | null>(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const fetchUser = useCallback(async (userId: number) => {
        setLoading(true);
        setError(null);
        try {
            const response = await userApi.getUser(userId);
            setUser(response.data.data);
        } catch (err) {
            const apiError = err as ApiError;
            setError(apiError?.message || "Failed to fetch user");
        } finally {
            setLoading(false);
        }
    }, []);

    const createUser = useCallback(async (data: Partial<User>) => {
        setLoading(true);
        setError(null);
        try {
            const response = await userApi.createUser(data);
            setUsers((prev) =>
                prev ? [...prev, response.data.data] : [response.data.data]
            );
            setUser(response.data.data);
            return response.data.data;
        } catch (err) {
            const apiError = err as ApiError;
            setError(apiError?.message || "Failed to create user");
            throw err;
        } finally {
            setLoading(false);
        }
    }, []);

    const updateUser = useCallback(
        async (userId: number, data: Partial<User>) => {
            setLoading(true);
            setError(null);
            try {
                const response = await userApi.updateUser(userId, data);
                setUsers(
                    (prev) =>
                        prev?.map((u) =>
                            u.id === userId ? response.data.data : u
                        ) || null
                );
                if (user?.id === userId) setUser(response.data.data);
                return response.data.data;
            } catch (err) {
                const apiError = err as ApiError;
                setError(apiError?.message || "Failed to update user");
                throw err;
            } finally {
                setLoading(false);
            }
        },
        [user]
    );

    const deleteUser = useCallback(
        async (userId: number) => {
            setLoading(true);
            setError(null);
            try {
                await userApi.deleteUser(userId);
                setUsers(
                    (prev) => prev?.filter((u) => u.id !== userId) || null
                );
                if (user?.id === userId) setUser(null);
            } catch (err) {
                const apiError = err as ApiError;
                setError(apiError?.message || "Failed to delete user");
                throw err;
            } finally {
                setLoading(false);
            }
        },
        [user]
    );

    return {
        users,
        user,
        loading,
        error,
        fetchUser,
        createUser,
        updateUser,
        deleteUser,
    };
};
