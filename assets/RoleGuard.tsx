import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuthContext } from "@/context/AuthContext";
import { CircularProgress, Container } from "@mui/material";

interface RoleGuardProps {
    allowedRoles: string[];
    children: React.ReactNode;
    fallbackPath?: string;
}

const RoleGuard: React.FC<RoleGuardProps> = ({
    allowedRoles,
    children,
    fallbackPath = "/",
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
                if (!isAuthenticated || !user) {
                    navigate(fallbackPath, { replace: true });
                    return;
                }

                const hasAccess = user.roles?.some((role) =>
                    allowedRoles.includes(role)
                );

                if (!hasAccess) {
                    navigate(fallbackPath, { replace: true });
                    return;
                }
            } catch {
                navigate(fallbackPath, { replace: true });
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
        loading,
    ]);

    if (loading || checking) {
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
    }

    return <>{children}</>;
};

export default RoleGuard;
