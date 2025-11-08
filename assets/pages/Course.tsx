import React from "react";
import {
    Box,
    Typography,
    List,
    ListItem,
    CircularProgress,
    Card,
    CardContent,
    Divider,
    Container,
} from "@mui/material";
import { useParams } from "react-router-dom";
import { useCourse } from "@/hooks/use-course";
import { Lesson } from "@/types/course";

const Course: React.FC = () => {
    const { id } = useParams<{ id: string }>();
    const { data: courseData, loading, error } = useCourse(parseInt(id!));

    if (loading) {
        return <CircularProgress />;
    }
    const course = Array.isArray(courseData) ? null : courseData;

    if (error || !course || !course.lessons || course.lessons.length === 0) {
        return (
            <Container maxWidth="lg">
                <Typography
                    variant="h4"
                    gutterBottom
                    align="center"
                    sx={{ mt: 10 }}
                >
                    No lessons available for this course.
                </Typography>
            </Container>
        );
    }

    return (
        <Container maxWidth="lg">
            <Box
                sx={{
                    mt: 8,
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center",
                }}
            >
                <Typography variant="h4" gutterBottom>
                    {course.title} by {course.instructor.firstName}{" "}
                    {course.instructor.lastName}
                </Typography>
                <Typography variant="body1" gutterBottom>
                    {course.description}
                </Typography>
            </Box>
            <List sx={{ width: "100%" }}>
                {course?.lessons?.map((lesson: Lesson) => (
                    <ListItem key={lesson.id} disableGutters>
                        <Card sx={{ width: "100%", mb: 2 }}>
                            <CardContent>
                                <Typography
                                    variant="h5"
                                    component="div"
                                    gutterBottom
                                >
                                    {lesson.title}
                                </Typography>
                                <Divider sx={{ my: 1 }} />
                                <Typography
                                    variant="body1"
                                    color="text.secondary"
                                >
                                    {lesson.content}
                                </Typography>
                            </CardContent>
                        </Card>
                    </ListItem>
                ))}
            </List>
        </Container>
    );
};

export default Course;
