import React, { useState, useEffect } from "react";
import {
    Container,
    Typography,
    TextField,
    Button,
    CircularProgress,
    Box,
    Select,
    MenuItem,
    InputLabel,
    FormControl,
} from "@mui/material";
import { useNavigate, useParams } from "react-router-dom";
import { useUsers } from "@/hooks/use-users";
import { useOrganizations } from "@/hooks/use-organization";

const roleOptions = ["ROLE_STUDENT", "ROLE_ADMIN", "ROLE_INSTRUCTOR"];

export const CreateUser: React.FC = () => {
    const { organizationId } = useParams<{ organizationId: string }>();
    const orgId = organizationId ? parseInt(organizationId) : undefined;
    const navigate = useNavigate();

    const { createUser, loading } = useUsers();
    const { organization, fetchOrganization } = useOrganizations();

    const [firstName, setFirstName] = useState("");
    const [lastName, setLastName] = useState("");
    const [email, setEmail] = useState("");
    const [role, setRole] = useState<{ name: string }>({
        name: "ROLE_STUDENT",
    });
    const [password, setPassword] = useState("");
    const [confirmPassword, setConfirmPassword] = useState("");

    useEffect(() => {
        if (orgId) fetchOrganization(orgId);
    }, [orgId, fetchOrganization]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!orgId) return;
        if (password !== confirmPassword) {
            alert("Passwords do not match!");
            return;
        }

        try {
            await createUser({
                firstName,
                lastName,
                email,
                role,
                password,
                organizationId: orgId,
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
                <FormControl required>
                    <InputLabel id="role-label">Role</InputLabel>
                    <Select
                        labelId="role-label"
                        value={role.name}
                        onChange={(e) => setRole({ name: e.target.value })}
                    >
                        {roleOptions.map((r) => (
                            <MenuItem key={r} value={r}>
                                {r}
                            </MenuItem>
                        ))}
                    </Select>
                </FormControl>
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
