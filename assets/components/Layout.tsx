import React from "react";
import { Box } from "@mui/material";
import Navbar from "@/components/Navbar";

interface LayoutProps {
    children: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
    return (
        <Box sx={{ minHeight: "100vh", bgcolor: "background.default" }}>
            <Navbar />
            <Box component="main" sx={{ pb: 6 }}>
                {children}
            </Box>
        </Box>
    );
};

export default Layout;
