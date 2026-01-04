import React, { useEffect } from 'react';

/**
 * Google AdSense Component
 * 
 * Usage:
 * <AdSense 
 *   adSlot="1234567890"           // Your ad unit ID from AdSense
 *   adFormat="auto"                // auto, horizontal, vertical, rectangle
 *   fullWidthResponsive={true}    // Enable responsive ads
 *   style={{ margin: '1rem 0' }}  // Custom styles
 *   className="my-ad-class"       // Custom CSS class
 * />
 * 
 * Make sure to:
 * 1. Replace 'ca-pub-XXXXXXXXXXXXXXXX' with your AdSense Publisher ID
 * 2. Get ad unit IDs from Google AdSense dashboard
 * 3. Add AdSense script to public/index.html <head> section
 */

const AdSense = ({ 
  adSlot,                    // Ad unit ID (required)
  adFormat = "auto",         // Ad format: auto, horizontal, vertical, rectangle
  fullWidthResponsive = true, // Enable responsive ads
  style = { display: 'block' },
  className = ""
}) => {
  // Replace this with your actual AdSense Publisher ID
  const publisherId = process.env.REACT_APP_ADSENSE_PUBLISHER_ID || 'ca-pub-XXXXXXXXXXXXXXXX';
  
  // Don't render ads if publisher ID is not set (prevents errors in development)
  if (publisherId === 'ca-pub-XXXXXXXXXXXXXXXX' || !publisherId) {
    if (process.env.NODE_ENV === 'development') {
      console.warn('AdSense: Publisher ID not configured. Ads will not display.');
      return (
        <div className={`adsense-placeholder ${className}`} style={style}>
          <div style={{ 
            padding: '2rem', 
            background: '#f0f0f0', 
            border: '2px dashed #ccc',
            textAlign: 'center',
            color: '#666'
          }}>
            AdSense Ad (Configure publisher ID)
          </div>
        </div>
      );
    }
    return null;
  }

  useEffect(() => {
    try {
      // Push ad to Google AdSense
      if (window.adsbygoogle && window.adsbygoogle.loaded) {
        (window.adsbygoogle = window.adsbygoogle || []).push({});
      }
    } catch (err) {
      console.error('AdSense error:', err);
    }
  }, []);

  // Don't render if no ad slot provided
  if (!adSlot) {
    console.warn('AdSense: adSlot prop is required');
    return null;
  }

  return (
    <div className={`adsense-container ${className}`} style={style}>
      <ins
        className="adsbygoogle"
        style={{ display: 'block' }}
        data-ad-client={publisherId}
        data-ad-slot={adSlot}
        data-ad-format={adFormat}
        data-full-width-responsive={fullWidthResponsive ? 'true' : 'false'}
      ></ins>
    </div>
  );
};

export default AdSense;











