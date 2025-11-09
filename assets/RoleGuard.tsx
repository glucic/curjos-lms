import React, { useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { useAuthContext } from "@/context/AuthContext";
import { Container, CircularProgress } from "@mui/material";

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
    const { user, isAuthenticated, fetchUser } = useAuthContext();
    const [loading, setLoading] = React.useState(true);
    const navigate = useNavigate();

    useEffect(() => {
        const checkAuth = async () => {
            if (!isAuthenticated) {
                setLoading(false);
                navigate(fallbackPath, { replace: true });
                return;
            }

            if (!user) {
                try {
                    await fetchUser();
                } catch (err) {
                    navigate(fallbackPath, { replace: true });
                    return;
                }
            }

            if (
                !user?.roles ||
                !user.roles.some((role) => allowedRoles.includes(role))
            ) {
                navigate(fallbackPath, { replace: true });
            }

            setLoading(false);
        };

        checkAuth();
    }, [
        user,
        allowedRoles,
        navigate,
        fallbackPath,
        isAuthenticated,
        fetchUser,
    ]);

    if (loading)
        return (
            <div
                style={{
                    display: "flex",
                    justifyContent: "center",
                    alignItems: "center",
                    height: "100vh",
                }}
            >
                <CircularProgress />
            </div>
        );

    return <>{children}</>;
};

export default RoleGuard;
