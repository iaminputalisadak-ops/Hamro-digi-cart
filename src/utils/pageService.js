import { apiRequest } from '../config/api';

/**
 * Fetch page content by page key
 * @param {string} pageKey - The page key (e.g., 'terms-conditions', 'privacy-policy', 'refund-policy')
 * @returns {Promise<Object|null>} Page object with content or null if not found
 */
export const fetchPageByKey = async (pageKey) => {
  try {
    const data = await apiRequest(`pages.php?key=${encodeURIComponent(pageKey)}`);
    if (data.success && data.data) {
      return data.data;
    }
    return null;
  } catch (error) {
    console.error(`Error fetching page ${pageKey}:`, error);
    return null;
  }
};

/**
 * Fetch all pages
 * @returns {Promise<Array>} Array of all pages
 */
export const fetchAllPages = async () => {
  try {
    const data = await apiRequest('pages.php');
    if (data.success && data.data) {
      return data.data;
    }
    return [];
  } catch (error) {
    console.error('Error fetching pages:', error);
    return [];
  }
};


