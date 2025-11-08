import React from "react";
import { AppBar, Toolbar, Typography, Button, Box } from "@mui/material";
import { Link as RouterLink } from "react-router-dom";
import SchoolIcon from "@mui/icons-material/School";

const Navbar: React.FC = () => {
    return (
        <AppBar position="static">
            <Toolbar>
                <SchoolIcon sx={{ mr: 2 }} />
                <Typography variant="h6" component="div" sx={{ flexGrow: 1 }}>
                    Curjos LMS
                </Typography>
                <Box>
                    <Button color="inherit" component={RouterLink} to="/">
                        Home
                    </Button>
                    <Button
                        color="inherit"
                        component={RouterLink}
                        to="/register"
                    >
                        Register
                    </Button>
                    <Button color="inherit" component={RouterLink} to="/login">
                        Login
                    </Button>
                </Box>
            </Toolbar>
        </AppBar>
    );
};

export default Navbar;
