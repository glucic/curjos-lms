import { User } from "@/types/auth";

export interface Course {
    id: number;
    title: string;
    description: string;
    lessons?: Lesson[];
    instructor: User;
    difficulty?: number;
}

export interface Lesson {
    id: number;
    title: string;
    content: string;
    difficulty: number;
}
