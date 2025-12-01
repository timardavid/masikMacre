/**
 * Footer Component
 * Modern footer with links and info
 */

import { Link } from 'react-router-dom';
import { Activity, Mail, Phone, MapPin } from 'lucide-react';

const Footer = () => {
  return (
    <footer className="bg-gradient-to-b from-gray-900 to-gray-800 text-white mt-auto">
      <div className="container mx-auto px-4 py-12 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
          {/* Brand */}
          <div>
            <div className="flex items-center space-x-2 mb-4">
              <div className="bg-gradient-to-br from-primary-600 to-primary-800 p-2 rounded-xl">
                <Activity className="h-5 w-5 text-white" />
              </div>
              <span className="text-xl font-bold">Pályafoglaló</span>
            </div>
            <p className="text-gray-400 text-sm">
              Könnyedén foglalj időpontot kedvenc teniszpályádra. 
              Modern, gyors és egyszerű pályafoglalási rendszer.
            </p>
          </div>

          {/* Quick Links */}
          <div>
            <h3 className="font-semibold mb-4">Gyors linkek</h3>
            <ul className="space-y-2 text-sm">
              <li>
                <Link to="/courts" className="text-gray-400 hover:text-primary-400 transition-colors">
                  Pályák böngészése
                </Link>
              </li>
              <li>
                <Link to="/login" className="text-gray-400 hover:text-primary-400 transition-colors">
                  Bejelentkezés
                </Link>
              </li>
              <li>
                <Link to="/register" className="text-gray-400 hover:text-primary-400 transition-colors">
                  Regisztráció
                </Link>
              </li>
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h3 className="font-semibold mb-4">Kapcsolat</h3>
            <ul className="space-y-2 text-sm text-gray-400">
              <li className="flex items-center space-x-2">
                <Mail className="h-4 w-4" />
                <span>info@palyafoglalo.hu</span>
              </li>
              <li className="flex items-center space-x-2">
                <Phone className="h-4 w-4" />
                <span>+36 1 234 5678</span>
              </li>
              <li className="flex items-center space-x-2">
                <MapPin className="h-4 w-4" />
                <span>Budapest, Magyarország</span>
              </li>
            </ul>
          </div>
        </div>

        {/* Copyright */}
        <div className="border-t border-gray-700 pt-8 text-center">
          <p className="text-sm text-gray-400">
            © {new Date().getFullYear()} Pályafoglaló - Teniszpálya Foglalási Rendszer. 
            Minden jog fenntartva.
          </p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;

