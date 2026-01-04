// Homepage Banner Service - Fetches homepage banners from PHP API

import { apiRequest } from '../config/api';

/**
 * Fetch active homepage banners (ordered by display_order)
 * @returns {Promise<Array>}
 */
export const fetchActiveHomepageBanners = async () => {
  try {
    const data = await apiRequest('homepage-banners.php');
    if (data.success) {
      return data.data || [];
    }
    return [];
  } catch (error) {
    console.error('Error fetching homepage banners:', error);
    return [];
  }
};


