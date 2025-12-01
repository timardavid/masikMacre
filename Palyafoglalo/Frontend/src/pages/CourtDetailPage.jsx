/**
 * Court Detail Page
 * Detailed information about a specific court
 */

import { useParams, Link, useLocation } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { useCourt } from '../hooks';
import { MapPin, Sun, Moon, Lightbulb, Calendar, CheckCircle, Mail, ExternalLink, Users, Ruler, Car, Shirt, ShoppingBag, Star } from 'lucide-react';
import AvailabilityCalendar from '../components/AvailabilityCalendar/AvailabilityCalendar';
import CourtImageGallery from '../components/CourtImageGallery/CourtImageGallery';
import CourtReviews from '../components/CourtReviews/CourtReviews';

const CourtDetailPage = () => {
  const { id } = useParams();
  const location = useLocation();
  const { court, loading, error } = useCourt(id);
  const [bookingMessage, setBookingMessage] = useState(null);
  const [emailInfo, setEmailInfo] = useState(null);

  useEffect(() => {
    if (location.state?.message || location.state?.bookingCreated) {
      setBookingMessage(location.state.message || 'Foglalás sikeresen létrehozva!');
      if (location.state?.emailInfo) {
        setEmailInfo(location.state.emailInfo);
      }
      // Clear the message from location state
      window.history.replaceState({}, document.title);
      // Auto-hide after 8 seconds
      setTimeout(() => {
        setBookingMessage(null);
        setEmailInfo(null);
      }, 8000);
    }
  }, [location.state]);

  if (loading) {
    return (
      <div className="text-center py-12">
        <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
        <p className="mt-4 text-gray-600">Betöltés...</p>
      </div>
    );
  }

  if (error || !court) {
    return (
      <div className="text-center py-12">
        <p className="text-red-600 mb-4">{error || 'Pálya nem található'}</p>
        <Link to="/courts" className="btn btn-primary">
          Vissza a pályákhoz
        </Link>
      </div>
    );
  }

  return (
    <div className="max-w-5xl mx-auto space-y-6">
      {bookingMessage && (
        <div className="mb-4 space-y-3">
          <div className="p-4 bg-green-50 border-2 border-green-200 rounded-xl flex items-center shadow-sm animate-slide-up">
            <CheckCircle className="h-5 w-5 text-green-600 mr-3 flex-shrink-0" />
            <p className="text-green-800 font-medium">{bookingMessage}</p>
          </div>
          
          {emailInfo && emailInfo.path && (
            <div className="p-4 bg-blue-50 border-2 border-blue-200 rounded-xl shadow-sm animate-slide-up">
              <div className="flex items-start gap-3">
                <Mail className="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" />
                <div className="flex-1">
                  <p className="text-blue-900 font-medium mb-2">
                    📧 Email megerősítés elmentve
                  </p>
                  <p className="text-blue-700 text-sm mb-3">
                    Az email megerősítés a fejlesztési környezetben fájlba lett mentve. Kattints az alábbi linkre az email tartalmának megtekintéséhez:
                  </p>
                  <a
                    href={emailInfo.path}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="inline-flex items-center gap-2 text-blue-700 hover:text-blue-800 font-semibold underline"
                  >
                    Email megtekintése
                    <ExternalLink className="h-4 w-4" />
                  </a>
                </div>
              </div>
            </div>
          )}
        </div>
      )}
      
      <div className="card">
        <div className="flex items-start justify-between mb-6 flex-wrap gap-4">
          <div className="flex-1">
            <h1 className="text-3xl md:text-4xl font-bold mb-4 text-gray-900">{court.name}</h1>
            
            <div className="flex flex-wrap items-center gap-3 mb-4">
              <span className="badge badge-info text-base py-1.5 px-3">
                {court.surface_name || court.surface}
              </span>
              
              {court.is_indoor ? (
                <span className="flex items-center text-gray-700 bg-blue-50 px-3 py-1.5 rounded-lg font-medium">
                  <Moon className="h-5 w-5 mr-1.5 text-blue-600" />
                  Beltéri
                </span>
              ) : (
                <span className="flex items-center text-gray-700 bg-green-50 px-3 py-1.5 rounded-lg font-medium">
                  <Sun className="h-5 w-5 mr-1.5 text-green-600" />
                  Kültéri
                </span>
              )}
              
              {court.has_lighting && (
                <span className="flex items-center text-gray-700 bg-yellow-50 px-3 py-1.5 rounded-lg font-medium">
                  <Lightbulb className="h-5 w-5 mr-1.5 text-yellow-600" />
                  Világítás
                </span>
              )}
            </div>

            {/* Rating display */}
            {court.average_rating && court.average_rating > 0 && (
              <div className="flex items-center gap-2 px-3 py-1.5 bg-yellow-50 rounded-lg">
                <Star className="h-5 w-5 text-yellow-500 fill-yellow-500" />
                <span className="font-bold text-gray-900">{court.average_rating.toFixed(1)}</span>
                <span className="text-gray-600 text-sm">({court.total_reviews || 0} értékelés)</span>
              </div>
            )}
          </div>

          {/* Description */}
          {court.description && (
            <p className="text-gray-700 mb-6 leading-relaxed text-lg">{court.description}</p>
          )}

          {court.notes && (
            <p className="text-gray-600 mb-6 leading-relaxed">{court.notes}</p>
          )}
        </div>

        <Link
          to={`/book/${court.id}`}
          className="btn btn-primary inline-flex items-center whitespace-nowrap"
        >
          <Calendar className="h-5 w-5 mr-2" />
          Foglalás most
        </Link>
      </div>

      {/* Court Images Gallery */}
      <div className="card">
        <CourtImageGallery 
          images={court.images || []} 
          mainImageUrl={court.main_image_url}
        />
      </div>

      {/* Court Details */}
      <div className="card">
        <h2 className="text-2xl font-bold mb-6">Részletek</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {court.dimensions && (
            <div className="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
              <div className="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                <Ruler className="h-6 w-6 text-primary-600" />
              </div>
              <div>
                <div className="text-sm text-gray-600">Méretek</div>
                <div className="font-semibold">{court.dimensions}</div>
              </div>
            </div>
          )}

          {court.capacity && (
            <div className="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
              <div className="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                <Users className="h-6 w-6 text-primary-600" />
              </div>
              <div>
                <div className="text-sm text-gray-600">Kapacitás</div>
                <div className="font-semibold">Max {court.capacity} fő</div>
              </div>
            </div>
          )}

          {court.parking_available && (
            <div className="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
              <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <Car className="h-6 w-6 text-green-600" />
              </div>
              <div>
                <div className="text-sm text-gray-600">Parkolás</div>
                <div className="font-semibold text-green-700">Elérhető</div>
              </div>
            </div>
          )}

          {court.changing_rooms && (
            <div className="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
              <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <Shirt className="h-6 w-6 text-blue-600" />
              </div>
              <div>
                <div className="text-sm text-gray-600">Öltöző</div>
                <div className="font-semibold text-blue-700">Elérhető</div>
              </div>
            </div>
          )}

          {court.pro_shop && (
            <div className="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
              <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <ShoppingBag className="h-6 w-6 text-purple-600" />
              </div>
              <div>
                <div className="text-sm text-gray-600">Pro Shop</div>
                <div className="font-semibold text-purple-700">Elérhető</div>
              </div>
            </div>
          )}

          {court.facilities && (
            <div className="md:col-span-2 lg:col-span-3 p-4 bg-gray-50 rounded-xl">
              <div className="text-sm text-gray-600 mb-2">További lehetőségek</div>
              <div className="flex flex-wrap gap-2">
                {JSON.parse(court.facilities || '[]').map((facility, idx) => (
                  <span key={idx} className="badge badge-info">
                    {facility}
                  </span>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>

      {/* Availability Calendar */}
      <div className="mt-6">
        <AvailabilityCalendar 
          key={`calendar-${id}-${bookingMessage ? 'updated' : 'default'}`}
          courtId={parseInt(id)} 
        />
      </div>

      {court.opening_hours && court.opening_hours.length > 0 && (
        <div className="card">
          <h2 className="text-2xl font-bold mb-6 flex items-center">
            <Calendar className="h-6 w-6 mr-2 text-primary-600" />
            Nyitvatartás
          </h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
            {court.opening_hours.map((hours, idx) => {
              const weekdays = ['Vasárnap', 'Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat'];
              return (
                <div key={idx} className="flex justify-between items-center p-3 bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-100 hover:border-primary-200 transition-colors">
                  <span className="font-semibold text-gray-900">{weekdays[hours.weekday]}</span>
                  {hours.is_closed ? (
                    <span className="badge badge-danger">Zárva</span>
                  ) : (
                    <span className="text-gray-700 font-medium">
                      {hours.open_time.substring(0, 5)} - {hours.close_time.substring(0, 5)}
                    </span>
                  )}
                </div>
              );
            })}
          </div>
        </div>
      )}

      {/* Reviews Section */}
      <div className="card">
        <CourtReviews courtId={parseInt(id)} />
      </div>
    </div>
  );
};

export default CourtDetailPage;

