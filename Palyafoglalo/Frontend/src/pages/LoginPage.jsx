/**
 * Login Page
 * User authentication page
 */

import { useState, useEffect } from 'react';
import { useNavigate, useLocation, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { LogIn, Mail, Lock, Eye, EyeOff } from 'lucide-react';

const LoginPage = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { login, isAuthenticated } = useAuth();
  
  // Get intended destination from location state
  const from = location.state?.from || '/';
  const courtId = location.state?.courtId;
  const prefillData = location.state?.prefillData || location.state?.formData;

  // If already authenticated, redirect back
  useEffect(() => {
    if (isAuthenticated()) {
      if (courtId) {
        navigate(`/courts/${courtId}`, { replace: true });
      } else {
        navigate(from, { replace: true });
      }
    }
  }, [isAuthenticated, navigate, from, courtId]);
  
  const [formData, setFormData] = useState({
    email: '',
    password: '',
  });
  
  const [errors, setErrors] = useState([]);
  const [loading, setLoading] = useState(false);
  const [showPassword, setShowPassword] = useState(false);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
    setErrors([]);
  };

  const validateForm = () => {
    const newErrors = [];
    
    if (!formData.email.trim()) {
      newErrors.push('Az email cím kötelező');
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
      newErrors.push('Az email cím formátuma nem megfelelő');
    }
    
    if (!formData.password) {
      newErrors.push('A jelszó kötelező');
    }
    
    return newErrors;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErrors([]);
    
    // Client-side validation
    const validationErrors = validateForm();
    if (validationErrors.length > 0) {
      setErrors(validationErrors);
      return;
    }
    
    setLoading(true);

    const result = await login(formData.email, formData.password);

    if (result.success) {
      // Only clear form on successful login
      setFormData({ email: '', password: '' });
      
      // Redirect to intended destination or court detail page
      if (courtId) {
        navigate(`/book/${courtId}`, { 
          state: prefillData || {},
          replace: true 
        });
      } else {
        navigate(from, { replace: true });
      }
    } else {
      // Display backend error message (already in Hungarian)
      // Keep form data (email and password remain)
      setErrors([result.message || 'Bejelentkezés sikertelen']);
    }

    setLoading(false);
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-primary-50 via-white to-primary-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-md w-full">
        {/* Logo/Header */}
        <div className="text-center mb-8">
          <div className="mx-auto w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center mb-4">
            <LogIn className="h-8 w-8 text-white" />
          </div>
          <h1 className="text-3xl font-bold text-gray-900">Bejelentkezés</h1>
          <p className="mt-2 text-sm text-gray-600">
            Jelentkezz be a fiókodba
          </p>
        </div>

        {/* Form Card */}
        <div className="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
          <h2 className="text-xl font-semibold mb-6 text-center text-gray-800">Üdvözölünk vissza!</h2>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              <Mail className="h-4 w-4 inline mr-1" />
              Email cím *
            </label>
            <input
              type="email"
              name="email"
              value={formData.email}
              onChange={handleChange}
              required
              className={`input ${errors.some(e => e.includes('email') || e.includes('Email')) ? 'border-red-500' : ''}`}
              placeholder="email@example.com"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              <Lock className="h-4 w-4 inline mr-1" />
              Jelszó *
            </label>
            <div className="relative">
              <input
                type={showPassword ? 'text' : 'password'}
                name="password"
                value={formData.password}
                onChange={handleChange}
                required
                className={`input w-full pr-10 ${errors.some(e => e.includes('jelszó') || e.includes('Jelszó')) ? 'border-red-500' : ''}`}
                placeholder="••••••••"
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
              >
                {showPassword ? <EyeOff className="h-5 w-5" /> : <Eye className="h-5 w-5" />}
              </button>
            </div>
          </div>

          {/* Forgot Password Link */}
          <div className="text-right">
            <Link
              to="/forgot-password"
              className="text-sm text-primary-600 hover:text-primary-700 font-medium"
            >
              Elfelejtetted a jelszavad?
            </Link>
          </div>

          {errors.length > 0 && (
            <div className="p-4 bg-red-50 border border-red-200 rounded-lg">
              <ul className="text-red-800 text-sm list-disc list-inside">
                {errors.map((error, idx) => (
                  <li key={idx}>{error}</li>
                ))}
              </ul>
            </div>
          )}

          <button
            type="submit"
            disabled={loading}
            className="btn btn-primary w-full disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 py-3"
          >
            {loading ? (
              <>
                <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                Bejelentkezés...
              </>
            ) : (
              <>
                <LogIn className="h-5 w-5" />
                Bejelentkezés
              </>
            )}
          </button>
        </form>

          {/* Register Link */}
          <div className="mt-6 text-center">
            <p className="text-sm text-gray-600">
              Még nincs fiókod?{' '}
              <Link to="/register" className="font-medium text-primary-600 hover:text-primary-700">
                Regisztráció
              </Link>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default LoginPage;

