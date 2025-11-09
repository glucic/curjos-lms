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
    loading: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider = ({ children }: { children: ReactNode }) => {
    const [user, setUser] = useState<AuthResult["user"] | null>(null);
    const [token, setToken] = useState<string | null>(null);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        const storedToken = localStorage.getItem("jwt_token");
        if (storedToken) setToken(storedToken);
    }, []);

    useEffect(() => {
        if (token) {
            fetchUser();
        }
    }, [token]);

    const fetchUser = async () => {
        if (!token) {
            setLoading(false);
            return;
        }
        setLoading(true);
        try {
            const response = await authApi.me();
            setUser(response.data);
        } catch (err) {
            setUser(null);
        } finally {
            setLoading(false);
        }
    };

    const login = async (result: AuthResult) => {
        localStorage.setItem("jwt_token", result.token);
        setToken(result.token);
    };

    const logout = () => {
        setToken(null);
        setUser(null);
        localStorage.removeItem("jwt_token");
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
                loading,
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
