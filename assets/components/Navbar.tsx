import React from "react";
import { AppBar, Toolbar, Typography, Button, Box } from "@mui/material";
import { Link as RouterLink, useNavigate } from "react-router-dom";
import SchoolIcon from "@mui/icons-material/School";
import { useAuthContext } from "@/context/AuthContext";

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
                        <>
                            <Button
                                color="inherit"
                                component={RouterLink}
                                to="/profile"
                            >
                                {user.firstName} {user.lastName}
                            </Button>
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
