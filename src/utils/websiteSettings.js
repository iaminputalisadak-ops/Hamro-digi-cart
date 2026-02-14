// Website Settings Service - Fetches website settings from PHP API

import { apiRequest } from '../config/api';
import { normalizeUploadsUrl } from './assetUrl';

// Simple cache for website settings (in-memory cache)
let settingsCache = null;
let settingsCacheTime = null;
const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes cache

/**
 * Fetch all website settings from the API with caching
 * @returns {Promise<Object>} Object containing all website settings
 */
export const fetchWebsiteSettings = async (forceRefresh = false) => {
  try {
    // Return cached settings if still valid
    if (!forceRefresh && settingsCache && settingsCacheTime) {
      const now = Date.now();
      if (now - settingsCacheTime < CACHE_DURATION) {
        return settingsCache;
      }
    }
    
    const data = await apiRequest('website-settings.php');
    if (data.success) {
      // Normalize any stored "/uploads/..." URLs so they work on cPanel deployments
      // where the backend lives under "/backend" and uploads are at "/backend/uploads/...".
      const normalized = { ...data.data };
      Object.keys(normalized).forEach((key) => {
        const val = normalized[key];
        if (typeof val === 'string' && val.includes('/uploads/')) {
          normalized[key] = normalizeUploadsUrl(val);
        }
      });

      settingsCache = normalized;
      settingsCacheTime = Date.now();
      return normalized;
    }
    const defaultSettings = getDefaultSettings();
    settingsCache = defaultSettings;
    settingsCacheTime = Date.now();
    return defaultSettings;
  } catch (error) {
    console.error('Error fetching website settings:', error);
    const defaultSettings = getDefaultSettings();
    // Cache default settings for shorter duration on error
    if (!settingsCache) {
      settingsCache = defaultSettings;
      settingsCacheTime = Date.now();
    }
    return defaultSettings;
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
    website_tagline: 'Best Digital Product In Nepal',
    website_description: '',
    facebook_url: '',
    facebook_name: 'Facebook',
    facebook_icon_url: '',
    instagram_url: '',
    instagram_name: 'Instagram',
    instagram_icon_url: '',
    youtube_url: '',
    youtube_name: 'YouTube',
    youtube_icon_url: '',
    twitter_url: '',
    twitter_name: 'Twitter/X',
    twitter_icon_url: '',
    whatsapp_url: '',
    whatsapp_name: 'WhatsApp',
    whatsapp_icon_url: '',
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
    // Homepage banner slider settings
    homepage_banner_autoplay: '0',
    homepage_banner_duration: '5',
    homepage_banner_height: '260',
    homepage_banner_animation: 'slide',
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



