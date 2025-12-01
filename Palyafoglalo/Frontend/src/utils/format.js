/**
 * Formatting utilities
 */

/**
 * Format price in cents to HUF with thousand separators
 */
export const formatPrice = (cents, currency = 'HUF') => {
  const amount = (cents / 100).toFixed(0);
  return `${amount.replace(/\B(?=(\d{3})+(?!\d))/g, ' ')} ${currency}`;
};

/**
 * Format datetime to Hungarian format
 */
export const formatDateTime = (dateTimeString) => {
  const date = new Date(dateTimeString);
  return date.toLocaleString('hu-HU', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

/**
 * Format date to Hungarian format
 */
export const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('hu-HU', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

/**
 * Format time to HH:MM
 */
export const formatTime = (timeString) => {
  const date = new Date(`2000-01-01T${timeString}`);
  return date.toLocaleTimeString('hu-HU', {
    hour: '2-digit',
    minute: '2-digit',
  });
};

/**
 * Get duration in hours and minutes
 */
export const getDuration = (startDatetime, endDatetime) => {
  const start = new Date(startDatetime);
  const end = new Date(endDatetime);
  const diffMs = end - start;
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
  const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
  
  if (diffHours > 0 && diffMinutes > 0) {
    return `${diffHours} óra ${diffMinutes} perc`;
  } else if (diffHours > 0) {
    return `${diffHours} óra`;
  } else {
    return `${diffMinutes} perc`;
  }
};

