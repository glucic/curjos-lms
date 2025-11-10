import React from "react";
import { AppBar, Toolbar, Typography, Button, Box } from "@mui/material";
import { redirect, Link as RouterLink, useNavigate } from "react-router-dom";
import SchoolIcon from "@mui/icons-material/School";
import { useAuthContext } from "@/context/AuthContext";
import RoleGuard from "@/RoleGuard";
import { red } from "@mui/material/colors";

const Navbar: React.FC = () => {
    const { user, isAuthenticated, logout } = useAuthContext();
    const navigate = useNavigate();
    console.log(user);
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
                        <>
                            <Button
                                color="inherit"
                                component={RouterLink}
                                to="/profile"
                            >
                                {user.firstName} {user.lastName}
                            </Button>
                            <RoleGuard
                                allowedRoles={["ROLE_ADMIN"]}
                                redirect={false}
                            >
                                <Button
                                    color="inherit"
                                    component={RouterLink}
                                    to="/course/create"
                                >
                                    Manage {user.organization?.name}
                                </Button>
                            </RoleGuard>
                            <Button color="inherit" onClick={handleLogout}>
                                Logout
                            </Button>
                        </>
                    )}
                </Box>
            </Toolbar>
        </AppBar>
    );
};

export default Navbar;
