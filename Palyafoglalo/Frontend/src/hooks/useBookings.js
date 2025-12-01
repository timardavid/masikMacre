/**
 * useBookings Hook
 * Custom hook for managing bookings data
 */

import { useState, useEffect } from 'react';
import { bookingsAPI } from '../services/api';

export const useBookings = (filters = {}) => {
  const [bookings, setBookings] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchBookings();
  }, [JSON.stringify(filters)]);

  const fetchBookings = async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await bookingsAPI.list(filters);
      if (response.success) {
        setBookings(response.data?.bookings || []);
      } else {
        setError(response.message || 'Failed to load bookings');
      }
    } catch (err) {
      setError(err.message || 'Failed to load bookings');
    } finally {
      setLoading(false);
    }
  };

  const createBooking = async (bookingData) => {
    try {
      const response = await bookingsAPI.create(bookingData);
      if (response.success) {
        await fetchBookings();
        // Include email info if available
        return { 
          success: true, 
          booking: response.data,
          email: response.data?.email || null
        };
      }
      return { success: false, errors: response.errors || [response.message] };
    } catch (err) {
      return {
        success: false,
        errors: Array.isArray(err.errors) ? err.errors : [err.message || 'Failed to create booking'],
      };
    }
  };

  const cancelBooking = async (bookingId) => {
    try {
      const response = await bookingsAPI.cancel(bookingId);
      if (response.success) {
        await fetchBookings();
        return { success: true };
      }
      return { success: false, errors: response.errors || [response.message] };
    } catch (err) {
      return {
        success: false,
        errors: Array.isArray(err.errors) ? err.errors : [err.message || 'Failed to cancel booking'],
      };
    }
  };

  const checkAvailability = async (courtId, startDatetime, endDatetime, excludeBookingId = null) => {
    try {
      const response = await bookingsAPI.checkAvailability(
        courtId,
        startDatetime,
        endDatetime,
        excludeBookingId
      );
      // Axios returns response.data, which is {success: true, data: {available: true, errors: []}}
      // Return the full response structure so BookingForm can handle it
      return response.data || response;
    } catch (err) {
      return {
        success: false,
        data: {
          available: false,
          errors: [err.response?.data?.message || err.message || 'Failed to check availability'],
        }
      };
    }
  };

  const calculatePrice = async (courtId, startDatetime, endDatetime) => {
    try {
      const response = await bookingsAPI.calculatePrice(courtId, startDatetime, endDatetime);
      if (response.success) {
        return { success: true, data: response.data };
      }
      return { success: false, message: response.message };
    } catch (err) {
      return {
        success: false,
        message: err.message || 'Failed to calculate price',
      };
    }
  };

  return {
    bookings,
    loading,
    error,
    createBooking,
    cancelBooking,
    checkAvailability,
    calculatePrice,
    refetch: fetchBookings,
  };
};

