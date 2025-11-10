import React, { useEffect } from "react";
import {
    Container,
    Typography,
    List,
    ListItem,
    Card,
    CardContent,
    Divider,
    Button,
    Box,
    CircularProgress,
} from "@mui/material";
import { useNavigate } from "react-router-dom";
import {
    Add as AddIcon,
    Edit as EditIcon,
    Delete as DeleteIcon,
    Visibility as EyeIcon,
} from "@mui/icons-material";
import RoleGuard from "@/RoleGuard";
import { useOrganizations } from "@/hooks/use-organization";
import { Organization } from "@/types/organization";

const SuperAdminPanel: React.FC = () => {
    const navigate = useNavigate();
    const {
        organizations,
        loading,
        error,
        fetchOrganizations,
        deleteOrganization,
    } = useOrganizations();

    useEffect(() => {
        fetchOrganizations();
    }, [fetchOrganizations]);

    const handleDelete = async (id: number) => {
        try {
            await deleteOrganization(id);
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

    if (error || !organizations) {
        return (
            <Container maxWidth="lg">
                <Typography variant="h4" align="center" sx={{ mt: 10 }}>
                    Failed to load organizations.
                </Typography>
            </Container>
        );
    }

    return (
        <Container maxWidth="lg">
            <Typography variant="h4" sx={{ mt: 8, mb: 4 }}>
                Super Admin Panel - Organizations
            </Typography>

            <RoleGuard allowedRoles={["ROLE_SUPER_ADMIN"]} redirect={false}>
                <Button
                    variant="contained"
                    color="primary"
                    startIcon={<AddIcon />}
                    sx={{ mb: 4 }}
                    onClick={() => navigate("/organization/create")}
                >
                    Create Organization
                </Button>
            </RoleGuard>

            <List>
                {organizations.map((org: Organization) => (
                    <ListItem key={org.id} disableGutters sx={{ mb: 2 }}>
                        <Card sx={{ width: "100%" }}>
                            <CardContent>
                                <Typography variant="h5">{org.name}</Typography>
                                <Typography
                                    variant="body2"
                                    color="text.secondary"
                                >
                                    Slug: {org.slug} | Active:{" "}
                                    {org.isActive ? "Yes" : "No"} | System
                                    Organization:{" "}
                                    {org.isSystemOrganization ? "Yes" : "No"}
                                </Typography>
                                <Divider sx={{ my: 1 }} />
                                <Typography
                                    variant="body2"
                                    color="text.secondary"
                                >
                                    Users:
                                    {org.users.length > 0 ? (
                                        <ul>
                                            {org.users.map((user: any) => (
                                                <li key={user.id}>
                                                    {user.firstName}{" "}
                                                    {user.lastName} (
                                                    {user.email})
                                                </li>
                                            ))}
                                        </ul>
                                    ) : (
                                        " No users assigned."
                                    )}
                                </Typography>
                            </CardContent>

                            <CardContent>
                                <Box sx={{ display: "flex", gap: 1 }}>
                                    <Button
                                        variant="outlined"
                                        startIcon={<EyeIcon />}
                                        onClick={() =>
                                            navigate(`/organization/${org.id}`)
                                        }
                                    >
                                        View and Manage Users
                                    </Button>
                                    <Button
                                        variant="outlined"
                                        startIcon={<EditIcon />}
                                        onClick={() =>
                                            navigate(
                                                `/organization/${org.id}/edit`
                                            )
                                        }
                                    >
                                        Edit
                                    </Button>
                                    <Button
                                        variant="contained"
                                        color="error"
                                        startIcon={<DeleteIcon />}
                                        onClick={() => handleDelete(org.id)}
                                    >
                                        Delete
                                    </Button>
                                </Box>
                            </CardContent>
                        </Card>
                    </ListItem>
                ))}
            </List>
        </Container>
    );
};

export default SuperAdminPanel;
