import React from "react";
import { Routes, Route } from "react-router-dom";
import Layout from "@/components/Layout";
import Home from "@/pages/Home";
import Login from "@/pages/auth/Login";
import Course from "@/pages/Course";
import CreateCourse from "@/pages/CreateCourse";
import CreateLesson from "@/pages/CreateLesson";
import Lesson from "@/pages/Lesson";
import RoleGuard from "@/RoleGuard";
import EditCourse from "./pages/EditCourse";
import EditLesson from "./pages/EditLesson";
import SuperAdminPanel from "./pages/SuperAdminPanel";
import Organization from "./pages/Organization";
import EditOrganization from "./pages/EditOrganization";
import { Create } from "@mui/icons-material";
import CreateOrganization from "./pages/CreateOrganization";
import EditUser from "./pages/EditUser";
import CreateUser from "./pages/CreateUser";

const App: React.FC = () => {
    return (
        <Layout>
            <Routes>
                <Route path="/" element={<Home />} />
                <Route path="/login" element={<Login />} />
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
                <Route
                    path="/super"
                    element={
                        <RoleGuard allowedRoles={["ROLE_SUPER_ADMIN"]}>
                            <SuperAdminPanel />
                        </RoleGuard>
                    }
                />
                <Route
                    path="/organization/:id"
                    element={
                        <RoleGuard
                            allowedRoles={["ROLE_SUPER_ADMIN", "ROLE_ADMIN"]}
                        >
                            <Organization />
                        </RoleGuard>
                    }
                />
                <Route
                    path="/organization/:id/edit"
                    element={
                        <RoleGuard allowedRoles={["ROLE_SUPER_ADMIN"]}>
                            <EditOrganization />
                        </RoleGuard>
                    }
                />
                <Route
                    path="/organization/create"
                    element={
                        <RoleGuard allowedRoles={["ROLE_SUPER_ADMIN"]}>
                            <CreateOrganization />
                        </RoleGuard>
                    }
                />
                <Route
                    path="organization/:organizationId/user/create"
                    element={
                        <RoleGuard allowedRoles={["ROLE_ADMIN", "ROLE_SUPER_ADMIN"]}>
                            <CreateUser />
                        </RoleGuard>
                    }
                />
                <Route
                    path="organization/:organizationId/user/:userId/edit"
                    element={
                        <RoleGuard allowedRoles={["ROLE_ADMIN", "ROLE_SUPER_ADMIN"]}>
                            <EditUser />
                        </RoleGuard>
                    }
                />
            </Routes>
        </Layout>
    );
};

export default App;
