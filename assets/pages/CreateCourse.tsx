import React, { useState } from "react";
import {
    Container,
    Typography,
    TextField,
    Button,
    CircularProgress,
} from "@mui/material";
import { useNavigate } from "react-router-dom";
import { useCourseData } from "@/hooks/use-course";

const CreateCourse: React.FC = () => {
    const navigate = useNavigate();
    const { createCourse, loading } = useCourseData();

    const [title, setTitle] = useState("");
    const [description, setDescription] = useState("");
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);

        try {
            const newCourse = await createCourse({ title, description });
            navigate(`/course/${newCourse.id}`);
        } catch (err) {
            setError("Failed to create course");
        }
    };

    return (
        <Container maxWidth="sm" sx={{ mt: 8 }}>
            <Typography variant="h4" gutterBottom>
                Create New Course
            </Typography>
            <Button
                variant="outlined"
                sx={{ mb: 2 }}
                onClick={() => navigate(-1)}
            >
                Back to Course
            </Button>
            <form onSubmit={handleSubmit}>
                <TextField
                    label="Title"
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                    fullWidth
                    required
                    sx={{ mb: 2 }}
                />
                <TextField
                    label="Description"
                    value={description}
                    onChange={(e) => setDescription(e.target.value)}
                    fullWidth
                    multiline
                    rows={4}
                    sx={{ mb: 2 }}
                />
                {error && (
                    <Typography color="error" sx={{ mb: 2 }}>
                        {error}
                    </Typography>
                )}
                <Button type="submit" variant="contained" disabled={loading}>
                    {loading ? <CircularProgress size={24} /> : "Create Course"}
                </Button>
            </form>
        </Container>
    );
};

export default CreateCourse;
