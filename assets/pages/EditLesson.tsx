import React, { useState, useEffect } from "react";
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
import { ArrowBack as ArrowBackIcon } from "@mui/icons-material";
import { useNavigate, useParams } from "react-router-dom";
import { useCourseData } from "@/hooks/use-course";

const difficulties = [0, 1, 2, 3, 4, 5];
const lessonTypes = ["Video", "Article", "Quiz", "Assignment"];

const EditLesson: React.FC = () => {
    const { courseId, lessonId } = useParams<{
        courseId: string;
        lessonId: string;
    }>();
    const navigate = useNavigate();
    const {
        lesson,
        loading,
        error: fetchError,
        updateLesson,
        fetchLesson,
    } = useCourseData(
        courseId ? parseInt(courseId) : undefined,
        lessonId ? parseInt(lessonId) : undefined
    );

    const [title, setTitle] = useState("");
    const [description, setDescription] = useState("");
    const [difficulty, setDifficulty] = useState(0);
    const [type, setType] = useState("");
    const [resourceUrl, setResourceUrl] = useState("");
    const [error, setError] = useState<string | null>(null);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        if (courseId && lessonId) {
            fetchLesson(parseInt(courseId), parseInt(lessonId));
        }
    }, [courseId, lessonId, fetchLesson]);

    useEffect(() => {
        if (lesson) {
            setTitle(lesson.title);
            setDescription(lesson.description || "");
            setDifficulty(lesson.difficulty || 0);
            setType(lesson.type || "");
            setResourceUrl(lesson.resourceUrl || "");
        }
    }, [lesson]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);

        if (!courseId || !lessonId)
            return setError("Course ID or Lesson ID is missing");

        try {
            setSaving(true);
            await updateLesson(parseInt(courseId), parseInt(lessonId), {
                title,
                description,
                difficulty,
                type,
                resourceUrl,
            });
            navigate(`/course/${courseId}`);
        } catch (err) {
            setError("Failed to update lesson");
        } finally {
            setSaving(false);
        }
    };

    if (loading || !lesson) {
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
                Edit Lesson
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
                <Button type="submit" variant="contained" disabled={saving}>
                    {saving ? <CircularProgress size={24} /> : "Save Changes"}
                </Button>
            </form>
        </Container>
    );
};

export default EditLesson;
