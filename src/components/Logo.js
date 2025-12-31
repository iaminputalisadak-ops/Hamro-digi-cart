import React from 'react';
import logoImage from '../logo.png';
import { useWebsiteSettings } from '../hooks/useWebsiteSettings';
import './Logo.css';

const Logo = ({ size = 'default', showText = true, variant = 'header' }) => {
  const { settings } = useWebsiteSettings();
  const logoSize = size === 'small' ? 80 : size === 'large' ? 120 : 100;

  const logoUrl = settings?.website_logo || logoImage;
  const logoTextLine1 = settings?.logo_text_line1 || 'Hamro Digi';
  const logoTextLine2 = settings?.logo_text_line2 || 'CART';

  return (
    <div className={`logo-container logo-${size} logo-${variant}`}>
      <img
        src={logoUrl}
        alt={`${logoTextLine1} ${logoTextLine2} Logo`}
        className="logo-image"
        style={{ width: logoSize, height: logoSize, objectFit: 'contain' }}
        onError={(e) => {
          // Fallback to default logo if custom logo fails to load
          if (e.target.src !== logoImage) {
            e.target.src = logoImage;
          }
        }}
      />

      {showText && (
        <div className={`logo-text-container logo-text-${variant}`}>
          <div className="logo-text-line1">{logoTextLine1}</div>
          <div className="logo-text-line2">{logoTextLine2}</div>
        </div>
      )}
    </div>
  );
};

export default Logo;

