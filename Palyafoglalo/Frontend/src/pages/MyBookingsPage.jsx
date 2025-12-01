/**
 * My Bookings Page
 * Modern page displaying user's bookings
 */

import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useBookings } from '../hooks';
import { formatDateTime, formatPrice, getDuration } from '../utils/format';
import { Calendar, Clock, X, CheckCircle, AlertCircle, TrendingUp, ArrowLeft } from 'lucide-react';

const MyBookingsPage = () => {
  const { isAuthenticated, loading: authLoading, user } = useAuth();
  const { bookings, loading, cancelBooking } = useBookings();
  const [message, setMessage] = useState(null);

  const navigate = useNavigate();
  
  // Wait for auth to load before checking authentication
  useEffect(() => {
    // Only redirect if auth is fully loaded AND user is not authenticated
    if (!authLoading && !isAuthenticated()) {
      navigate('/login', { 
        replace: true,
        state: { from: '/my-bookings' }
      });
    }
  }, [authLoading, isAuthenticated, navigate]);

  useEffect(() => {
    // Check for success message from navigation state
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('message');
    if (msg) {
      setMessage(msg);
      setTimeout(() => setMessage(null), 5000);
    }
  }, []);

  const handleCancel = async (bookingId) => {
    if (!window.confirm('Biztosan lemondod ezt a foglalást?')) {
      return;
    }

    const result = await cancelBooking(bookingId);
    if (result.success) {
      setMessage('Foglalás sikeresen lemondva');
      setTimeout(() => setMessage(null), 5000);
    } else {
      alert(result.errors?.join('\n') || 'A lemondás sikertelen volt');
    }
  };

  const getStatusBadge = (status) => {
    const badges = {
      pending: 'badge badge-warning',
      confirmed: 'badge badge-success',
      cancelled: 'badge badge-danger',
      completed: 'badge badge-info',
      no_show: 'badge badge-danger',
    };

    const labels = {
      pending: 'Függőben',
      confirmed: 'Megerősítve',
      cancelled: 'Lemondva',
      completed: 'Befejezve',
      no_show: 'Nem jelent meg',
    };

    return (
      <span className={badges[status] || 'badge'}>
        {labels[status] || status}
      </span>
    );
  };

  // Show loading while auth is loading
  if (authLoading) {
    return (
      <div className="text-center py-20">
        <div className="inline-block animate-spin rounded-full h-16 w-16 border-4 border-primary-200 border-t-primary-600"></div>
        <p className="mt-4 text-gray-600 text-lg">Betöltés...</p>
      </div>
    );
  }

  // If not authenticated, return null (redirect is handled by useEffect)
  if (!isAuthenticated()) {
    return null;
  }

  return (
    <div className="max-w-5xl mx-auto space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 className="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
            Foglalásaim
          </h1>
          <p className="text-gray-600">
            {bookings.length > 0 ? `${bookings.length} foglalás` : 'Nincs még foglalásod'}
          </p>
        </div>
        <Link to="/courts" className="btn btn-outline inline-flex items-center">
          <ArrowLeft className="h-5 w-5 mr-2" />
          Új foglalás
        </Link>
      </div>

      {message && (
        <div className="p-4 bg-green-50 border-2 border-green-200 rounded-xl flex items-center animate-slide-up shadow-sm">
          <CheckCircle className="h-5 w-5 text-green-600 mr-3 flex-shrink-0" />
          <p className="text-green-800 font-medium">{message}</p>
        </div>
      )}

      {loading ? (
        <div className="text-center py-20">
          <div className="inline-block animate-spin rounded-full h-16 w-16 border-4 border-primary-200 border-t-primary-600"></div>
          <p className="mt-4 text-gray-600 text-lg">Betöltés...</p>
        </div>
      ) : bookings.length > 0 ? (
        <div className="grid grid-cols-1 gap-4">
          {bookings.map((booking) => (
            <div key={booking.id} className="card hover:shadow-xl transition-all duration-300 group">
              <div className="flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div className="flex-1">
                  <div className="flex items-center gap-3 mb-3">
                    <h3 className="text-xl md:text-2xl font-bold text-gray-900 group-hover:text-primary-600 transition-colors">
                      {booking.court_name || `Pálya #${booking.court_id}`}
                    </h3>
                    {getStatusBadge(booking.status)}
                    {booking.payment_status === 'paid' && (
                      <span className="badge badge-success">Fizetve</span>
                    )}
                  </div>
                  
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div className="flex items-center text-gray-700 bg-gray-50 px-4 py-2 rounded-lg">
                      <Calendar className="h-5 w-5 mr-2 text-primary-600" />
                      <span className="font-medium">{formatDateTime(booking.start_datetime)}</span>
                    </div>
                    <div className="flex items-center text-gray-700 bg-gray-50 px-4 py-2 rounded-lg">
                      <Clock className="h-5 w-5 mr-2 text-primary-600" />
                      <span className="font-medium">{getDuration(booking.start_datetime, booking.end_datetime)}</span>
                    </div>
                  </div>

                  <div className="flex flex-wrap gap-2 text-sm text-gray-600">
                    <span><strong>Ügyfél:</strong> {booking.customer_name}</span>
                    {booking.customer_email && (
                      <span className="ml-4"><strong>Email:</strong> {booking.customer_email}</span>
                    )}
                    {booking.customer_phone && (
                      <span className="ml-4"><strong>Telefon:</strong> {booking.customer_phone}</span>
                    )}
                  </div>
                </div>
                
                <div className="flex flex-col md:items-end gap-3">
                  {booking.price_cents && (
                    <div className="text-right bg-primary-50 px-4 py-3 rounded-xl border border-primary-200">
                      <p className="text-xs text-gray-600 mb-1">Összeg</p>
                      <p className="text-2xl font-bold text-primary-600">
                        {formatPrice(booking.price_cents, booking.currency).replace(/[^0-9 ]/g, '').trim()}
                      </p>
                    </div>
                  )}
                  
                  {(booking.status === 'confirmed' || booking.status === 'pending') && 
                   new Date(booking.start_datetime) > new Date() && (
                    <button
                      onClick={() => handleCancel(booking.id)}
                      className="btn btn-danger flex items-center justify-center gap-2 whitespace-nowrap"
                    >
                      <X className="h-5 w-5" />
                      Lemondás
                    </button>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>
      ) : (
        <div className="card text-center py-16">
          <div className="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <Calendar className="h-10 w-10 text-gray-400" />
          </div>
          <h2 className="text-2xl font-bold text-gray-900 mb-2">Még nincsenek foglalásaid</h2>
          <p className="text-gray-600 mb-6">
            Kezdj el foglalni pályákat az oldalon
          </p>
          <Link to="/courts" className="btn btn-primary inline-flex items-center text-lg px-8 py-3">
            <TrendingUp className="h-5 w-5 mr-2" />
            Pályák böngészése
          </Link>
        </div>
      )}
    </div>
  );
};

export default MyBookingsPage;

