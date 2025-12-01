/**
 * Auth Context
 * Manages authentication state across the application
 */

import { createContext, useContext, useState, useEffect } from 'react';
import { authAPI } from '../services/api';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  // Logout function defined outside useEffect to avoid dependency issues
  const logout = () => {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    setUser(null);
  };

  useEffect(() => {
    // Restore user from localStorage on page load/refresh
    // NO API CALLS - just restore from localStorage
    const token = localStorage.getItem('auth_token');
    const savedUser = localStorage.getItem('auth_user');
    
    console.log('🔄 Page loaded - checking localStorage:', { 
      hasToken: !!token, 
      hasUser: !!savedUser 
    });
    
    if (token && savedUser) {
      try {
        const parsedUser = JSON.parse(savedUser);
        console.log('✅ Restoring user from localStorage:', parsedUser.email);
        
        // Set user state IMMEDIATELY - no API calls that could fail
        setUser(parsedUser);
        setLoading(false);
        
        // NO token verification on init - keep user logged in always
        // Token will be verified automatically on next API call
      } catch (e) {
        console.error('❌ Error parsing saved user:', e);
        // Only clear if data is corrupted
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        setUser(null);
        setLoading(false);
      }
    } else {
      console.log('ℹ️ No saved auth found in localStorage');
      setUser(null);
      setLoading(false);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const login = async (email, password) => {
    try {
      const response = await authAPI.login(email, password);
      
      if (response.success && response.data) {
        // Backend returns: { success: true, data: { success: true, user: {...}, token: "..." } }
        // BaseController wraps AuthService result in 'data', so it's nested
        const data = response.data;
        const token = data.token;
        const user = data.user;
        
        if (token && user) {
          // Save to localStorage FIRST
          localStorage.setItem('auth_token', token);
          localStorage.setItem('auth_user', JSON.stringify(user));
          console.log('✅ Login successful, saved to localStorage:', user.email);
          
          // Then set state
          setUser(user);
          return { success: true };
        } else {
          console.error('Missing token or user in login response:', response);
          return { success: false, message: 'Login succeeded but token/user missing' };
        }
      }
      return { success: false, message: response.message || 'Login failed' };
    } catch (error) {
      console.error('Login error:', error);
      const errorMessage = error.response?.message || error.message || 'Login failed';
      return { success: false, message: errorMessage };
    }
  };

  const register = async (data) => {
    try {
      const response = await authAPI.register(data);
      
      if (response.success && response.data) {
        // Backend returns: { success: true, data: { success: true, user: {...}, token: "..." } }
        // BaseController wraps AuthService result in 'data', so it's nested
        const dataObj = response.data;
        const token = dataObj.token;
        const user = dataObj.user;
        
        if (token && user) {
          // Save to localStorage FIRST
          localStorage.setItem('auth_token', token);
          localStorage.setItem('auth_user', JSON.stringify(user));
          console.log('✅ Registration successful, saved to localStorage:', user.email);
          
          // Then set state
          setUser(user);
          return { success: true };
        } else {
          console.error('Missing token or user in register response:', response);
          return { success: false, message: 'Registration succeeded but authentication failed' };
        }
      }
      return { success: false, message: response.message || 'Registration failed' };
    } catch (error) {
      console.error('Registration error:', error);
      const errorMessage = error.response?.message || error.message || error.errors?.[0] || 'Registration failed';
      return { success: false, message: errorMessage };
    }
  };

  const isAuthenticated = () => !!user;

  const isAdmin = () => user?.role_name === 'admin';
  
  const isStaff = () => user?.role_name === 'staff' || isAdmin();

  return (
    <AuthContext.Provider
      value={{
        user,
        loading,
        login,
        register,
        logout,
        isAuthenticated,
        isAdmin,
        isStaff,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within AuthProvider');
  }
  return context;
};

