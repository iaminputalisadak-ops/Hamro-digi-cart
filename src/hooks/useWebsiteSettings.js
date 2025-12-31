import { useState, useEffect } from 'react';
import { fetchWebsiteSettings } from '../utils/websiteSettings';

/**
 * Custom hook to fetch and use website settings
 * @returns {Object} Website settings object
 */
export const useWebsiteSettings = () => {
  const [settings, setSettings] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const loadSettings = async () => {
      try {
        const websiteSettings = await fetchWebsiteSettings();
        setSettings(websiteSettings);
        
        // Update document title
        if (websiteSettings.website_title) {
          document.title = websiteSettings.website_title;
        }
      } catch (error) {
        console.error('Error loading website settings:', error);
      } finally {
        setLoading(false);
      }
    };
    
    loadSettings();
  }, []);

  return { settings, loading };
};





