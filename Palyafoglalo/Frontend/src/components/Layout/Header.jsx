/**
 * Header Component
 * Modern navigation header with auth status
 */

import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { LogOut, User, Calendar, Menu, X, Activity } from 'lucide-react';
import { useState } from 'react';

const Header = () => {
  const { user, isAuthenticated, logout } = useAuth();
  const navigate = useNavigate();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  const handleLogout = () => {
    logout();
    navigate('/');
    setMobileMenuOpen(false);
  };

  return (
    <header className="bg-white shadow-lg sticky top-0 z-50 backdrop-blur-sm bg-white/95">
      <div className="container mx-auto px-4 lg:px-8">
        <div className="flex items-center justify-between h-20">
          {/* Logo */}
          <Link 
            to="/" 
            className="flex items-center space-x-3 group"
            onClick={() => setMobileMenuOpen(false)}
          >
            <div className="bg-gradient-to-br from-primary-600 to-primary-800 p-2 rounded-xl group-hover:scale-110 transition-transform duration-200">
              <Activity className="h-6 w-6 text-white" />
            </div>
            <div>
              <span className="text-2xl font-bold bg-gradient-to-r from-primary-600 to-primary-800 bg-clip-text text-transparent">
                Pályafoglaló
              </span>
              <p className="text-xs text-gray-500 -mt-1">Teniszpálya Foglalás</p>
            </div>
          </Link>

          {/* Desktop Navigation */}
          <nav className="hidden md:flex items-center space-x-8">
            <Link
              to="/courts"
              className="text-gray-700 hover:text-primary-600 transition-colors font-medium relative group"
            >
              Pályák
              <span className="absolute bottom-0 left-0 w-0 h-0.5 bg-primary-600 group-hover:w-full transition-all duration-300"></span>
            </Link>
            
            {isAuthenticated() ? (
              <>
                <Link
                  to="/my-bookings"
                  className="text-gray-700 hover:text-primary-600 transition-colors font-medium relative group"
                >
                  Foglalásaim
                  <span className="absolute bottom-0 left-0 w-0 h-0.5 bg-primary-600 group-hover:w-full transition-all duration-300"></span>
                </Link>
                <div className="flex items-center space-x-4 pl-4 border-l border-gray-200">
                  <div className="flex items-center space-x-2 text-gray-700 bg-gray-50 px-3 py-2 rounded-lg">
                    <div className="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center">
                      <User className="h-4 w-4 text-white" />
                    </div>
                    <span className="text-sm font-medium">{user?.full_name || user?.email}</span>
                  </div>
                  <button
                    onClick={handleLogout}
                    className="flex items-center space-x-2 text-gray-700 hover:text-red-600 transition-colors px-3 py-2 rounded-lg hover:bg-red-50"
                  >
                    <LogOut className="h-5 w-5" />
                    <span className="font-medium">Kijelentkezés</span>
                  </button>
                </div>
              </>
            ) : (
              <div className="flex items-center space-x-3">
                <Link
                  to="/login"
                  className="text-gray-700 hover:text-primary-600 transition-colors font-medium"
                >
                  Bejelentkezés
                </Link>
                <Link
                  to="/register"
                  className="btn btn-primary"
                >
                  Regisztráció
                </Link>
              </div>
            )}
          </nav>

          {/* Mobile Menu Button */}
          <button
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            className="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            {mobileMenuOpen ? (
              <X className="h-6 w-6 text-gray-700" />
            ) : (
              <Menu className="h-6 w-6 text-gray-700" />
            )}
          </button>
        </div>

        {/* Mobile Menu */}
        {mobileMenuOpen && (
          <nav className="md:hidden py-4 border-t border-gray-200 animate-slide-up">
            <div className="flex flex-col space-y-3">
              <Link
                to="/courts"
                className="px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors font-medium"
                onClick={() => setMobileMenuOpen(false)}
              >
                Pályák
              </Link>
              
              {isAuthenticated() ? (
                <>
                  <Link
                    to="/my-bookings"
                    className="px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors font-medium"
                    onClick={() => setMobileMenuOpen(false)}
                  >
                    Foglalásaim
                  </Link>
                  <div className="px-4 py-2 flex items-center space-x-2 text-gray-700">
                    <User className="h-5 w-5" />
                    <span className="font-medium">{user?.full_name || user?.email}</span>
                  </div>
                  <button
                    onClick={handleLogout}
                    className="px-4 py-2 text-left text-red-600 hover:bg-red-50 rounded-lg transition-colors font-medium flex items-center space-x-2"
                  >
                    <LogOut className="h-5 w-5" />
                    <span>Kijelentkezés</span>
                  </button>
                </>
              ) : (
                <>
                  <Link
                    to="/login"
                    className="px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors font-medium"
                    onClick={() => setMobileMenuOpen(false)}
                  >
                    Bejelentkezés
                  </Link>
                  <Link
                    to="/register"
                    className="btn btn-primary mx-4"
                    onClick={() => setMobileMenuOpen(false)}
                  >
                    Regisztráció
                  </Link>
                </>
              )}
            </div>
          </nav>
        )}
      </div>
    </header>
  );
};

export default Header;

