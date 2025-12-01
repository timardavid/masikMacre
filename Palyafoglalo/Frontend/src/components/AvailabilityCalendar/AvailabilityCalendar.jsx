/**
 * Availability Calendar Component
 * Displays court availability in a calendar/table format
 */

import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { format, startOfWeek, addDays, addWeeks, subWeeks, isSameDay, parseISO, isBefore, isAfter } from 'date-fns';
import { ChevronLeft, ChevronRight, Calendar as CalendarIcon, Clock } from 'lucide-react';
import { bookingsAPI } from '../../services/api';

const AvailabilityCalendar = ({ courtId, startDate, endDate }) => {
  const navigate = useNavigate();
  const [availability, setAvailability] = useState(null);
  const [loading, setLoading] = useState(true);
  const [currentWeek, setCurrentWeek] = useState(new Date());
  const [selectedDate, setSelectedDate] = useState(new Date());
  const [hoveredSlot, setHoveredSlot] = useState(null);

  // Time slots (hourly intervals - whole hours only)
  const timeSlots = [];
  for (let hour = 8; hour < 22; hour++) {
    timeSlots.push(`${hour.toString().padStart(2, '0')}:00`);
  }
  
  // Handle slot click - navigate to booking page with pre-filled data
  const handleSlotClick = (date, timeSlot) => {
    const isPast = isBefore(date, new Date()) || (isSameDay(date, new Date()) && timeSlot < format(new Date(), 'HH:mm'));
    if (isPast) return;
    
    const isBooked = isTimeSlotBooked(date, timeSlot);
    if (isBooked) return;
    
    // Calculate start and end time (1 hour booking)
    const startHour = parseInt(timeSlot.split(':')[0]);
    const endHour = startHour + 1;
    const dateStr = format(date, 'yyyy-MM-dd');
    
    // Format for datetime-local input (YYYY-MM-DDTHH:mm)
    const startDateTime = `${dateStr}T${startHour.toString().padStart(2, '0')}:00`;
    const endDateTime = `${dateStr}T${endHour.toString().padStart(2, '0')}:00`;
    
    // Navigate to booking page with pre-filled data
    navigate(`/book/${courtId}`, {
      state: {
        courtId,
        startDatetime: startDateTime,
        endDatetime: endDateTime,
      }
    });
  };

  useEffect(() => {
    if (!courtId) return;
    fetchAvailability();
  }, [courtId, currentWeek]);

  // Expose refresh function via useEffect cleanup or window event
  useEffect(() => {
    const handleBookingUpdate = () => {
      fetchAvailability();
    };
    
    window.addEventListener('bookingCreated', handleBookingUpdate);
    return () => window.removeEventListener('bookingCreated', handleBookingUpdate);
  }, [courtId]);

  const fetchAvailability = async () => {
    try {
      setLoading(true);
      const weekStart = startOfWeek(currentWeek, { weekStartsOn: 1 }); // Monday
      const weekEnd = addDays(weekStart, 6);
      
      // Fetch actual bookings for the week
      try {
        const bookingsResponse = await bookingsAPI.list({
          court_id: courtId,
          start_date: format(weekStart, 'yyyy-MM-dd'),
          end_date: format(weekEnd, 'yyyy-MM-dd'),
        });

        // Handle different response formats
        let bookings = [];
        if (bookingsResponse.success) {
          if (Array.isArray(bookingsResponse.data)) {
            bookings = bookingsResponse.data;
          } else if (bookingsResponse.data?.bookings) {
            bookings = bookingsResponse.data.bookings;
          } else if (Array.isArray(bookingsResponse)) {
            bookings = bookingsResponse;
          }
        }
        
        // Filter out cancelled bookings explicitly (double-check)
        bookings = bookings.filter(booking => 
          booking.status && 
          booking.status !== 'cancelled' && 
          booking.start_datetime && 
          booking.end_datetime
        );
        
        setAvailability({ bookings });
      } catch (err) {
        // If list requires auth, try without filters
        console.warn('Could not fetch bookings list:', err);
        setAvailability({ bookings: [] });
      }
    } catch (error) {
      console.error('Failed to fetch availability:', error);
      setAvailability({ bookings: [] });
    } finally {
      setLoading(false);
    }
  };

  const isTimeSlotBooked = (date, timeSlot) => {
    if (!availability?.bookings || availability.bookings.length === 0) return false;
    
    const slotDateStr = format(date, 'yyyy-MM-dd');
    const slotStart = new Date(`${slotDateStr}T${timeSlot}:00`);
    const slotEnd = new Date(slotStart.getTime() + 60 * 60 * 1000); // 1 hour (whole hour booking)

    return availability.bookings.some(booking => {
      // Strict validation: must have both dates and not be cancelled
      if (!booking.start_datetime || !booking.end_datetime) return false;
      if (!booking.status || booking.status === 'cancelled') return false;
      
      try {
        const bookingStart = new Date(booking.start_datetime);
        const bookingEnd = new Date(booking.end_datetime);
        
        // Validate dates
        if (isNaN(bookingStart.getTime()) || isNaN(bookingEnd.getTime())) return false;
        
        // Check if slot overlaps with booking (exclusive boundaries to avoid edge cases)
        // A slot is booked if: slotStart < bookingEnd AND slotEnd > bookingStart
        const overlaps = slotStart < bookingEnd && slotEnd > bookingStart;
        return overlaps;
      } catch (e) {
        console.warn('Error checking booking overlap:', e, booking);
        return false;
      }
    });
  };

  const getTimeSlotClass = (date, timeSlot, isHovered) => {
    if (isTimeSlotBooked(date, timeSlot)) {
      return 'bg-red-50 border-2 border-red-300 text-red-700 cursor-not-allowed opacity-75';
    }
    if (isHovered) {
      return 'bg-primary-100 border-2 border-primary-500 text-primary-900 cursor-pointer shadow-lg scale-105';
    }
    return 'bg-green-50 border-2 border-green-300 text-green-800 hover:bg-green-100 hover:border-green-400 hover:shadow-md cursor-pointer transition-all duration-200';
  };

  const weekStart = startOfWeek(currentWeek, { weekStartsOn: 1 });
  const weekDays = Array.from({ length: 7 }, (_, i) => addDays(weekStart, i));

  const previousWeek = () => setCurrentWeek(subWeeks(currentWeek, 1));
  const nextWeek = () => setCurrentWeek(addWeeks(currentWeek, 1));
  const goToToday = () => setCurrentWeek(new Date());

  if (loading) {
    return (
      <div className="text-center py-8">
        <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
        <p className="mt-4 text-gray-600">Betöltés...</p>
      </div>
    );
  }

  return (
    <div className="card shadow-xl">
      <div className="flex flex-col md:flex-row md:items-center justify-between mb-6 pb-6 border-b-2 border-gray-200">
        <h3 className="text-2xl md:text-3xl font-bold flex items-center text-gray-900 mb-4 md:mb-0">
          <div className="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center mr-3">
            <CalendarIcon className="h-6 w-6 text-primary-600" />
          </div>
          Elérhetőség Naptár
        </h3>
        <div className="flex items-center space-x-2">
          <button
            onClick={previousWeek}
            className="btn btn-secondary p-2.5 hover:bg-gray-200 transition-all hover:scale-105"
            title="Előző hét"
          >
            <ChevronLeft className="h-5 w-5" />
          </button>
          <button
            onClick={goToToday}
            className="btn btn-primary px-5 py-2.5 font-semibold"
          >
            Ma
          </button>
          <button
            onClick={nextWeek}
            className="btn btn-secondary p-2.5 hover:bg-gray-200 transition-all hover:scale-105"
            title="Következő hét"
          >
            <ChevronRight className="h-5 w-5" />
          </button>
        </div>
      </div>

      <div className="overflow-x-auto rounded-xl border-2 border-gray-200 shadow-inner">
        <table className="w-full border-collapse bg-white">
          <thead>
            <tr className="bg-gradient-to-r from-primary-50 via-gray-50 to-primary-50">
              <th className="border-b-2 border-r-2 border-gray-300 px-5 py-4 bg-primary-100 font-bold text-left min-w-[120px] sticky left-0 z-10 shadow-md">
                <div className="flex items-center">
                  <Clock className="h-5 w-5 mr-2 text-primary-700" />
                  <span className="text-gray-800">Időpont</span>
                </div>
              </th>
              {weekDays.map((day, index) => {
                const isToday = isSameDay(day, new Date());
                return (
                  <th
                    key={index}
                    className={`border-b-2 border-gray-300 px-5 py-4 font-bold text-center min-w-[140px] ${
                      isToday 
                        ? 'bg-primary-100 border-primary-300 text-primary-800 shadow-inner' 
                        : 'bg-white text-gray-700'
                    }`}
                  >
                    <div className="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">
                      {format(day, 'EEE')}
                    </div>
                    <div className={`text-xl font-bold ${isToday ? 'text-primary-700' : 'text-gray-900'}`}>
                      {format(day, 'd')}
                    </div>
                    <div className="text-xs font-medium text-gray-500">
                      {format(day, 'MMM')}
                    </div>
                  </th>
                );
              })}
            </tr>
          </thead>
          <tbody>
            {timeSlots.map((timeSlot, slotIndex) => {
              const hour = parseInt(timeSlot.split(':')[0]);
              const nextHour = hour + 1;
              return (
                <tr key={slotIndex} className="hover:bg-gray-50/50 transition-colors">
                  <td className="border-r-2 border-gray-200 px-5 py-4 bg-gray-100 font-bold text-sm text-gray-800 sticky left-0 z-10 shadow-sm">
                    {timeSlot} - {nextHour.toString().padStart(2, '0')}:00
                  </td>
                  {weekDays.map((day, dayIndex) => {
                    const isBooked = isTimeSlotBooked(day, timeSlot);
                    const isPast = isBefore(day, new Date()) || (isSameDay(day, new Date()) && timeSlot < format(new Date(), 'HH:mm'));
                    const slotId = `${format(day, 'yyyy-MM-dd')}-${timeSlot}`;
                    const isHovered = hoveredSlot === slotId;
                    const cellClass = isPast 
                      ? 'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed'
                      : getTimeSlotClass(day, timeSlot, isHovered);
                    
                    const handleClick = () => handleSlotClick(day, timeSlot);
                    const handleMouseEnter = () => !isPast && !isBooked && setHoveredSlot(slotId);
                    const handleMouseLeave = () => setHoveredSlot(null);
                    
                    return (
                      <td
                        key={dayIndex}
                        className={`border-2 border-gray-200 px-4 py-5 text-center transition-all duration-200 font-medium ${cellClass}`}
                        onClick={handleClick}
                        onMouseEnter={handleMouseEnter}
                        onMouseLeave={handleMouseLeave}
                        title={
                          isPast
                            ? 'Múltbeli időpont'
                            : isBooked
                            ? `Foglalt: ${format(day, 'yyyy-MM-dd')} ${timeSlot}`
                            : `Kattints a foglaláshoz: ${format(day, 'yyyy-MM-dd')} ${timeSlot}-${nextHour.toString().padStart(2, '0')}:00`
                        }
                      >
                        {isPast ? (
                          <span className="text-gray-400 text-xs">⏰</span>
                        ) : isBooked ? (
                          <span className="text-red-600 font-semibold">✕</span>
                        ) : (
                          <span className="text-green-600 font-bold text-sm">✓</span>
                        )}
                      </td>
                    );
                  })}
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>

      <div className="mt-6 pt-4 border-t border-gray-200">
        <div className="flex flex-wrap items-center justify-center gap-6 text-sm">
          <div className="flex items-center bg-green-50 px-4 py-2 rounded-lg border border-green-200">
            <div className="w-5 h-5 bg-green-100 border-2 border-green-300 rounded mr-3 flex items-center justify-center">
              <span className="text-green-600 font-bold text-xs">✓</span>
            </div>
            <span className="text-gray-700 font-medium">Szabad - Kattints a foglaláshoz</span>
          </div>
          <div className="flex items-center bg-red-50 px-4 py-2 rounded-lg border border-red-200">
            <div className="w-5 h-5 bg-red-100 border-2 border-red-300 rounded mr-3 flex items-center justify-center">
              <span className="text-red-600 font-bold text-xs">✕</span>
            </div>
            <span className="text-gray-700 font-medium">Foglalt</span>
          </div>
          <div className="flex items-center bg-gray-50 px-4 py-2 rounded-lg border border-gray-200">
            <div className="w-5 h-5 bg-gray-100 border-2 border-gray-300 rounded mr-3 flex items-center justify-center">
              <span className="text-gray-400 text-xs">⏰</span>
            </div>
            <span className="text-gray-600 font-medium">Múltbeli</span>
          </div>
        </div>
        <p className="text-center text-xs text-gray-500 mt-4">
          💡 Tipp: Kattints egy szabad időpontra a gyors foglaláshoz
        </p>
      </div>
    </div>
  );
};

export default AvailabilityCalendar;

