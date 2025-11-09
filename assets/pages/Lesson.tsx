import React, { useEffect } from "react";
import {
    Container,
    Typography,
    CircularProgress,
    Divider,
    Button,
    Box,
} from "@mui/material";
import { useParams, useNavigate } from "react-router-dom";
import { useCourseData } from "@/hooks/use-course";

const Lesson: React.FC = () => {
    const { courseId, lessonId } = useParams<{
        courseId: string;
        lessonId: string;
    }>();
    const navigate = useNavigate();
    const { lesson, fetchLesson, loading, error } = useCourseData();

    useEffect(() => {
        if (courseId && lessonId)
            fetchLesson(parseInt(courseId), parseInt(lessonId));
    }, [courseId, lessonId, fetchLesson]);

    if (loading) return <CircularProgress />;
    if (error)
        return (
            <Container>
                <Typography color="error">{error}</Typography>
            </Container>
        );
    if (!lesson) return null;

    return (
        <Container maxWidth="md" sx={{ mt: 8 }}>
            <Button
                variant="outlined"
                sx={{ mb: 2 }}
                onClick={() => navigate(-1)}
            >
                Back to Course
            </Button>

            <Typography variant="h4" gutterBottom>
                {lesson.title}
            </Typography>
            <Divider sx={{ my: 2 }} />

            {lesson.description && (
                <Typography variant="body1" sx={{ mb: 2 }}>
                    {lesson.description}
                </Typography>
            )}
            <Typography variant="body2" color="text.secondary" sx={{ mb: 1 }}>
                Type: {lesson.type}
            </Typography>

            {lesson.difficulty > 0 && (
                <Typography
                    variant="body2"
                    color="text.secondary"
                    sx={{ mb: 1 }}
                >
                    Difficulty:{" "}
                    {Array.from({ length: lesson.difficulty }, (_, i) => (
                        <span key={i}>⭐</span>
                    ))}
                </Typography>
            )}

            {lesson.resourceUrl && (
                <Box sx={{ mt: 2 }}>
                    <Typography variant="body2" color="text.secondary">
                        Resource:
                    </Typography>
                    <Button
                        href={lesson.resourceUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                        variant="contained"
                        sx={{ mt: 1 }}
                    >
                        Open Resource
                    </Button>
                </Box>
            )}
        </Container>
    );
};

export default Lesson;
