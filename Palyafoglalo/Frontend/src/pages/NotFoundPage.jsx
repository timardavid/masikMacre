/**
 * 404 Not Found Page
 * Handles unknown routes
 */

import { Link } from 'react-router-dom';
import { Home } from 'lucide-react';

const NotFoundPage = () => {
  return (
    <div className="min-h-screen bg-gradient-to-br from-primary-50 via-white to-primary-100 flex items-center justify-center py-12 px-4">
      <div className="text-center">
        <h1 className="text-6xl font-bold text-primary-600 mb-4">404</h1>
        <h2 className="text-2xl font-semibold text-gray-800 mb-4">Az oldal nem található</h2>
        <p className="text-gray-600 mb-8">
          A keresett oldal nem létezik vagy el lett távolítva.
        </p>
        <Link
          to="/"
          className="btn btn-primary inline-flex items-center gap-2"
        >
          <Home className="h-5 w-5" />
          Vissza a főoldalra
        </Link>
      </div>
    </div>
  );
};

export default NotFoundPage;

