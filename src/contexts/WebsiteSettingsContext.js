import React, { createContext, useContext, useState, useEffect } from 'react';
import { fetchWebsiteSettings } from '../utils/websiteSettings';

const WebsiteSettingsContext = createContext(null);

export const WebsiteSettingsProvider = ({ children }) => {
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

  return (
    <WebsiteSettingsContext.Provider value={{ settings, loading }}>
      {children}
    </WebsiteSettingsContext.Provider>
  );
};

export const useWebsiteSettings = () => {
  const context = useContext(WebsiteSettingsContext);
  if (!context) {
    // Fallback for components outside provider (shouldn't happen but safe fallback)
    return { settings: null, loading: true };
  }
  return context;
};













