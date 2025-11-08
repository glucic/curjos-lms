import React, { useEffect, useState } from "react";
import {
    Box,
    Typography,
    List,
    ListItem,
    ListItemText,
    CircularProgress,
    Card,
    CardContent,
    CardActions,
    Button,
} from "@mui/material";
import { useNavigate } from "react-router-dom";
import { useCourse } from "@/hooks/use-course";

const CoursesList: React.FC = () => {
    const { data: courses, loading, error } = useCourse();
    const navigate = useNavigate();

    if (loading) {
        return <CircularProgress />;
    }

    if (error) {
        return <Typography>{error}</Typography>;
    }

    if (!Array.isArray(courses) || courses.length === 0) {
        return <Typography>No courses available.</Typography>;
    }

    return (
        <Box sx={{ height: "100%", width: "100%", mt: 5 }}>
            <Typography variant="h4" align="center" gutterBottom>
                Available Courses
            </Typography>
            <List>
                {courses.map((course) => (
                    <ListItem key={course.id} disableGutters>
                        <Card sx={{ mb: 2 }}>
                            <CardContent>
                                <Typography variant="h6" component="div">
                                    {course.title}
                                </Typography>
                                <Typography
                                    variant="body2"
                                    color="text.secondary"
                                >
                                    {course.description}
                                </Typography>
                                <Typography
                                    variant="body2"
                                    color="text.secondary"
                                >
                                    {course.difficulty ? (
                                        <>
                                            Difficulty Level:{" "}
                                            {Array.from(
                                                { length: course.difficulty },
                                                (_, i) => (
                                                    <span key={i}>⭐</span>
                                                )
                                            )}
                                        </>
                                    ) : (
                                        ""
                                    )}
                                </Typography>
                            </CardContent>
                            <CardActions>
                                <Button
                                    size="small"
                                    color="primary"
                                    onClick={() =>
                                        navigate(`/course/${course.id}`)
                                    }
                                >
                                    View Lessons
                                </Button>
                            </CardActions>
                        </Card>
                    </ListItem>
                ))}
            </List>
        </Box>
    );
};

export default CoursesList;
