/**
 * API Configuration
 * Centralized API endpoint configuration
 * All endpoints are relative to baseURL in api.js
 */

export const API_ENDPOINTS = {
  // Auth
  AUTH: {
    LOGIN: `auth/login.php`,
    ME: `auth/me.php`,
    REGISTER: `auth/register.php`,
    FORGOT_PASSWORD: `auth/forgot-password.php`,
    RESET_PASSWORD: `auth/reset-password.php`,
  },
  
  // Courts
  COURTS: {
    LIST: `courts.php`,
    DETAIL: (id) => `courts/_id.php?id=${id}`,
    AVAILABILITY: (id) => `courts/_id.php?id=${id}&action=availability`,
  },
  SURFACES: `surfaces.php`,
  
  // Bookings
  BOOKINGS: {
    LIST: `bookings.php`,
    DETAIL: (id) => `bookings/_id.php?id=${id}`,
    CREATE: `bookings.php`,
    UPDATE: (id) => `bookings/_id.php?id=${id}`,
    CANCEL: (id) => `bookings/_id.php?id=${id}`,
    CHECK_AVAILABILITY: `bookings/availability.php`,
    CALCULATE_PRICE: `bookings/calculate-price.php`,
  },
  
  // Pricing
  PRICING: {
    RULES: `pricing/rules.php`,
    COURT_RULES: (courtId) => `pricing/rules/court/_courtId.php?courtId=${courtId}`,
    CALCULATE: `pricing/calculate.php`,
  },
  
  // Health
  HEALTH: `health.php`,
};

