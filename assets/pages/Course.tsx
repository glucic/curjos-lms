import React from "react";
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
import { useParams, useNavigate } from "react-router-dom";
import { useAuthContext } from "@/context/AuthContext";
import { useCourseData } from "@/hooks/use-course";
import { Lesson } from "@/types/course";

const Course: React.FC = () => {
    const { courseId } = useParams<{ courseId: string }>();
    const navigate = useNavigate();
    const { user } = useAuthContext();
    const isNotStudent = user && !user.roles.includes("ROLE_STUDENT");

    const { course, loading, error } = useCourseData(
        courseId ? parseInt(courseId) : undefined
    );

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

    if (error || !course) {
        return (
            <Container maxWidth="lg">
                <Typography variant="h4" align="center" sx={{ mt: 10 }}>
                    Course does not exist.
                </Typography>
            </Container>
        );
    }

    return (
        <Container maxWidth="lg">
            <Typography variant="h4" sx={{ mt: 8, mb: 2 }}>
                {course.title} by{" "}
                {course.instructorName || "Unknown Instructor"}
            </Typography>
            <Typography variant="body1" sx={{ mb: 4 }}>
                {course.description}
            </Typography>

            <Box sx={{ display: "flex", gap: 2 }}>
                <Button
                    variant="outlined"
                    sx={{ mb: 3 }}
                    onClick={() => navigate(-1)}
                >
                    Back to Homepage
                </Button>
                {isNotStudent && (
                    <Button
                        variant="contained"
                        sx={{ mb: 3 }}
                        onClick={() =>
                            navigate(`/course/${course.id}/lesson/create`)
                        }
                    >
                        + New Lesson
                    </Button>
                )}
            </Box>

            {course.lessons && course.lessons.length > 0 ? (
                <List>
                    {course.lessons.map((lesson: Lesson) => (
                        <ListItem key={lesson.id} disableGutters>
                            <Card sx={{ width: "100%", mb: 2 }}>
                                <CardContent>
                                    <Typography variant="h5">
                                        {lesson.title}
                                    </Typography>
                                    <Divider sx={{ my: 1 }} />
                                    <Typography
                                        variant="body2"
                                        color="text.secondary"
                                    >
                                        {lesson.description || "No description"}
                                    </Typography>
                                    {lesson.difficulty > 0 && (
                                        <Typography
                                            variant="body2"
                                            color="text.secondary"
                                        >
                                            Difficulty:{" "}
                                            {Array.from(
                                                { length: lesson.difficulty },
                                                (_, i) => (
                                                    <span key={i}>⭐</span>
                                                )
                                            )}
                                        </Typography>
                                    )}
                                </CardContent>
                                <CardContent>
                                    <Button
                                        size="small"
                                        onClick={() =>
                                            navigate(
                                                `/course/${course.id}/lesson/${lesson.id}`
                                            )
                                        }
                                    >
                                        View Lesson
                                    </Button>
                                </CardContent>
                            </Card>
                        </ListItem>
                    ))}
                </List>
            ) : (
                <Typography variant="h6" color="text.secondary" align="center">
                    No lessons available for this course.
                </Typography>
            )}
        </Container>
    );
};

export default Course;
