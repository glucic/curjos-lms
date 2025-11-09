import { useState, useEffect, useCallback } from "react";
import { courseApi, ApiError } from "@/api/client";
import { Course, Lesson } from "@/types/course";

export const useCourseData = (courseId?: number) => {
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

    useEffect(() => {
        if (courseId) fetchCourse(courseId);
        else fetchCourses();
    }, [courseId, fetchCourse, fetchCourses]);

    return {
        courses,
        course,
        lesson,
        loading,
        error,
        fetchCourses,
        fetchCourse,
        fetchLesson,
        createCourse,
        createLesson,
    };
};
