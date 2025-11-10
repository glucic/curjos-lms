import React, { useEffect, useState } from "react";
import {
    Container,
    Typography,
    Card,
    CardContent,
    Divider,
    List,
    ListItem,
    Box,
    Button,
    CircularProgress,
} from "@mui/material";
import { useParams, useNavigate } from "react-router-dom";
import {
    Add as AddIcon,
    Edit as EditIcon,
    Delete as DeleteIcon,
} from "@mui/icons-material";
import RoleGuard from "@/RoleGuard";
import { useOrganizations } from "@/hooks/use-organization";
import { useUsers } from "@/hooks/use-users";
import { Organization as OrganizationType, User } from "@/types/organization";

const Organization: React.FC = () => {
    const { id } = useParams<{ id: string }>();
    const organizationId = id ? parseInt(id) : undefined;
    const navigate = useNavigate();

    const { organization, loading, error, fetchOrganization } =
        useOrganizations();

    const { deleteUser } = useUsers();

    useEffect(() => {
        if (organizationId) {
            fetchOrganization(organizationId);
        }
    }, [organizationId, fetchOrganization]);

    const handleDeleteUser = async (userId: number) => {
        if (!organizationId) return;
        try {
            await deleteUser(userId);
            fetchOrganization(organizationId);
        } catch (e) {
            console.error(e);
        }
    };

    if (loading) {
        return (
            <Container
                maxWidth="lg"
                sx={{
                    display: "flex",
                    justifyContent: "center",
                    alignItems: "center",
                    height: "80vh",
                }}
            >
                <CircularProgress />
            </Container>
        );
    }

    if (error || !organization) {
        return (
            <Container maxWidth="lg">
                <Typography variant="h4" align="center" sx={{ mt: 10 }}>
                    Failed to load organization.
                </Typography>
            </Container>
        );
    }

    return (
        <Container maxWidth="lg">
            <Typography variant="h4" sx={{ mt: 8, mb: 4 }}>
                Organization: {organization.name}
            </Typography>

            <Card sx={{ mb: 4 }}>
                <CardContent>
                    <Typography variant="body1">
                        Slug: {organization.slug}
                    </Typography>
                    <Typography variant="body1">
                        Active: {organization.isActive ? "Yes" : "No"}
                    </Typography>
                    <Divider sx={{ my: 2 }} />
                    <Typography variant="body1">
                        Created At:{" "}
                        {new Date(organization.createdAt).toLocaleString()}
                    </Typography>
                </CardContent>
            </Card>

            <Box
                sx={{ display: "flex", justifyContent: "space-between", mb: 2 }}
            >
                <Typography variant="h5">Users</Typography>
                <RoleGuard
                    allowedRoles={["ROLE_SUPER_ADMIN", "ROLE_ADMIN"]}
                    redirect={false}
                >
                    <Button
                        variant="contained"
                        startIcon={<AddIcon />}
                        onClick={() =>
                            navigate(
                                `/organization/${organizationId}/user/create`
                            )
                        }
                    >
                        Add User
                    </Button>
                </RoleGuard>
            </Box>

            <List>
                {organization.users.length > 0 ? (
                    organization.users.map((user: User) => (
                        <ListItem key={user.id} disableGutters sx={{ mb: 1 }}>
                            <Card sx={{ width: "100%" }}>
                                <CardContent
                                    sx={{
                                        display: "flex",
                                        justifyContent: "space-between",
                                        alignItems: "center",
                                    }}
                                >
                                    <Box>
                                        <Typography variant="body1">
                                            {user.firstName} {user.lastName}
                                        </Typography>
                                        <Typography
                                            variant="body2"
                                            color="text.secondary"
                                        >
                                            {user.email}
                                        </Typography>
                                    </Box>
                                    <Box sx={{ display: "flex", gap: 1 }}>
                                        <RoleGuard
                                            allowedRoles={[
                                                "ROLE_SUPER_ADMIN",
                                                "ROLE_ADMIN",
                                            ]}
                                            redirect={false}
                                        >
                                            <Button
                                                variant="outlined"
                                                startIcon={<EditIcon />}
                                                onClick={() =>
                                                    navigate(
                                                        `/organization/${organizationId}/user/${user.id}/edit`
                                                    )
                                                }
                                            >
                                                Edit
                                            </Button>
                                            <Button
                                                variant="contained"
                                                color="error"
                                                startIcon={<DeleteIcon />}
                                                onClick={() =>
                                                    handleDeleteUser(user.id)
                                                }
                                            >
                                                Delete
                                            </Button>
                                        </RoleGuard>
                                    </Box>
                                </CardContent>
                            </Card>
                        </ListItem>
                    ))
                ) : (
                    <Typography variant="body2" color="text.secondary">
                        No users in this organization.
                    </Typography>
                )}
            </List>
        </Container>
    );
};

export default Organization;
