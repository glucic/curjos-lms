import { useEffect, useState } from "react";
import { courseApi } from "@/api/client";
import { Course } from "@/types/course";

export const useCourse = (courseId?: number) => {
    const [data, setData] = useState<Course[] | Course | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = courseId
                    ? await courseApi.getCourse(courseId)
                    : await courseApi.getCourses();
                setData(response.data.data);
            } catch (err) {
                setError("Failed to fetch data");
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, [courseId]);

    return { data, loading, error };
};
