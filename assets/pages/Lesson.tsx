import React from "react";
import {
    Container,
    Typography,
    Divider,
    Button,
    Box,
    CircularProgress,
} from "@mui/material";
import { useParams, useNavigate } from "react-router-dom";
import { useAuthContext } from "@/context/AuthContext";
import { useCourseData } from "@/hooks/use-course";
import {
    ArrowBack as ArrowBackIcon,
    Edit as EditIcon,
    Delete as DeleteIcon,
} from "@mui/icons-material";

const LessonPage: React.FC = () => {
    const { courseId, lessonId } = useParams<{
        courseId: string;
        lessonId: string;
    }>();
    const navigate = useNavigate();
    const { user } = useAuthContext();
    const isNotStudent = user && !user.roles.includes("ROLE_STUDENT");

    const { lesson, loading, error, deleteLesson } = useCourseData(
        Number(courseId),
        Number(lessonId)
    );

    const handleDelete = async () => {
        if (!courseId || !lessonId) return;
        try {
            await deleteLesson(Number(courseId), Number(lessonId));
            navigate(`/course/${courseId}`);
        } catch (err) {
            console.error(err);
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

    if (error || !lesson) {
        return (
            <Container maxWidth="lg">
                <Typography variant="h4" align="center" sx={{ mt: 10 }}>
                    Lesson does not exist.
                </Typography>
            </Container>
        );
    }

    return (
        <Container maxWidth="lg">
            <Typography variant="h4" sx={{ mt: 8, mb: 2 }}>
                {lesson.title}
            </Typography>
            <Divider sx={{ mb: 3 }} />
            <Typography variant="body1" sx={{ mb: 4 }}>
                {lesson.description || "No description available."}
            </Typography>

            <Box
                sx={{
                    display: "flex",
                    flexDirection: { xs: "column", sm: "row" },
                    justifyContent: "space-between",
                    alignItems: { xs: "stretch", sm: "center" },
                    gap: 2,
                    mb: 3,
                }}
            >
                <Button
                    variant="outlined"
                    color="primary"
                    startIcon={<ArrowBackIcon />}
                    onClick={() => navigate(`/course/${courseId}`)}
                    sx={{ px: 3, fontWeight: 500, textTransform: "none" }}
                >
                    Back to Course
                </Button>

                {isNotStudent && (
                    <Box sx={{ display: "flex", gap: 1.5 }}>
                        <Button
                            variant="outlined"
                            color="primary"
                            startIcon={<EditIcon />}
                            onClick={() =>
                                navigate(
                                    `/course/${courseId}/lesson/${lessonId}/edit`
                                )
                            }
                            sx={{
                                px: 3,
                                fontWeight: 500,
                                textTransform: "none",
                            }}
                        >
                            Edit Lesson
                        </Button>

                        <Button
                            variant="contained"
                            color="error"
                            startIcon={<DeleteIcon />}
                            onClick={handleDelete}
                            sx={{
                                px: 3,
                                fontWeight: 600,
                                textTransform: "none",
                            }}
                        >
                            Delete Lesson
                        </Button>
                    </Box>
                )}
            </Box>

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

export default LessonPage;
