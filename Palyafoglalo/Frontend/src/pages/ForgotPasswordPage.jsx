/**
 * Forgot Password Page
 * Password reset request page
 */

import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { authAPI } from '../services/api';
import { Lock, Mail, ArrowLeft, CheckCircle, KeyRound } from 'lucide-react';

const ForgotPasswordPage = () => {
  const navigate = useNavigate();
  const { isAuthenticated } = useAuth();
  
  const [step, setStep] = useState(1); // 1: email, 2: code, 3: new password
  const [formData, setFormData] = useState({
    email: '',
    code: '',
    password: '',
    password_confirm: '',
  });
  
  const [errors, setErrors] = useState([]);
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
    setErrors([]);
  };

  const handleRequestCode = async (e) => {
    e.preventDefault();
    setErrors([]);
    setLoading(true);

    try {
      const response = await authAPI.forgotPassword(formData.email);
      if (response.success) {
        setStep(2);
        setSuccess(true);
      } else {
        setErrors([response.message || 'Hiba történt']);
      }
    } catch (error) {
      setErrors([error.message || 'Hiba történt']);
    } finally {
      setLoading(false);
    }
  };

  const handleResetPassword = async (e) => {
    e.preventDefault();
    setErrors([]);
    
    if (formData.password !== formData.password_confirm) {
      setErrors(['A jelszavak nem egyeznek']);
      return;
    }
    
    if (formData.password.length < 8) {
      setErrors(['A jelszónak legalább 8 karakter hosszúnak kell lennie']);
      return;
    }
    
    setLoading(true);

    try {
      const response = await authAPI.resetPassword(
        formData.email,
        formData.code,
        formData.password
      );
      
      if (response.success) {
        setStep(3);
      } else {
        setErrors([response.message || 'Érvénytelen vagy lejárt kód']);
      }
    } catch (error) {
      setErrors([error.message || 'Hiba történt']);
    } finally {
      setLoading(false);
    }
  };

  if (isAuthenticated()) {
    navigate('/courts', { replace: true });
    return null;
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-primary-50 via-white to-primary-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-md w-full">
        {/* Header */}
        <div className="text-center mb-8">
          <div className="mx-auto w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center mb-4">
            <KeyRound className="h-8 w-8 text-white" />
          </div>
          <h1 className="text-3xl font-bold text-gray-900">Jelszó visszaállítása</h1>
          <p className="mt-2 text-sm text-gray-600">
            {step === 1 && 'Add meg az email címed, és küldünk egy visszaállítási kódot'}
            {step === 2 && 'Add meg az email címre küldött 6 számjegyű kódot'}
            {step === 3 && 'Jelszavad sikeresen megváltoztatva!'}
          </p>
        </div>

        {/* Form Card */}
        <div className="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
          {step === 1 && (
            <form onSubmit={handleRequestCode} className="space-y-5">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  <Mail className="h-4 w-4 inline mr-1" />
                  E-mail cím *
                </label>
                <input
                  type="email"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                  required
                  className="input w-full"
                  placeholder="email@example.com"
                />
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

              {success && (
                <div className="p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-2">
                  <CheckCircle className="h-5 w-5 text-green-600" />
                  <p className="text-green-800 text-sm">
                    A visszaállítási kód elküldve az email címedre
                  </p>
                </div>
              )}

              <button
                type="submit"
                disabled={loading}
                className="btn btn-primary w-full disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {loading ? 'Küldés...' : 'Kód küldése'}
              </button>
            </form>
          )}

          {step === 2 && (
            <form onSubmit={handleResetPassword} className="space-y-5">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  6 számjegyű kód *
                </label>
                <input
                  type="text"
                  name="code"
                  value={formData.code}
                  onChange={handleChange}
                  required
                  maxLength={6}
                  className="input w-full text-center text-2xl tracking-widest"
                  placeholder="000000"
                  pattern="[0-9]{6}"
                />
                <p className="mt-1 text-xs text-gray-500">
                  Add meg a 6 számjegyű kódot az email-ből
                </p>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  <Lock className="h-4 w-4 inline mr-1" />
                  Új jelszó *
                </label>
                <input
                  type="password"
                  name="password"
                  value={formData.password}
                  onChange={handleChange}
                  required
                  minLength={8}
                  className="input w-full"
                  placeholder="Minimum 8 karakter"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  <Lock className="h-4 w-4 inline mr-1" />
                  Új jelszó megerősítés *
                </label>
                <input
                  type="password"
                  name="password_confirm"
                  value={formData.password_confirm}
                  onChange={handleChange}
                  required
                  minLength={8}
                  className="input w-full"
                  placeholder="Erősítsd meg az új jelszót"
                />
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

              <div className="flex gap-3">
                <button
                  type="button"
                  onClick={() => setStep(1)}
                  className="btn btn-secondary flex-1"
                >
                  <ArrowLeft className="h-4 w-4 inline mr-1" />
                  Vissza
                </button>
                <button
                  type="submit"
                  disabled={loading}
                  className="btn btn-primary flex-1 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {loading ? 'Mentés...' : 'Jelszó megváltoztatása'}
                </button>
              </div>
            </form>
          )}

          {step === 3 && (
            <div className="text-center space-y-5">
              <div className="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                <CheckCircle className="h-8 w-8 text-green-600" />
              </div>
              <div>
                <h2 className="text-xl font-bold text-gray-900 mb-2">
                  Sikeres jelszó változtatás!
                </h2>
                <p className="text-gray-600">
                  Most már be tudsz jelentkezni az új jelszóval.
                </p>
              </div>
              <Link
                to="/login"
                className="btn btn-primary w-full inline-block"
              >
                Bejelentkezés
              </Link>
            </div>
          )}

          {/* Back to Login */}
          <div className="mt-6 text-center">
            <Link
              to="/login"
              className="text-sm text-gray-600 hover:text-gray-900 flex items-center justify-center gap-1"
            >
              <ArrowLeft className="h-4 w-4" />
              Vissza a bejelentkezéshez
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ForgotPasswordPage;

