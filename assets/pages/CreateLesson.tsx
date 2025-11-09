import React, { useState } from "react";
import {
    Container,
    Typography,
    TextField,
    Button,
    Select,
    MenuItem,
    CircularProgress,
    FormControl,
    InputLabel,
} from "@mui/material";
import { useNavigate, useParams } from "react-router-dom";
import { useCourseData } from "@/hooks/use-course";

const difficulties = [0, 1, 2, 3, 4, 5];
const lessonTypes = ["Video", "Article", "Quiz", "Assignment"];

const CreateLesson: React.FC = () => {
    const { courseId } = useParams<{ courseId: string }>();
    const navigate = useNavigate();
    const { createLesson, loading } = useCourseData();

    const [title, setTitle] = useState("");
    const [description, setDescription] = useState("");
    const [difficulty, setDifficulty] = useState(0);
    const [type, setType] = useState("");
    const [resourceUrl, setResourceUrl] = useState("");
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);

        if (!courseId) return setError("Course ID is missing");

        try {
            await createLesson(parseInt(courseId), {
                title,
                description,
                difficulty,
                type,
                resourceUrl,
            });
            navigate(`/course/${courseId}`);
        } catch (err) {
            setError("Failed to create lesson");
        }
    };

    return (
        <Container maxWidth="sm" sx={{ mt: 8 }}>
            <Typography variant="h4" gutterBottom>
                Create New Lesson
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
                <FormControl fullWidth sx={{ mb: 2 }}>
                    <InputLabel>Difficulty</InputLabel>
                    <Select
                        value={difficulty}
                        onChange={(e) => setDifficulty(Number(e.target.value))}
                    >
                        {difficulties.map((d) => (
                            <MenuItem key={d} value={d}>
                                {d}
                            </MenuItem>
                        ))}
                    </Select>
                </FormControl>
                <FormControl fullWidth sx={{ mb: 2 }}>
                    <InputLabel>Type</InputLabel>
                    <Select
                        value={type}
                        onChange={(e) => setType(e.target.value)}
                        required
                    >
                        {lessonTypes.map((t) => (
                            <MenuItem key={t} value={t}>
                                {t}
                            </MenuItem>
                        ))}
                    </Select>
                </FormControl>
                <TextField
                    label="Resource URL"
                    value={resourceUrl}
                    onChange={(e) => setResourceUrl(e.target.value)}
                    fullWidth
                    sx={{ mb: 2 }}
                />
                {error && (
                    <Typography color="error" sx={{ mb: 2 }}>
                        {error}
                    </Typography>
                )}
                <Button type="submit" variant="contained" disabled={loading}>
                    {loading ? <CircularProgress size={24} /> : "Create Lesson"}
                </Button>
            </form>
        </Container>
    );
};

export default CreateLesson;
