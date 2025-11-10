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

const roleOptions = ["ROLE_ADMIN", "ROLE_STUDENT", "ROLE_INSTRUCTOR"];

export const EditUser: React.FC = () => {
    const { organizationId, userId } = useParams<{
        organizationId: string;
        userId: string;
    }>();
    const orgId = organizationId ? parseInt(organizationId) : undefined;
    const uId = userId ? parseInt(userId) : undefined;
    const navigate = useNavigate();

    const { user, fetchUser, updateUser, loading } = useUsers();
    const { fetchOrganization, organization } = useOrganizations();

    const [firstName, setFirstName] = useState("");
    const [lastName, setLastName] = useState("");
    const [email, setEmail] = useState("");
    const [role, setRole] = useState<{ name: string }>({
        name: "ROLE_STUDENT",
    });
    const [password, setPassword] = useState("");
    const [confirmPassword, setConfirmPassword] = useState("");

    useEffect(() => {
        if (uId) fetchUser(uId);
        if (orgId) fetchOrganization(orgId);
    }, [uId, orgId, fetchUser, fetchOrganization]);

    useEffect(() => {
        if (user) {
            setFirstName(user.firstName);
            setLastName(user.lastName);
            setEmail(user.email);
            setRole({ name: user.role?.name });
        }
    }, [user]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!orgId || !uId) return;
        if (password && password !== confirmPassword) {
            alert("Passwords do not match!");
            return;
        }

        try {
            await updateUser(uId, {
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

    if (!user || !organization) return <CircularProgress />;

    return (
        <Container maxWidth="sm">
            <Typography variant="h4" sx={{ mt: 4, mb: 2 }}>
                Edit User in {organization.name}
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
                        value={role?.name || ""}
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
                />
                <TextField
                    label="Confirm Password"
                    type="password"
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                />
                <Button type="submit" variant="contained" disabled={loading}>
                    {loading ? <CircularProgress size={24} /> : "Update User"}
                </Button>
            </Box>
        </Container>
    );
};

export default EditUser;
