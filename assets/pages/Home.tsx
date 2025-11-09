import React from "react";
import {
    Container,
    Typography,
    Box,
    Button,
    Paper,
    Divider,
    CircularProgress,
} from "@mui/material";
import { useNavigate } from "react-router-dom";
import SchoolIcon from "@mui/icons-material/School";
import { useAuthContext } from "@/context/AuthContext";
import CoursesList from "@/components/CoursesList";

const Home: React.FC = () => {
    const navigate = useNavigate();
    const { isAuthenticated, loading } = useAuthContext();

    return (
        <Container maxWidth={false}>
            <Container>
                <Box
                    sx={{
                        mt: 8,
                        display: "flex",
                        flexDirection: "column",
                        alignItems: "center",
                    }}
                >
                    <SchoolIcon
                        sx={{ fontSize: 80, color: "primary.main", mb: 2 }}
                    />

                    <Typography
                        component="h1"
                        variant="h2"
                        align="center"
                        gutterBottom
                    >
                        Welcome to Curjos LMS!
                    </Typography>

                    <Typography
                        variant="h5"
                        color="text.secondary"
                        align="center"
                        paragraph
                    >
                        A modern learning management system for students and
                        instructors
                    </Typography>

                    {!isAuthenticated ? (
                        <Box sx={{ mt: 4, display: "flex", gap: 2 }}>
                            <Button
                                variant="contained"
                                size="large"
                                onClick={() => navigate("/register")}
                            >
                                Get Started
                            </Button>
                            <Button
                                variant="outlined"
                                size="large"
                                onClick={() => navigate("/login")}
                            >
                                Login
                            </Button>
                        </Box>
                    ) : null}
                </Box>
            </Container>
            {isAuthenticated ? (
                <>
                    <Divider sx={{ my: 1 }} />
                    <CoursesList />
                </>
            ) : null}
        </Container>
    );
};

export default Home;
