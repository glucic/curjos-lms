import React, { useState } from "react";
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
import { useNavigate } from "react-router-dom";
import { useOrganizations } from "@/hooks/use-organization";

const CreateOrganization: React.FC = () => {
    const [name, setName] = useState("");
    const [isActive, setIsActive] = useState(true);
    const navigate = useNavigate();
    const { createOrganization, loading } = useOrganizations();

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        try {
            const org = await createOrganization({ name, isActive });
            navigate(`/super`);
        } catch (err) {
            console.error(err);
        }
    };

    return (
        <Container maxWidth="sm" sx={{ mt: 8 }}>
            <Typography variant="h4" sx={{ mb: 4 }}>
                Create Organization
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
                        {loading ? <CircularProgress size={24} /> : "Create"}
                    </Button>
                    <Button
                        variant="outlined"
                        onClick={() => navigate("/super")}
                        disabled={loading}
                    >
                        Cancel
                    </Button>
                </Box>
            </form>
        </Container>
    );
};

export default CreateOrganization;
