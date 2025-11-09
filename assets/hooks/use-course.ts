import { useState, useEffect, useCallback } from "react";
import { courseApi, ApiError } from "@/api/client";
import { Course, Lesson } from "@/types/course";

export const useCourseData = (courseId?: number, lessonId?: number) => {
    const [courses, setCourses] = useState<Course[] | null>(null);
    const [course, setCourse] = useState<Course | null>(null);
    const [lesson, setLesson] = useState<Lesson | null>(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const fetchCourses = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            const response = await courseApi.getCourses();
            setCourses(response.data.data);
        } catch (err) {
            const apiError = err as ApiError;
            setError(apiError?.message || "Failed to fetch courses");
        } finally {
            setLoading(false);
        }
    }, []);

    const fetchCourse = useCallback(async (id: number) => {
        setLoading(true);
        setError(null);
        try {
            const response = await courseApi.getCourse(id);
            setCourse(response.data.data);
        } catch (err) {
            const apiError = err as ApiError;
            setError(apiError?.message || "Failed to fetch course");
        } finally {
            setLoading(false);
        }
    }, []);

    const updateCourse = useCallback(
        async (id: number, data: { title?: string; description?: string }) => {
            setLoading(true);
            setError(null);
            try {
                const response = await courseApi.updateCourse(id, data);
                setCourse(response.data.data);
                if (courses) {
                    setCourses(
                        courses.map((c) =>
                            c.id === id ? response.data.data : c
                        )
                    );
                }
                return response.data.data;
            } catch (err) {
                const apiError = err as ApiError;
                setError(apiError?.message || "Failed to update course");
                throw err;
            } finally {
                setLoading(false);
            }
        },
        [courses]
    );

    const deleteCourse = useCallback(
        async (id: number) => {
            setLoading(true);
            setError(null);
            try {
                await courseApi.deleteCourse(id);
                if (courses) {
                    setCourses(courses.filter((c) => c.id !== id));
                }
                if (course?.id === id) {
                    setCourse(null);
                }
            } catch (err) {
                const apiError = err as ApiError;
                setError(apiError?.message || "Failed to delete course");
                throw err;
            } finally {
                setLoading(false);
            }
        },
        [courses, course]
    );

    const fetchLesson = useCallback(
        async (courseId: number, lessonId: number) => {
            setLoading(true);
            setError(null);
            try {
                const response = await courseApi.getLesson(courseId, lessonId);
                setLesson(response.data.data);
            } catch (err) {
                const apiError = err as ApiError;
                setError(apiError?.message || "Failed to fetch lesson");
            } finally {
                setLoading(false);
            }
        },
        []
    );

    const createCourse = useCallback(
        async (data: { title: string; description: string }) => {
            setLoading(true);
            setError(null);
            try {
                const response = await courseApi.createCourse(data);
                setCourse(response.data.data);
                return response.data.data;
            } catch (err) {
                const apiError = err as ApiError;
                setError(apiError?.message || "Failed to create course");
                throw err;
            } finally {
                setLoading(false);
            }
        },
        []
    );

    const createLesson = useCallback(
        async (
            courseId: number,
            data: {
                title: string;
                description?: string;
                difficulty?: number;
                type: string;
                resourceUrl?: string;
            }
        ) => {
            setLoading(true);
            setError(null);
            try {
                const response = await courseApi.createLesson(courseId, data);
                if (course) {
                    setCourse({
                        ...course,
                        lessons: [
                            ...(course.lessons || []),
                            response.data.data,
                        ],
                        lessonsCount: (course.lessonsCount || 0) + 1,
                    });
                }
                return response.data.data;
            } catch (err) {
                const apiError = err as ApiError;
                setError(apiError?.message || "Failed to create lesson");
                throw err;
            } finally {
                setLoading(false);
            }
        },
        [course]
    );

    const updateLesson = useCallback(
        async (
            courseId: number,
            lessonId: number,
            data: {
                title?: string;
                description?: string;
                difficulty?: number;
                type?: string;
                resourceUrl?: string;
            }
        ) => {
            setLoading(true);
            setError(null);
            try {
                const response = await courseApi.updateLesson(
                    courseId,
                    lessonId,
                    data
                );
                setLesson(response.data.data);

                // update lesson in course if available
                if (course?.lessons) {
                    setCourse({
                        ...course,
                        lessons: course.lessons.map((l) =>
                            l.id === lessonId ? response.data.data : l
                        ),
                    });
                }
                return response.data.data;
            } catch (err) {
                const apiError = err as ApiError;
                setError(apiError?.message || "Failed to update lesson");
                throw err;
            } finally {
                setLoading(false);
            }
        },
        [course]
    );

    const deleteLesson = useCallback(
        async (courseId: number, lessonId: number) => {
            setLoading(true);
            setError(null);
            try {
                await courseApi.deleteLesson(courseId, lessonId);
                if (course?.lessons) {
                    setCourse({
                        ...course,
                        lessons: course.lessons.filter(
                            (l) => l.id !== lessonId
                        ),
                        lessonsCount: (course.lessonsCount || 1) - 1,
                    });
                }
                if (lesson?.id === lessonId) {
                    setLesson(null);
                }
            } catch (err) {
                const apiError = err as ApiError;
                setError(apiError?.message || "Failed to delete lesson");
                throw err;
            } finally {
                setLoading(false);
            }
        },
        [course, lesson]
    );

    useEffect(() => {
        if (lessonId && courseId) {
            fetchLesson(courseId, lessonId);
        } else if (courseId) {
            fetchCourse(courseId);
        } else {
            fetchCourses();
        }
    }, [courseId, lessonId, fetchCourse, fetchCourses, fetchLesson]);

    return {
        courses,
        course,
        lesson,
        loading,
        error,
        fetchCourses,
        fetchCourse,
        updateCourse,
        deleteCourse,
        fetchLesson,
        createCourse,
        createLesson,
        updateLesson,
        deleteLesson,
    };
};
