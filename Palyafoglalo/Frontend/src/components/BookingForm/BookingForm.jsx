/**
 * Booking Form Component
 * Form for creating a new booking
 */

import { useState, useEffect } from 'react';
import { useNavigate, useParams, useLocation } from 'react-router-dom';
import { useCourt, useBookings } from '../../hooks';
import { useAuth } from '../../context/AuthContext';
import { formatPrice, formatDateTime } from '../../utils/format';
import { Calendar, Clock, User, Mail, Phone } from 'lucide-react';

const BookingForm = ({ courtId: propCourtId }) => {
  const navigate = useNavigate();
  const location = useLocation();
  const { courtId: paramCourtId } = useParams();
  const courtId = propCourtId || paramCourtId;
  
  const { user, isAuthenticated, loading: authLoading } = useAuth();
  const { court, loading: courtLoading } = useCourt(courtId);
  const { createBooking, checkAvailability } = useBookings();
  
  // Get pre-filled data from location state (if coming from calendar)
  const prefillData = location.state || {};

  // Check authentication on mount
  useEffect(() => {
    if (!authLoading && !isAuthenticated()) {
      // Redirect to login, save intended destination
      navigate('/login', {
        state: { 
          from: location.pathname,
          courtId: courtId,
          prefillData: prefillData
        }
      });
    }
  }, [authLoading, isAuthenticated, navigate, location.pathname, courtId, prefillData]);
  
  const [selectedHours, setSelectedHours] = useState(1); // Default 1 hour
  const [formData, setFormData] = useState({
    customer_name: '',
    customer_email: '',
    customer_phone: '',
    start_datetime: prefillData.startDatetime ? formatDateTimeForInput(prefillData.startDatetime) : '',
    end_datetime: prefillData.endDatetime ? formatDateTimeForInput(prefillData.endDatetime) : '',
  });

  // Helper function to format datetime for input field
  function formatDateTimeForInput(datetimeString) {
    if (!datetimeString) return '';
    const date = new Date(datetimeString);
    if (isNaN(date.getTime())) return '';
    // Format as YYYY-MM-DDTHH:mm for datetime-local input
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
  }
  
  const [pricing, setPricing] = useState(null);
  const [availability, setAvailability] = useState(null);
  const [errors, setErrors] = useState([]);
  const [submitting, setSubmitting] = useState(false);

  // Fixed prices (in cents)
  const fixedPrices = {
    1: 9000 * 100, // 9000 HUF
    2: 16000 * 100, // 16000 HUF
    3: 20000 * 100, // 20000 HUF
  };

  // Update end_datetime when start_datetime or selectedHours changes
  useEffect(() => {
    if (formData.start_datetime && selectedHours) {
      const startDate = new Date(formData.start_datetime);
      const endDate = new Date(startDate);
      endDate.setHours(endDate.getHours() + selectedHours);
      
      const endFormatted = formatDateTimeForInput(endDate.toISOString());
      setFormData(prev => ({
        ...prev,
        end_datetime: endFormatted
      }));
    }
  }, [formData.start_datetime, selectedHours]);

  useEffect(() => {
    // Only check if we have valid datetime values
    // Reset availability when datetime changes
    if (!formData.start_datetime || !formData.end_datetime) {
      setAvailability(null);
      setPricing(null);
      return;
    }

    if (formData.start_datetime && formData.end_datetime && courtId) {
      // Validate datetime format
      const startDate = new Date(formData.start_datetime);
      const endDate = new Date(formData.end_datetime);
      
      // Check if dates are valid
      if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
        setAvailability(null);
        return;
      }

      // Check if end is after start
      if (endDate <= startDate) {
        setAvailability({ available: false, errors: ['A befejezési időpontnak későbbinek kell lennie a kezdésnél'] });
        return;
      }

      // Convert datetime-local format to API format
      const startAPI = convertToAPIDateTime(formData.start_datetime);
      const endAPI = convertToAPIDateTime(formData.end_datetime);
      
      if (startAPI && endAPI) {
        // Set fixed price based on selected hours
        const priceCents = fixedPrices[selectedHours] || fixedPrices[1];
        const hours = selectedHours;
        setPricing({
          price_cents: priceCents,
          currency: 'HUF',
          hours: hours
        });

        // Use debounce to avoid too many API calls for availability
        const timeoutId = setTimeout(() => {
          checkAvailabilityForBooking(startAPI, endAPI);
        }, 500);
        
        return () => clearTimeout(timeoutId);
      }
    }
  }, [formData.start_datetime, formData.end_datetime, courtId, selectedHours]);

  // Convert datetime-local format (YYYY-MM-DDTHH:mm) to API format (YYYY-MM-DD HH:mm:ss)
  const convertToAPIDateTime = (datetimeLocal) => {
    if (!datetimeLocal) return null;
    // datetime-local format: YYYY-MM-DDTHH:mm
    // API format: YYYY-MM-DD HH:mm:ss
    return datetimeLocal.replace('T', ' ') + ':00';
  };

  // Removed calculatePriceForBooking - using fixed prices instead

  const checkAvailabilityForBooking = async (startAPI, endAPI) => {
    try {
      const result = await checkAvailability(
        courtId,
        startAPI,
        endAPI
      );
      
      // Handle backend response format: {success: true, data: {available: true, errors: []}}
      let availabilityData;
      if (result && typeof result === 'object') {
        if (result.success !== undefined && result.data) {
          // Backend format: {success: true, data: {available: true, errors: []}}
          availabilityData = result.data;
        } else if (result.available !== undefined) {
          // Direct format: {available: true, errors: []}
          availabilityData = result;
        } else {
          // Unexpected format
          console.warn('Unexpected availability response format:', result);
          availabilityData = { available: false, errors: ['Hibás válasz a szervertől'] };
        }
      } else {
        availabilityData = { available: false, errors: ['Hibás válasz a szervertől'] };
      }
      
      setAvailability(availabilityData);
      
      // Clear errors if available
      if (availabilityData.available) {
        setErrors([]);
      }
    } catch (error) {
      console.error('Availability check failed:', error);
      // Don't show error immediately, maybe it's temporary
      setAvailability({ available: false, errors: ['Nem sikerült ellenőrizni az elérhetőséget. Próbáld újra!'] });
    }
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
    setErrors([]);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErrors([]);
    setSubmitting(true);

    // Check authentication again before submitting
    if (!isAuthenticated()) {
      navigate('/login', {
        state: { 
          from: location.pathname,
          courtId: courtId,
          formData: formData
        }
      });
      setSubmitting(false);
      return;
    }

    // Convert to API format
    const startAPI = convertToAPIDateTime(formData.start_datetime);
    const endAPI = convertToAPIDateTime(formData.end_datetime);

    // Re-check availability before submitting
    if (startAPI && endAPI) {
      const lastCheck = await checkAvailability(courtId, startAPI, endAPI);
      if (!lastCheck.available || (lastCheck.success !== undefined && !lastCheck.success)) {
        const errors = lastCheck.errors || lastCheck.data?.errors || ['A pálya nem elérhető erre az időpontra'];
        setErrors(errors);
        setAvailability(lastCheck.data || lastCheck);
        setSubmitting(false);
        return;
      }
    }

    const result = await createBooking({
      customer_name: formData.customer_name,
      customer_email: formData.customer_email,
      customer_phone: formData.customer_phone,
      start_datetime: startAPI,
      end_datetime: endAPI,
      court_id: parseInt(courtId),
    });

    if (result.success) {
      // Trigger refresh event for calendar
      window.dispatchEvent(new Event('bookingCreated'));
      
      // Navigate back to court detail page (calendar)
      navigate(`/courts/${courtId}`, {
        state: { 
          message: 'Foglalás sikeresen létrehozva!', 
          bookingCreated: true,
          emailInfo: result.email || null
        },
      });
    } else {
      // Check if it's an authentication error
      const errorMessage = result.errors?.[0] || result.message || 'A foglalás létrehozása sikertelen volt';
      if (errorMessage.includes('token') || errorMessage.includes('authentication') || errorMessage.includes('Invalid')) {
        setErrors(['A bejelentkezési munkamenet lejárt. Kérjük, jelentkezz be újra.']);
        // Redirect to login after a short delay
        setTimeout(() => {
          navigate('/login', { 
            state: { from: `/book/${courtId}`, message: 'A bejelentkezési munkamenet lejárt. Kérjük, jelentkezz be újra.' }
          });
        }, 2000);
      } else {
        setErrors(result.errors || [errorMessage]);
      }
    }
    
    setSubmitting(false);
  };

  const getMinDateTime = () => {
    const now = new Date();
    now.setHours(now.getHours() + 2); // Minimum 2 hours in advance
    return now.toISOString().slice(0, 16);
  };

  if (authLoading || courtLoading) {
    return <div className="text-center py-8">Betöltés...</div>;
  }

  // Don't render if not authenticated (will redirect)
  if (!isAuthenticated()) {
    return null;
  }

  return (
    <div className="max-w-3xl mx-auto">
      <div className="card">
        <div className="flex items-center mb-6">
          <Calendar className="h-6 w-6 mr-3 text-primary-600" />
          <h2 className="text-2xl md:text-3xl font-bold text-gray-900">Foglalás létrehozása</h2>
        </div>
        
        {court && (
          <div className="mb-8 p-5 bg-gradient-to-r from-primary-50 to-primary-100 rounded-xl border border-primary-200">
            <div className="flex items-center justify-between flex-wrap gap-3">
              <div>
                <h3 className="font-bold text-xl mb-1 text-gray-900">{court.name}</h3>
                <p className="text-sm text-gray-600 flex items-center">
                  <span className="badge badge-info mr-2">{court.surface_name || court.surface}</span>
                  {court.is_indoor ? 'Beltéri' : 'Kültéri'} pálya
                </p>
              </div>
            </div>
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              <User className="h-4 w-4 inline mr-1" />
              Név *
            </label>
            <input
              type="text"
              name="customer_name"
              value={formData.customer_name}
              onChange={handleChange}
              required
              className="input"
              placeholder="Teljes név"
            />
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                <Mail className="h-4 w-4 inline mr-1" />
                Email *
              </label>
              <input
                type="email"
                name="customer_email"
                value={formData.customer_email}
                onChange={handleChange}
                required
                className="input"
                placeholder="email@example.com"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                <Phone className="h-4 w-4 inline mr-1" />
                Telefon
              </label>
              <input
                type="tel"
                name="customer_phone"
                value={formData.customer_phone}
                onChange={handleChange}
                className="input"
                placeholder="+36 20 123 4567"
              />
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                <Calendar className="h-4 w-4 inline mr-1" />
                Kezdés *
              </label>
              <input
                type="datetime-local"
                name="start_datetime"
                value={formData.start_datetime}
                onChange={handleChange}
                required
                min={getMinDateTime()}
                className="input"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                <Clock className="h-4 w-4 inline mr-1" />
                Időtartam *
              </label>
              <div className="grid grid-cols-3 gap-2">
                {[1, 2, 3].map((hours) => (
                  <button
                    key={hours}
                    type="button"
                    onClick={() => setSelectedHours(hours)}
                    className={`p-3 rounded-lg border-2 font-semibold transition-all ${
                      selectedHours === hours
                        ? 'border-primary-500 bg-primary-50 text-primary-700 shadow-md'
                        : 'border-gray-200 bg-white text-gray-700 hover:border-primary-300 hover:bg-primary-50'
                    }`}
                  >
                    {hours} {hours === 1 ? 'óra' : 'óra'}
                  </button>
                ))}
              </div>
              <input
                type="hidden"
                name="end_datetime"
                value={formData.end_datetime}
              />
            </div>
          </div>

          {availability && (
            <div className={`p-4 rounded-lg ${
              availability.available
                ? 'bg-green-50 border border-green-200'
                : 'bg-red-50 border border-red-200'
            }`}>
              {availability.available ? (
                <p className="text-green-800 text-sm font-medium">
                  ✓ A pálya elérhető erre az időpontra
                </p>
              ) : (
                <div>
                  <p className="text-red-800 font-medium mb-1">⚠️ A pálya nem elérhető</p>
                  <ul className="text-red-700 text-sm list-disc list-inside">
                    {availability.errors && availability.errors.length > 0 ? (
                      availability.errors.map((error, idx) => (
                        <li key={idx}>{error}</li>
                      ))
                    ) : (
                      <li>Erre az időpontra a pálya már foglalt vagy nem elérhető</li>
                    )}
                  </ul>
                </div>
              )}
            </div>
          )}

          {formData.start_datetime && availability?.available && (
            <div className="p-4 bg-primary-50 rounded-lg border border-primary-200">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-gray-600">Időtartam</p>
                  <p className="font-semibold">{selectedHours} {selectedHours === 1 ? 'óra' : 'óra'}</p>
                </div>
                <div className="text-right">
                  <p className="text-sm text-gray-600">Összeg</p>
                  <p className="text-2xl font-bold text-primary-600">
                    {formatPrice(fixedPrices[selectedHours] || fixedPrices[1], 'HUF').replace(/[^0-9 ]/g, '').trim()} HUF
                  </p>
                </div>
              </div>
            </div>
          )}

          {errors.length > 0 && (
            <div className="p-4 bg-red-50 border border-red-200 rounded-lg">
              <ul className="text-red-800 text-sm list-disc list-inside">
                {errors.map((error, idx) => (
                  <li key={idx}>{error}</li>
                ))}
              </ul>
            </div>
          )}

          <div className="pt-6 border-t border-gray-200 flex flex-col sm:flex-row gap-3">
            <button
              type="submit"
              disabled={submitting || !availability?.available}
              className="btn btn-primary flex-1 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 py-4 text-lg font-semibold shadow-lg hover:shadow-xl"
            >
              {submitting ? (
                <>
                  <div className="animate-spin rounded-full h-6 w-6 border-b-2 border-white"></div>
                  Foglalás létrehozása...
                </>
              ) : (
                <>
                  <Calendar className="h-6 w-6" />
                  Foglalás megerősítése
                </>
              )}
            </button>
            <button
              type="button"
              onClick={() => navigate(-1)}
              className="btn btn-secondary px-6 py-4"
            >
              Mégse
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default BookingForm;

