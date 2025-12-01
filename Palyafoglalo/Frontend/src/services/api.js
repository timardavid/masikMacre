/**
 * API Service
 * Handles all HTTP requests to the backend
 */

import axios from 'axios';
import { API_ENDPOINTS } from '../config/api';

// Create axios instance
// In dev mode, Vite proxy handles /Palyafoglalo/Bakcend
// In production, direct path works
const api = axios.create({
  baseURL: import.meta.env.DEV 
    ? '/Palyafoglalo/Bakcend/api/v1'  // Dev: uses Vite proxy
    : '/Palyafoglalo/Bakcend/api/v1', // Production: direct path
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor - Add auth token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
      // Debug log for development
      if (import.meta.env.DEV) {
        console.log('🔑 Adding token to request:', config.url, token.substring(0, 20) + '...');
      }
    } else {
      // Debug log if no token found
      if (import.meta.env.DEV) {
        console.warn('⚠️ No token found in localStorage for request:', config.url);
      }
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Response interceptor - Handle errors
api.interceptors.response.use(
  (response) => response.data,
  async (error) => {
    // Store original response for error handling
    const status = error.response?.status;
    const errorData = error.response?.data || error;
    
    if (status === 401) {
      // Check if this is an auth endpoint (login, register, etc.)
      const isAuthEndpoint = error.config?.url?.includes('/auth/');
      const isMeEndpoint = error.config?.url?.includes('/auth/me');
      
      // For auth endpoints, just return error
      if (isAuthEndpoint && !isMeEndpoint) {
        return Promise.reject({
          ...errorData,
          status: status,
          response: error.response,
          message: errorData.message || error.message || 'An error occurred'
        });
      }
      
      // For protected endpoints, check if token is expired
      // Try to refresh token or redirect to login
      const token = localStorage.getItem('auth_token');
      if (token && !isMeEndpoint) {
        // Log the error for debugging
        console.error('401 Unauthorized - Token may be expired:', {
          url: error.config?.url,
          tokenPreview: token.substring(0, 30) + '...',
          error: errorData.message
        });
        
        // For booking endpoints, show user-friendly message
        if (error.config?.url?.includes('/bookings')) {
          console.warn('⚠️ Booking failed due to authentication. Please login again.');
        }
      }
    }
    
    // Return error with status code preserved
    return Promise.reject({
      ...errorData,
      status: status,
      response: error.response,
      message: errorData.message || error.message || 'An error occurred'
    });
  }
);

// Auth API
export const authAPI = {
  login: (email, password) =>
    api.post(API_ENDPOINTS.AUTH.LOGIN, { email, password }),
  
  me: () => api.get(API_ENDPOINTS.AUTH.ME),
  
  register: (data) => api.post(API_ENDPOINTS.AUTH.REGISTER, data),
  
  forgotPassword: (email) => 
    api.post(API_ENDPOINTS.AUTH.FORGOT_PASSWORD, { email }),
  
  resetPassword: (email, code, password) =>
    api.post(API_ENDPOINTS.AUTH.RESET_PASSWORD, { email, code, password }),
};

// Courts API
export const courtsAPI = {
  list: () => api.get(API_ENDPOINTS.COURTS.LIST),
  
  detail: (id) => api.get(API_ENDPOINTS.COURTS.DETAIL(id)),
  
  availability: (id, startDate, endDate) =>
    api.get(API_ENDPOINTS.COURTS.AVAILABILITY(id), {
      params: { start: startDate, end: endDate },
    }),
  
  surfaces: () => api.get(API_ENDPOINTS.SURFACES),
};

// Bookings API
export const bookingsAPI = {
  list: (filters = {}) =>
    api.get(API_ENDPOINTS.BOOKINGS.LIST, { params: filters }),
  
  detail: (id) => api.get(API_ENDPOINTS.BOOKINGS.DETAIL(id)),
  
  create: (data) => api.post(API_ENDPOINTS.BOOKINGS.CREATE, data),
  
  update: (id, data) => api.put(API_ENDPOINTS.BOOKINGS.UPDATE(id), data),
  
  cancel: (id) => api.delete(API_ENDPOINTS.BOOKINGS.CANCEL(id)),
  
  checkAvailability: (courtId, startDatetime, endDatetime, excludeBookingId = null) =>
    api.get(API_ENDPOINTS.BOOKINGS.CHECK_AVAILABILITY, {
      params: {
        court_id: courtId,
        start_datetime: startDatetime,
        end_datetime: endDatetime,
        exclude_booking_id: excludeBookingId,
      },
    }),
  
  calculatePrice: (courtId, startDatetime, endDatetime) =>
    api.get(API_ENDPOINTS.BOOKINGS.CALCULATE_PRICE, {
      params: {
        court_id: courtId,
        start_datetime: startDatetime,
        end_datetime: endDatetime,
      },
    }),
};

// Pricing API
export const pricingAPI = {
  rules: () => api.get(API_ENDPOINTS.PRICING.RULES),
  
  courtRules: (courtId) => api.get(API_ENDPOINTS.PRICING.COURT_RULES(courtId)),
  
  calculate: (courtId, startDatetime, endDatetime) =>
    api.get(API_ENDPOINTS.PRICING.CALCULATE, {
      params: {
        court_id: courtId,
        start_datetime: startDatetime,
        end_datetime: endDatetime,
      },
    }),
};

    // Court Reviews API
    export const courtReviewsAPI = {
      list: (courtId, options = {}) =>
        api.get(`/courts/${courtId}/reviews`, { params: options }),
      
      stats: (courtId) =>
        api.get(`/reviews/stats.php`, { params: { court_id: courtId } }),
  
  create: (courtId, data) => {
    const formData = new FormData();
    Object.keys(data).forEach(key => {
      if (data[key] !== null && data[key] !== undefined) {
        formData.append(key, data[key]);
      }
    });
    return api.post(`/courts/${courtId}/reviews`, data);
  },
  
  update: (reviewId, data) =>
    api.put(`/reviews/${reviewId}`, data),
  
  delete: (reviewId) =>
    api.delete(`/reviews/${reviewId}`),
};

// Court Images API
export const courtImagesAPI = {
  upload: (courtId, file, data = {}) => {
    const formData = new FormData();
    formData.append('image', file);
    if (data.alt_text) formData.append('alt_text', data.alt_text);
    if (data.display_order !== undefined) formData.append('display_order', data.display_order);
    
    return api.post(`/courts/${courtId}/images`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  
  delete: (imageId) =>
    api.delete(`/courts/images/${imageId}`),
};

export default api;

