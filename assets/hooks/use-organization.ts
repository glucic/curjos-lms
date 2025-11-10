import { useState, useEffect, useCallback } from "react";
import { ApiError, organizationApi } from "@/api/client";
import { Organization } from "@/types/organization";

export const useOrganizations = () => {
    const [organizations, setOrganizations] = useState<Organization[] | null>(
        null
    );
    const [organization, setOrganization] = useState<Organization | null>(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const fetchOrganizations = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            const response = await organizationApi.getOrganizations();
            setOrganizations(response.data.data);
        } catch (err) {
            const apiError = err as ApiError;
            setError(apiError?.message || "Failed to fetch organizations");
        } finally {
            setLoading(false);
        }
    }, []);

    const fetchOrganization = useCallback(async (id: number) => {
        setLoading(true);
        setError(null);
        try {
            const response = await organizationApi.getOrganization(id);
            setOrganization(response.data.data);
        } catch (err) {
            const apiError = err as ApiError;
            setError(apiError?.message || "Failed to fetch organization");
        } finally {
            setLoading(false);
        }
    }, []);

    const createOrganization = useCallback(
        async (data: { name: string; isActive?: boolean }) => {
            setLoading(true);
            setError(null);
            try {
                const response = await organizationApi.createOrganization(data);
                setOrganizations((prev) =>
                    prev ? [...prev, response.data.data] : [response.data.data]
                );
                return response.data.data;
            } catch (err) {
                const apiError = err as ApiError;
                setError(apiError?.message || "Failed to create organization");
                throw err;
            } finally {
                setLoading(false);
            }
        },
        []
    );

    const updateOrganization = useCallback(
        async (id: number, data: { name?: string; isActive?: boolean }) => {
            setLoading(true);
            setError(null);
            try {
                const response = await organizationApi.updateOrganization(
                    id,
                    data
                );
                setOrganization(response.data.data);
                setOrganizations(
                    (prev) =>
                        prev?.map((org) =>
                            org.id === id ? response.data.data : org
                        ) || null
                );
                return response.data.data;
            } catch (err) {
                const apiError = err as ApiError;
                setError(apiError?.message || "Failed to update organization");
                throw err;
            } finally {
                setLoading(false);
            }
        },
        []
    );

    const deleteOrganization = useCallback(
        async (id: number) => {
            setLoading(true);
            setError(null);
            try {
                await organizationApi.deleteOrganization(id);
                setOrganizations(
                    (prev) => prev?.filter((org) => org.id !== id) || null
                );
                if (organization?.id === id) setOrganization(null);
            } catch (err) {
                const apiError = err as ApiError;
                setError(apiError?.message || "Failed to delete organization");
                throw err;
            } finally {
                setLoading(false);
            }
        },
        [organization]
    );

    return {
        organizations,
        organization,
        loading,
        error,
        fetchOrganizations,
        fetchOrganization,
        createOrganization,
        updateOrganization,
        deleteOrganization,
    };
};
