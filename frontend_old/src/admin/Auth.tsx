import {
  createContext, ReactNode, useCallback, useContext, useMemo, useState,
} from 'react';
import {
  login as userLogIn,
  logout as userLogout,
  isLoggedIn as userIsLoggedIn,
} from './User';

interface Authentication {
  login(name: string, password: string): Promise<boolean>
  logout(): void
  isLoggedIn(): boolean
}

const AuthContext = createContext({} as Authentication);

export function useAuthContext(): Authentication {
  return useContext(AuthContext);
}

export default function AuthProvider({ children }: {children: ReactNode}) {
  const [isAuthenticated, setAuthenticated] = useState<boolean>(userIsLoggedIn());

  const logout = useCallback((): void => {
    userLogout();

    setAuthenticated(false);
  }, []);

  const login = useCallback((name: string, password: string): Promise<boolean> => {
    logout();

    return userLogIn(name, password)
      .finally(() => setAuthenticated(userIsLoggedIn()));
  }, [logout]);

  const auth = useMemo(() => ({
    login,
    logout,
    isLoggedIn: () => isAuthenticated,
  }), [login, logout, isAuthenticated]);

  return (
    <AuthContext.Provider value={auth}>
      {children}
    </AuthContext.Provider>
  );
}
