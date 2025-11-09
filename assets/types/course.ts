export interface Course {
    id: number;
    title: string;
    description: string;
    lessons?: Lesson[];
    instructorName?: string;
    difficulty: number;
    lessonsCount: number;
}

export interface Lesson {
    id: number;
    title: string;
    description: string;
    difficulty: number;
    resourceUrl?: string;
    type: string;
}
