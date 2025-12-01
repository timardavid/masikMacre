/**
 * Home Page
 * Modern landing page with featured courts and quick booking
 */

import { Link } from 'react-router-dom';
import { useCourts } from '../hooks';
import { useAuth } from '../context/AuthContext';
import CourtCard from '../components/CourtCard/CourtCard';
import { Calendar, ArrowRight, Activity, Clock, Shield, TrendingUp } from 'lucide-react';

const HomePage = () => {
  const { courts, loading } = useCourts();
  const { isAuthenticated } = useAuth();

  return (
    <div className="space-y-16">
      {/* Hero Section */}
      <section className="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 rounded-3xl shadow-2xl">
        <div className="absolute inset-0 bg-grid-pattern opacity-10"></div>
        <div className="relative text-white py-20 px-6">
          <div className="max-w-4xl mx-auto text-center">
            <div className="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl mb-6">
              <Activity className="h-10 w-10 text-white" />
            </div>
            <h1 className="text-4xl md:text-6xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-white to-primary-100">
              Teniszpálya Foglalási Rendszer
            </h1>
            <p className="text-xl md:text-2xl mb-10 text-primary-100 max-w-2xl mx-auto">
              Könnyedén foglalj időpontot kedvenc teniszpályádra. 
              Gyors, egyszerű és biztonságos foglalási rendszer.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link 
                to="/courts" 
                className="btn bg-white text-primary-600 hover:bg-gray-50 hover:scale-105 inline-flex items-center justify-center text-lg px-8 py-4 shadow-xl"
              >
                <Calendar className="h-6 w-6 mr-2" />
                Pályák böngészése
                <ArrowRight className="h-6 w-6 ml-2" />
              </Link>
              {!isAuthenticated() && (
                <Link 
                  to="/register" 
                  className="btn border-2 border-white text-white hover:bg-white/10 inline-flex items-center justify-center text-lg px-8 py-4"
                >
                  Regisztráció most
                </Link>
              )}
            </div>
          </div>
        </div>
      </section>

      {/* Features */}
      <section>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
          <div className="card text-center hover:scale-105 transition-transform">
            <div className="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <Clock className="h-8 w-8 text-primary-600" />
            </div>
            <h3 className="text-xl font-bold mb-2">24/7 Elérhető</h3>
            <p className="text-gray-600">
              Bármikor foglalhatsz, akár éjjel-nappal. Nincs időzítés vagy korlátozás.
            </p>
          </div>
          <div className="card text-center hover:scale-105 transition-transform">
            <div className="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <Shield className="h-8 w-8 text-primary-600" />
            </div>
            <h3 className="text-xl font-bold mb-2">Biztonságos</h3>
            <p className="text-gray-600">
              Adataid védve, biztonságos fizetés és adatkezelés garantált.
            </p>
          </div>
          <div className="card text-center hover:scale-105 transition-transform">
            <div className="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <TrendingUp className="h-8 w-8 text-primary-600" />
            </div>
            <h3 className="text-xl font-bold mb-2">Gyors és Egyszerű</h3>
            <p className="text-gray-600">
              Pár kattintással kész a foglalás. Nincs bonyolult regisztráció vagy folyamat.
            </p>
          </div>
        </div>
      </section>

      {/* Featured Courts */}
      <section>
        <div className="flex items-center justify-between mb-8">
          <div>
            <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
              Elérhető Pályák
            </h2>
            <p className="text-gray-600">
              Válaszd ki a kedvenc pályádat és foglalj időpontot
            </p>
          </div>
          <Link 
            to="/courts" 
            className="hidden md:flex items-center text-primary-600 hover:text-primary-700 font-medium"
          >
            Összes pálya
            <ArrowRight className="h-5 w-5 ml-1" />
          </Link>
        </div>
        
        {loading ? (
          <div className="text-center py-20">
            <div className="inline-block animate-spin rounded-full h-16 w-16 border-4 border-primary-200 border-t-primary-600"></div>
            <p className="mt-4 text-gray-600 text-lg">Pályák betöltése...</p>
          </div>
        ) : courts.length > 0 ? (
          <>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {courts.slice(0, 6).map((court) => (
                <CourtCard key={court.id} court={court} />
              ))}
            </div>
            {courts.length > 6 && (
              <div className="text-center mt-10">
                <Link to="/courts" className="btn btn-primary text-lg px-8 py-3 inline-flex items-center">
                  Összes {courts.length} pálya megtekintése
                  <ArrowRight className="h-5 w-5 ml-2" />
                </Link>
              </div>
            )}
          </>
        ) : (
          <div className="card text-center py-16">
            <Activity className="h-16 w-16 text-gray-300 mx-auto mb-4" />
            <p className="text-gray-600 text-lg">Jelenleg nincsenek elérhető pályák.</p>
          </div>
        )}
      </section>
    </div>
  );
};

export default HomePage;

