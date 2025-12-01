/**
 * Courts Page
 * Modern list of all available courts
 */

import { useCourts } from '../hooks';
import CourtCard from '../components/CourtCard/CourtCard';
import { Activity, Search } from 'lucide-react';
import { useState } from 'react';

const CourtsPage = () => {
  const { courts, loading, error } = useCourts();
  const [searchTerm, setSearchTerm] = useState('');

  const filteredCourts = courts.filter((court) =>
    court.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    (court.surface_name || court.surface || '').toLowerCase().includes(searchTerm.toLowerCase())
  );

  if (loading) {
    return (
      <div className="text-center py-20">
        <div className="inline-block animate-spin rounded-full h-16 w-16 border-4 border-primary-200 border-t-primary-600"></div>
        <p className="mt-4 text-gray-600 text-lg">Pályák betöltése...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="card text-center py-16 max-w-md mx-auto">
        <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <span className="text-2xl">⚠️</span>
        </div>
        <p className="text-red-600 text-lg font-medium mb-2">Hiba történt</p>
        <p className="text-gray-600">{error}</p>
      </div>
    );
  }

  return (
    <div className="space-y-8">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
            Összes Pálya
          </h1>
          <p className="text-gray-600">
            {courts.length} pálya elérhető
          </p>
        </div>

        {/* Search */}
        <div className="relative max-w-md w-full">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
          <input
            type="text"
            placeholder="Keresés pálya név vagy felület alapján..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="input pl-10 w-full"
          />
        </div>
      </div>
      
      {/* Courts Grid */}
      {filteredCourts.length > 0 ? (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredCourts.map((court) => (
            <CourtCard key={court.id} court={court} />
          ))}
        </div>
      ) : (
        <div className="card text-center py-16">
          <Activity className="h-16 w-16 text-gray-300 mx-auto mb-4" />
          <p className="text-gray-600 text-lg">
            {searchTerm 
              ? 'Nincs találat a keresésre' 
              : 'Jelenleg nincsenek elérhető pályák.'}
          </p>
          {searchTerm && (
            <button
              onClick={() => setSearchTerm('')}
              className="mt-4 text-primary-600 hover:text-primary-700 font-medium"
            >
              Keresés törlése
            </button>
          )}
        </div>
      )}
    </div>
  );
};

export default CourtsPage;

