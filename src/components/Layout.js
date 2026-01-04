import React, { useEffect } from 'react';
import { Outlet } from 'react-router-dom';
import Header from './Header';
import Footer from './Footer';
import { useWebsiteSettings } from '../hooks/useWebsiteSettings';

const Layout = () => {
  const { settings } = useWebsiteSettings();

  useEffect(() => {
    // Update favicon if set in website settings
    if (settings?.website_favicon) {
      // Remove existing favicon links
      const existingFavicons = document.querySelectorAll('link[rel*="icon"]');
      existingFavicons.forEach(link => link.remove());
      
      // Add new favicon
      const link = document.createElement('link');
      link.rel = 'icon';
      link.href = settings.website_favicon;
      document.head.appendChild(link);
      
      // Also update apple-touch-icon if it's an image
      const appleTouchIcon = document.querySelector('link[rel="apple-touch-icon"]');
      if (appleTouchIcon && settings.website_favicon) {
        appleTouchIcon.href = settings.website_favicon;
      }
    }
  }, [settings?.website_favicon]);

  return (
    <>
      <Header />
      <Outlet />
      <Footer />
    </>
  );
};

export default Layout;

