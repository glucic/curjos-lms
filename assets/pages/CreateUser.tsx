import React, { useState, useEffect } from "react";
import {
    Container,
    Typography,
    TextField,
    Button,
    CircularProgress,
    Box,
} from "@mui/material";
import { useNavigate, useParams } from "react-router-dom";
import { useUsers } from "@/hooks/use-users";
import { useOrganizations } from "@/hooks/use-organization";

const CreateUser: React.FC = () => {
    const { organizationId } = useParams<{ organizationId: string }>();
    const orgId = organizationId ? parseInt(organizationId) : undefined;
    const navigate = useNavigate();

    const { createUser, loading } = useUsers();
    const { organization, fetchOrganization } = useOrganizations();

    const [firstName, setFirstName] = useState("");
    const [lastName, setLastName] = useState("");
    const [email, setEmail] = useState("");
    const [role, setRole] = useState("ROLE_STUDENT");
    const [password, setPassword] = useState("");
    const [confirmPassword, setConfirmPassword] = useState("");

    useEffect(() => {
        if (orgId) {
            fetchOrganization(orgId);
        }
    }, [orgId, fetchOrganization]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!orgId) return;
        try {
            await createUser({
                firstName,
                lastName,
                email,
                role,
                ...(password ? { password } : {}),
            });
            navigate(`/organization/${orgId}`);
        } catch (err) {
            console.error(err);
        }
    };

    if (!organization) return <CircularProgress />;

    return (
        <Container maxWidth="sm">
            <Typography variant="h4" sx={{ mt: 4, mb: 2 }}>
                Add User to {organization.name}
            </Typography>

            <Box
                component="form"
                onSubmit={handleSubmit}
                sx={{ display: "flex", flexDirection: "column", gap: 2 }}
            >
                <TextField
                    label="First Name"
                    value={firstName}
                    onChange={(e) => setFirstName(e.target.value)}
                    required
                />
                <TextField
                    label="Last Name"
                    value={lastName}
                    onChange={(e) => setLastName(e.target.value)}
                    required
                />
                <TextField
                    label="Email"
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    required
                />
                <TextField
                    label="Role"
                    value={role}
                    onChange={(e) => setRole(e.target.value)}
                    helperText="Roles: ROLE_ADMIN, ROLE_SUPER_ADMIN, ROLE_STUDENT"
                    required
                />
                <TextField
                    label="Password"
                    type="password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    required
                />
                <TextField
                    label="Confirm Password"
                    type="password"
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    required
                />
                <Button type="submit" variant="contained" disabled={loading}>
                    {loading ? <CircularProgress size={24} /> : "Create User"}
                </Button>
            </Box>
        </Container>
    );
};

export default CreateUser;
