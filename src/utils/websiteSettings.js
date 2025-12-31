// Website Settings Service - Fetches website settings from PHP API

import API_BASE_URL, { apiRequest } from '../config/api';

/**
 * Fetch all website settings from the API
 * @returns {Promise<Object>} Object containing all website settings
 */
export const fetchWebsiteSettings = async () => {
  try {
    const data = await apiRequest('website-settings.php');
    if (data.success) {
      return data.data;
    }
    return getDefaultSettings();
  } catch (error) {
    console.error('Error fetching website settings:', error);
    return getDefaultSettings();
  }
};

/**
 * Get default website settings
 * @returns {Object} Default settings object
 */
const getDefaultSettings = () => {
  return {
    website_logo: '',
    logo_text_line1: 'Hamro Digi',
    logo_text_line2: 'CART',
    website_title: 'Hamro Digi Cart',
    website_tagline: 'Best Digital Product In India',
    website_description: '',
    facebook_url: '',
    instagram_url: '',
    youtube_url: '',
    twitter_url: '',
    whatsapp_url: '',
    footer_copyright: `Copyright (c) ${new Date().getFullYear()}`,
    contact_email: '',
    contact_phone: '',
    contact_address: '',
    banner1_title: 'WE ARE Creators DIGITAL PRODUCT',
    banner1_subtitle: 'Sell Digital Products For Free create Store',
    banner1_image: '',
    banner2_title: 'WE ARE Creators DIGITAL PRODUCT',
    banner2_subtitle: 'Digital Products Selling Website',
    banner2_image: '',
    popup_enabled: '0',
    popup_title: '',
    popup_content: '',
    popup_image: ''
  };
};

/**
 * Format copyright text with current year
 * @param {string} copyrightText - Copyright text (may contain {year})
 * @returns {string} Formatted copyright text
 */
export const formatCopyright = (copyrightText) => {
  if (!copyrightText) {
    return `Copyright (c) ${new Date().getFullYear()}`;
  }
  return copyrightText.replace('{year}', new Date().getFullYear());
};



