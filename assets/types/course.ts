import { User } from "@/types/auth";

export interface Course {
    id: number;
    title: string;
    description: string;
    lessons?: Lesson[];
    instructor: User;
}

export interface Lesson {
    id: number;
    title: string;
    content: string;
}
