import React from "react";
import { Routes, Route } from "react-router-dom";
import Layout from "@/components/Layout";
import Home from "@/pages/Home";
import Login from "@/pages/auth/Login";
import Register from "@/pages/auth/Register";
import Course from "@/pages/Course";
import CreateCourse from "@/pages/CreateCourse";
import CreateLesson from "@/pages/CreateLesson";
import Lesson from "@/pages/Lesson";
import RoleGuard from "@/RoleGuard";
import { Edit } from "@mui/icons-material";
import EditCourse from "./pages/EditCourse";
import EditLesson from "./pages/EditLesson";

const App: React.FC = () => {
    return (
        <Layout>
            <Routes>
                <Route path="/" element={<Home />} />
                <Route path="/login" element={<Login />} />
                <Route path="/register" element={<Register />} />
                <Route
                    path="/course/:courseId"
                    element={
                        <RoleGuard
                            allowedRoles={[
                                "ROLE_STUDENT",
                                "ROLE_INSTRUCTOR",
                                "ROLE_ADMIN",
                                "ROLE_SUPER_ADMIN",
                            ]}
                        >
                            <Course />
                        </RoleGuard>
                    }
                />
                <Route
                    path="/course/create"
                    element={
                        <RoleGuard
                            allowedRoles={[
                                "ROLE_INSTRUCTOR",
                                "ROLE_ADMIN",
                                "ROLE_SUPER_ADMIN",
                            ]}
                        >
                            <CreateCourse />
                        </RoleGuard>
                    }
                />
                <Route
                    path="/course/:courseId/edit"
                    element={
                        <RoleGuard
                            allowedRoles={[
                                "ROLE_INSTRUCTOR",
                                "ROLE_ADMIN",
                                "ROLE_SUPER_ADMIN",
                            ]}
                        >
                            <EditCourse />
                        </RoleGuard>
                    }
                />
                <Route
                    path="/course/:courseId/lesson/:lessonId"
                    element={
                        <RoleGuard
                            allowedRoles={[
                                "ROLE_STUDENT",
                                "ROLE_INSTRUCTOR",
                                "ROLE_ADMIN",
                                "ROLE_SUPER_ADMIN",
                            ]}
                        >
                            <Lesson />
                        </RoleGuard>
                    }
                />
                <Route
                    path="/course/:courseId/lesson/create"
                    element={
                        <RoleGuard
                            allowedRoles={[
                                "ROLE_INSTRUCTOR",
                                "ROLE_ADMIN",
                                "ROLE_SUPER_ADMIN",
                            ]}
                        >
                            <CreateLesson />
                        </RoleGuard>
                    }
                />
                <Route
                    path="/course/:courseId/lesson/:lessonId/edit"
                    element={
                        <RoleGuard
                            allowedRoles={[
                                "ROLE_INSTRUCTOR",
                                "ROLE_ADMIN",
                                "ROLE_SUPER_ADMIN",
                            ]}
                        >
                            <EditLesson />
                        </RoleGuard>
                    }
                />
            </Routes>
        </Layout>
    );
};

export default App;
