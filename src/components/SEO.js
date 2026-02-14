import React from 'react';
import { Helmet } from 'react-helmet-async';
import { useLocation } from 'react-router-dom';

const SEO = ({ 
  title, 
  description, 
  keywords, 
  image, 
  type = 'website',
  structuredData,
  canonicalUrl,
  robots = 'index, follow'
}) => {
  const location = useLocation();
  // Use environment variable or default to production domain
  const siteUrl = process.env.REACT_APP_SITE_URL || (process.env.NODE_ENV === 'production' ? 'https://hamrodigicart.com' : 'http://localhost:3000');
  const defaultTitle = 'Hamro Digi Cart - Best Digital Products In Nepal';
  const defaultDescription = 'Buy premium digital products, reels bundles, templates, and more. Best digital products in Nepal with instant download and lifetime access.';
  const defaultImage = `${siteUrl}/logo512.png`;
  
  const seoTitle = title ? `${title} | ${defaultTitle}` : defaultTitle;
  const seoDescription = description || defaultDescription;
  const seoImage = image || defaultImage;
  const canonical = canonicalUrl || `${siteUrl}${location.pathname}`;

  return (
    <Helmet>
      {/* Primary Meta Tags */}
      <title>{seoTitle}</title>
      <meta name="title" content={seoTitle} />
      <meta name="description" content={seoDescription} />
      {keywords && <meta name="keywords" content={keywords} />}
      <link rel="canonical" href={canonical} />

      {/* Open Graph / Facebook */}
      <meta property="og:type" content={type} />
      <meta property="og:url" content={canonical} />
      <meta property="og:title" content={seoTitle} />
      <meta property="og:description" content={seoDescription} />
      <meta property="og:image" content={seoImage} />
      <meta property="og:site_name" content="Hamro Digi Cart" />

      {/* Twitter */}
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:url" content={canonical} />
      <meta name="twitter:title" content={seoTitle} />
      <meta name="twitter:description" content={seoDescription} />
      <meta name="twitter:image" content={seoImage} />

      {/* Additional SEO */}
      <meta name="robots" content={robots} />
      <meta name="language" content="English" />
      <meta name="author" content="Hamro Digi Cart" />
      <meta name="geo.region" content="NP" />
      <meta name="geo.placename" content="Nepal" />

      {/* Structured Data */}
      {structuredData && (
        <script type="application/ld+json">
          {JSON.stringify(structuredData)}
        </script>
      )}
    </Helmet>
  );
};

export default SEO;






