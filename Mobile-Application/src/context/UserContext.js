import React, { createContext, useState, useEffect } from 'react';
import { getStoredUser, storeUser, clearUser } from '../services/auth';

export const UserContext = createContext();

export const UserProvider = ({ children }) => {
  const [user, setUser] = useState(null);

  useEffect(() => {
    const loadUser = async () => {
      const storedUser = await getStoredUser();
      if (storedUser) {
        setUser(storedUser);
      }
    };
    loadUser();
  }, []);

  const login = (userData) => {
    setUser(userData);
    storeUser(userData);
  };

  const logout = () => {
    setUser(null);
    clearUser();
  };

  return (
    <UserContext.Provider value={{ user, login, logout }}>
      {children}
    </UserContext.Provider>
  );
};