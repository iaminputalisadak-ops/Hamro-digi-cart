// Offer Service - Fetches offers from PHP API

import { apiRequest } from '../config/api';

/**
 * Fetch all offers from the API
 * @returns {Promise<Array>} Array of offer objects
 */
export const fetchAllOffers = async () => {
  try {
    const data = await apiRequest('offers.php');
    if (data.success) {
      return data.data || [];
    }
    return [];
  } catch (error) {
    console.error('Error fetching offers:', error);
    return [];
  }
};

/**
 * Generate a slug from offer title for URL
 * @param {string} title - The offer title
 * @returns {string} URL-friendly slug
 */
export const generateOfferSlug = (title) => {
  if (!title) return '';
  return title
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
};


