import React, { useState } from "react";
import {
    Container,
    Box,
    Typography,
    TextField,
    Button,
    Paper,
    Link,
    CircularProgress,
    Alert,
} from "@mui/material";
import { useNavigate } from "react-router-dom";
import { useAuth } from "@/hooks/use-auth";
import { LoginFormData } from "@/types/auth";
import { useAuthContext } from "@/context/AuthContext";

const Login: React.FC = () => {
    const navigate = useNavigate();
    const { loading, error, validationErrors, handleLogin } = useAuth();
    const { isAuthenticated } = useAuthContext();
    React.useEffect(() => {
        if (isAuthenticated) {
            navigate("/");
        }
    }, [isAuthenticated, navigate]);
    const [formData, setFormData] = useState<LoginFormData>({
        email: "",
        password: "",
    });

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData((prev) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        await handleLogin(formData);
    };

    return (
        <Container component="main" maxWidth="sm">
            <Box
                sx={{
                    mt: 8,
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center",
                }}
            >
                <Paper elevation={3} sx={{ p: 4, width: "100%" }}>
                    <Typography
                        component="h1"
                        variant="h4"
                        align="center"
                        gutterBottom
                    >
                        Login
                    </Typography>

                    <Typography
                        variant="body2"
                        color="text.secondary"
                        align="center"
                        sx={{ mb: 3 }}
                    >
                        Welcome back! Please sign in to continue.
                    </Typography>

                    {error && (
                        <Alert severity="error" sx={{ mb: 2 }}>
                            {error}
                        </Alert>
                    )}

                    <Box
                        component="form"
                        onSubmit={handleSubmit}
                        sx={{ mt: 1 }}
                    >
                        <TextField
                            margin="normal"
                            required
                            fullWidth
                            id="email"
                            label="Email Address"
                            name="email"
                            autoComplete="email"
                            type="email"
                            value={formData.email}
                            onChange={handleChange}
                            error={!!validationErrors.email}
                            helperText={validationErrors.email}
                        />

                        <TextField
                            margin="normal"
                            required
                            fullWidth
                            name="password"
                            label="Password"
                            type="password"
                            id="password"
                            autoComplete="current-password"
                            value={formData.password}
                            onChange={handleChange}
                            error={!!validationErrors.password}
                            helperText={validationErrors.password}
                        />

                        <Button
                            type="submit"
                            fullWidth
                            variant="contained"
                            sx={{ mt: 3, mb: 2 }}
                            disabled={loading}
                        >
                            {loading ? <CircularProgress size={24} /> : "Login"}
                        </Button>

                        <Box sx={{ textAlign: "center" }}>
                            <Link
                                component="button"
                                variant="body2"
                                onClick={() => navigate("/register")}
                                type="button"
                            >
                                Don't have an account? Register
                            </Link>
                        </Box>
                    </Box>
                </Paper>
            </Box>
        </Container>
    );
};

export default Login;
