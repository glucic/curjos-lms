import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuthContext } from "@/context/AuthContext";
import { CircularProgress, Container } from "@mui/material";

interface RoleGuardProps {
    allowedRoles: string[];
    children: React.ReactNode;
    fallbackPath?: string;
    redirect?: boolean;
}

const RoleGuard: React.FC<RoleGuardProps> = ({
    allowedRoles,
    children,
    fallbackPath = "/",
    redirect = true,
}) => {
    const { user, isAuthenticated, fetchUser, loading } = useAuthContext();
    const [checking, setChecking] = useState(true);
    const navigate = useNavigate();

    useEffect(() => {
        const verify = async () => {
            if (loading) return;

            try {
                if (!user && isAuthenticated) {
                    await fetchUser();
                }

                const hasAccess =
                    isAuthenticated &&
                    user?.role?.name &&
                    allowedRoles.includes(user.role.name);

                if (!hasAccess && redirect) {
                    navigate(fallbackPath, { replace: true });
                }
            } catch {
                if (redirect) navigate(fallbackPath, { replace: true });
            } finally {
                setChecking(false);
            }
        };

        verify();
    }, [
        user,
        isAuthenticated,
        fetchUser,
        allowedRoles,
        navigate,
        fallbackPath,
        redirect,
        loading,
    ]);

    if (loading || checking) {
        if (redirect) {
            return (
                <Container
                    maxWidth={false}
                    sx={{
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                        height: "100vh",
                    }}
                >
                    <CircularProgress />
                </Container>
            );
        } else {
            return null;
        }
    }

    if (!redirect) {
        const hasAccess =
            isAuthenticated &&
            user?.role?.name &&
            allowedRoles.includes(user.role.name);
        if (!hasAccess) return null;
    }

    return <>{children}</>;
};

export default RoleGuard;
