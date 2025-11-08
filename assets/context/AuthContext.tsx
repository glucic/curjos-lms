import React, {
    createContext,
    useContext,
    useState,
    useEffect,
    ReactNode,
} from "react";
import { AuthResult } from "@/types/auth";
import { authApi } from "@/api/client";

interface AuthContextType {
    user: AuthResult["user"] | null;
    token: string | null;
    login: (result: AuthResult) => void;
    logout: () => void;
    isAuthenticated: boolean;
    fetchUser: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider = ({ children }: { children: ReactNode }) => {
    const [user, setUser] = useState<AuthResult["user"] | null>(null);
    const [token, setToken] = useState<string | null>(null);

    useEffect(() => {
        const storedToken = localStorage.getItem("jwt_token");
        const storedUser = localStorage.getItem("authUser");
        if (storedToken) setToken(storedToken);
        if (storedUser) {
            setUser(JSON.parse(storedUser));
        }
    }, []);

    useEffect(() => {
        if (token && !user) {
            fetchUser();
        }
    }, [token]);

    const fetchUser = async () => {
        if (!token) return;
        try {
            const response = await authApi.me();
            setUser(response.data);
            localStorage.setItem("authUser", JSON.stringify(response.data));
        } catch (err) {
            setUser(null);
            localStorage.removeItem("authUser");
        }
    };

    const login = async (result: AuthResult) => {
        localStorage.setItem("jwt_token", result.token);
        setToken(result.token);
        await fetchUser();
    };

    const logout = () => {
        setToken(null);
        setUser(null);
        localStorage.removeItem("jwt_token");
        localStorage.removeItem("authUser");
    };

    return (
        <AuthContext.Provider
            value={{
                user,
                token,
                login,
                logout,
                isAuthenticated: !!token,
                fetchUser,
            }}
        >
            {children}
        </AuthContext.Provider>
    );
};

export const useAuthContext = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error("useAuthContext must be used within an AuthProvider");
    }
    return context;
};
