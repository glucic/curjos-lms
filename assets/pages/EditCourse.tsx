import React, { useState, useEffect } from "react";
import {
    Container,
    Typography,
    TextField,
    Button,
    CircularProgress,
} from "@mui/material";
import { ArrowBack as ArrowBackIcon } from "@mui/icons-material";
import { useNavigate, useParams } from "react-router-dom";
import { useCourseData } from "@/hooks/use-course";

const EditCourse: React.FC = () => {
    const navigate = useNavigate();
    const { courseId } = useParams<{ courseId: string }>();
    const {
        course,
        loading,
        error: fetchError,
        updateCourse,
        fetchCourse,
    } = useCourseData(courseId ? parseInt(courseId) : undefined);

    const [title, setTitle] = useState("");
    const [description, setDescription] = useState("");
    const [error, setError] = useState<string | null>(null);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        if (courseId) {
            fetchCourse(parseInt(courseId));
        }
    }, [courseId, fetchCourse]);

    useEffect(() => {
        if (course) {
            setTitle(course.title);
            setDescription(course.description || "");
        }
    }, [course]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);

        if (!courseId) return;

        try {
            setSaving(true);
            await updateCourse(parseInt(courseId), { title, description });
            navigate(`/course/${courseId}`);
        } catch (err) {
            setError("Failed to update course");
        } finally {
            setSaving(false);
        }
    };

    if (loading || !course) {
        return (
            <Container
                maxWidth="sm"
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

    if (fetchError) {
        return (
            <Container maxWidth="sm">
                <Typography color="error" sx={{ mt: 10, textAlign: "center" }}>
                    {fetchError}
                </Typography>
            </Container>
        );
    }

    return (
        <Container maxWidth="sm" sx={{ mt: 8 }}>
            <Typography variant="h4" gutterBottom>
                Edit Course
            </Typography>
            <Button
                variant="outlined"
                startIcon={<ArrowBackIcon />}
                sx={{ mb: 2 }}
                onClick={() => navigate(`/course/${courseId}`)}
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
                <Button type="submit" variant="contained" disabled={saving}>
                    {saving ? <CircularProgress size={24} /> : "Save Changes"}
                </Button>
            </form>
        </Container>
    );
};

export default EditCourse;
