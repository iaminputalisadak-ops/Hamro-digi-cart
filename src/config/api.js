/**
 * API Configuration
 * Update this URL to match your PHP backend location
 */

// For development - adjust this to your backend URL
// If backend is in same domain: '/backend/api'
// If backend is on different domain: use environment variable
// For production: Uses environment variable or defaults to relative path

// Use relative path in production to avoid localhost references that trigger security warnings
const API_BASE_URL = process.env.REACT_APP_API_URL || (process.env.NODE_ENV === 'production' ? '/backend/api' : 'http://localhost:8000/api');

export default API_BASE_URL;

/**
 * Make API request
 */
export const apiRequest = async (endpoint, options = {}) => {
  const url = `${API_BASE_URL}/${endpoint}`;
  
  const defaultOptions = {
    headers: {
      'Content-Type': 'application/json',
    },
  };

  const config = {
    ...defaultOptions,
    ...options,
    headers: {
      ...defaultOptions.headers,
      ...(options.headers || {}),
    },
  };

  try {
    const response = await fetch(url, config);
    
    // Check if response is JSON
    const contentType = response.headers.get('content-type');
    let data;
    
    if (contentType && contentType.includes('application/json')) {
      try {
        data = await response.json();
      } catch (jsonError) {
        const text = await response.text();
        console.error('JSON Parse Error:', text);
        throw new Error('Invalid JSON response from server');
      }
    } else {
      const text = await response.text();
      console.error('Non-JSON Response:', text);
      throw new Error('Server returned non-JSON response');
    }
    
    if (!response.ok) {
      throw new Error(data.error || `API request failed (${response.status})`);
    }
    
    return data;
  } catch (error) {
    console.error('API Error:', error);
    // If it's already an Error, rethrow it
    if (error instanceof Error) {
      throw error;
    }
    // Otherwise wrap it
    throw new Error(error.message || 'Network error. Please check your connection.');
  }
};

