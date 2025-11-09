import React from "react";
import {
    Box,
    Typography,
    Grid,
    Card,
    CardContent,
    CardActions,
    Button,
    CircularProgress,
    Container,
    Link,
} from "@mui/material";
import { useNavigate } from "react-router-dom";
import { useCourseData } from "@/hooks/use-course";
import { useAuthContext } from "@/context/AuthContext";
import { Course } from "@/types/course";

const CoursesList: React.FC = () => {
    const { courses, loading, error } = useCourseData();
    const navigate = useNavigate();
    const { user } = useAuthContext();
    const isNotStudent =
        user && user.roles && !user.roles.includes("ROLE_STUDENT");

    if (loading)
        return (
            <Container
                maxWidth="sm"
                sx={{ display: "flex", justifyContent: "center", mt: 4 }}
            >
                <CircularProgress />
            </Container>
        );

    if (!courses || courses.length === 0 || error)
        return (
            <Container maxWidth="lg">
                <Typography variant="h4" align="center" sx={{ mt: 10 }}>
                    No courses available for your Organization{" "}
                    {user?.organization?.name}.
                </Typography>
                {isNotStudent && (
                    <Box sx={{ textAlign: "center", mt: 5 }}>
                        <Button
                            variant="outlined"
                            size="large"
                            color="primary"
                            onClick={() => navigate("/course/create")}
                        >
                            Start by creating your first course.
                        </Button>
                    </Box>
                )}
            </Container>
        );

    return (
        <Box sx={{ width: "100%", mt: 4 }}>
            <Grid
                container
                justifyContent="center"
                alignItems="center"
                sx={{ px: { xs: 2, sm: 4, md: 6 }, mb: 2 }}
                gap={10}
            >
                <Typography variant="h5" fontWeight="bold">
                    Courses
                </Typography>

                {isNotStudent && (
                    <Button
                        variant="contained"
                        color="primary"
                        onClick={() => navigate("/course/create")}
                    >
                        + New Course
                    </Button>
                )}
            </Grid>

            <Grid
                container
                spacing={3}
                justifyContent="center"
                sx={{ px: { xs: 2, sm: 4, md: 6 } }}
            >
                {courses.map((course: Course) => (
                    // @ts-ignore
                    <Grid item xs={12} sm={6} md={4} key={course.id}>
                        <Card
                            sx={{
                                height: 200,
                                display: "flex",
                                flexDirection: "column",
                                justifyContent: "space-between",
                                overflowX: "auto",
                            }}
                        >
                            <CardContent>
                                <Typography variant="h6">
                                    {course.title} by{" "}
                                    {course.instructorName ||
                                        "Unknown Instructor"}
                                </Typography>

                                <Typography
                                    variant="body2"
                                    color="text.secondary"
                                    sx={{ mt: 1 }}
                                >
                                    {course.description || "No description"}
                                </Typography>

                                {course.difficulty > 0 && (
                                    <Typography
                                        variant="body2"
                                        color="text.secondary"
                                        sx={{ mt: 1 }}
                                    >
                                        Difficulty:{" "}
                                        {Array.from(
                                            { length: course.difficulty },
                                            (_, i) => (
                                                <span key={i}>⭐</span>
                                            )
                                        )}
                                    </Typography>
                                )}

                                {course.lessonsCount > 0 ? (
                                    <Typography
                                        variant="body2"
                                        color="text.secondary"
                                        sx={{ mt: 1 }}
                                    >
                                        Lessons: {course.lessonsCount}
                                    </Typography>
                                ) : (
                                    <Typography
                                        variant="body2"
                                        color="text.secondary"
                                        sx={{ mt: 1 }}
                                    >
                                        No lessons yet
                                    </Typography>
                                )}
                            </CardContent>

                            <CardActions>
                                <Button
                                    size="small"
                                    onClick={() =>
                                        navigate(`/course/${course.id}`)
                                    }
                                >
                                    View Course
                                </Button>
                            </CardActions>
                        </Card>
                    </Grid>
                ))}
            </Grid>
        </Box>
    );
};

export default CoursesList;
