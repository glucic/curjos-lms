import React, { useEffect, useState } from "react";
import {
    Container,
    Typography,
    TextField,
    Button,
    Box,
    CircularProgress,
    FormControlLabel,
    Switch,
} from "@mui/material";
import { useParams, useNavigate } from "react-router-dom";
import { useOrganizations } from "@/hooks/use-organization";

const EditOrganization: React.FC = () => {
    const { id } = useParams<{ id: string }>();
    const organizationId = id ? parseInt(id) : undefined;
    const navigate = useNavigate();
    const { organization, fetchOrganization, updateOrganization, loading } =
        useOrganizations();

    const [name, setName] = useState("");
    const [isActive, setIsActive] = useState(true);

    useEffect(() => {
        if (organizationId) {
            fetchOrganization(organizationId);
        }
    }, [organizationId, fetchOrganization]);

    useEffect(() => {
        if (organization) {
            setName(organization.name);
            setIsActive(organization.isActive);
        }
    }, [organization]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!organizationId) return;

        try {
            await updateOrganization(organizationId, { name, isActive });
            navigate(`/super`);
        } catch (err) {
            console.error(err);
        }
    };

    if (loading || !organization) {
        return (
            <Container
                maxWidth="sm"
                sx={{ display: "flex", justifyContent: "center", mt: 8 }}
            >
                <CircularProgress />
            </Container>
        );
    }

    return (
        <Container maxWidth="sm" sx={{ mt: 8 }}>
            <Typography variant="h4" sx={{ mb: 4 }}>
                Edit Organization
            </Typography>

            <form onSubmit={handleSubmit}>
                <TextField
                    label="Organization Name"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    fullWidth
                    required
                    sx={{ mb: 3 }}
                />

                <FormControlLabel
                    control={
                        <Switch
                            checked={isActive}
                            onChange={(e) => setIsActive(e.target.checked)}
                        />
                    }
                    label="Active"
                    sx={{ mb: 3 }}
                />

                <Box sx={{ display: "flex", gap: 2 }}>
                    <Button
                        type="submit"
                        variant="contained"
                        disabled={loading}
                    >
                        {loading ? <CircularProgress size={24} /> : "Save"}
                    </Button>
                    <Button
                        variant="outlined"
                        onClick={() => navigate(`/super`)}
                        disabled={loading}
                    >
                        Cancel
                    </Button>
                </Box>
            </form>
        </Container>
    );
};

export default EditOrganization;
