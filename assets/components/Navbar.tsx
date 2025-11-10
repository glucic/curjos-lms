import React from "react";
import { AppBar, Toolbar, Typography, Button, Box, Stack } from "@mui/material";
import { redirect, Link as RouterLink, useNavigate } from "react-router-dom";
import SchoolIcon from "@mui/icons-material/School";
import { useAuthContext } from "@/context/AuthContext";
import RoleGuard from "@/RoleGuard";
import { red } from "@mui/material/colors";

const Navbar: React.FC = () => {
    const { user, isAuthenticated, logout } = useAuthContext();
    const navigate = useNavigate();

    const handleLogout = () => {
        logout();
        navigate("/");
    };

    return (
        <AppBar position="sticky">
            <Toolbar>
                <Typography variant="h6" component="div" sx={{ flexGrow: 1 }}>
                    <Button color="inherit" component={RouterLink} to="/">
                        <SchoolIcon sx={{ mr: 2 }} />
                        <Typography variant="h6">Curjos LMS</Typography>
                    </Button>
                </Typography>
                <Box>
                    {isAuthenticated && user && (
                        <Stack
                            direction={{ xs: "column", sm: "row" }}
                            spacing={1.5}
                            alignItems="center"
                            justifyContent="flex-end"
                        >
                            <Typography
                                variant="h6"
                                component="div"
                                sx={{ flexGrow: 1, minWidth: 120 }}
                            >
                                {user.firstName} {user.lastName}
                            </Typography>

                            <RoleGuard
                                allowedRoles={["ROLE_ADMIN"]}
                                redirect={false}
                            >
                                <Button
                                    color="inherit"
                                    size="small"
                                    component={RouterLink}
                                    to={`/organization/${user.organization?.id}`}
                                >
                                    Manage {user.organization?.name}
                                </Button>
                            </RoleGuard>

                            <RoleGuard
                                allowedRoles={["ROLE_SUPER_ADMIN"]}
                                redirect={false}
                            >
                                <Button
                                    color="inherit"
                                    size="small"
                                    component={RouterLink}
                                    to="/super"
                                >
                                    Super Admin Panel
                                </Button>
                            </RoleGuard>

                            <Button
                                color="inherit"
                                size="small"
                                onClick={handleLogout}
                            >
                                Logout
                            </Button>
                        </Stack>
                    )}
                </Box>
            </Toolbar>
        </AppBar>
    );
};

export default Navbar;
