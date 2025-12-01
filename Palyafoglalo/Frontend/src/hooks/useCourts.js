/**
 * useCourts Hook
 * Custom hook for managing courts data
 */

import { useState, useEffect } from 'react';
import { courtsAPI } from '../services/api';

export const useCourts = () => {
  const [courts, setCourts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchCourts();
  }, []);

  const fetchCourts = async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await courtsAPI.list();
      if (response.success) {
        setCourts(response.data || []);
      } else {
        setError(response.message || 'Failed to load courts');
      }
    } catch (err) {
      setError(err.message || 'Failed to load courts');
    } finally {
      setLoading(false);
    }
  };

  return { courts, loading, error, refetch: fetchCourts };
};

export const useCourt = (courtId) => {
  const [court, setCourt] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (!courtId) {
      setLoading(false);
      return;
    }
    fetchCourt();
  }, [courtId]);

  const fetchCourt = async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await courtsAPI.detail(courtId);
      if (response.success) {
        setCourt(response.data);
      } else {
        setError(response.message || 'Court not found');
      }
    } catch (err) {
      setError(err.message || 'Failed to load court');
    } finally {
      setLoading(false);
    }
  };

  return { court, loading, error, refetch: fetchCourt };
};

export const useCourtAvailability = (courtId, startDate, endDate) => {
  const [availability, setAvailability] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const fetchAvailability = async () => {
    if (!courtId) return;
    
    try {
      setLoading(true);
      setError(null);
      const response = await courtsAPI.availability(
        courtId,
        startDate || new Date().toISOString().split('T')[0],
        endDate || new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
      );
      if (response.success) {
        setAvailability(response.data);
      } else {
        setError(response.message || 'Failed to load availability');
      }
    } catch (err) {
      setError(err.message || 'Failed to load availability');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (courtId) {
      fetchAvailability();
    }
  }, [courtId, startDate, endDate]);

  return { availability, loading, error, refetch: fetchAvailability };
};

