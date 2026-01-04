import React, { useState, useEffect } from 'react';
import { fetchPageByKey } from '../utils/pageService';
import './PageTemplate.css';

const AboutUs = () => {
  const [content, setContent] = useState('');
  const [loading, setLoading] = useState(true);

  const loadContent = async () => {
    try {
      setLoading(true);
      const page = await fetchPageByKey('about-us');
      
      if (page && page.content) {
        setContent(page.content);
      } else {
        // Default content if page doesn't exist in database
        setContent(`
        <h1>About Us</h1>
        <p>Welcome to <strong>Hamro DIGI CART</strong>, your premier destination for digital products in India. We are a leading platform dedicated to providing high-quality digital content that helps creators, businesses, and individuals grow their online presence.</p>
        
        <h2>Our Story</h2>
        <p>Founded in 2020, Hamro DIGI CART started with a simple mission: to make professional digital content accessible to everyone. What began as a small venture has grown into one of India's most trusted digital product marketplaces, serving thousands of satisfied customers across the country.</p>
        
        <h2>Our Mission</h2>
        <p>Our mission is to empower creators and businesses by providing affordable, high-quality digital products that enable them to create stunning content without breaking the bank. We believe that everyone deserves access to professional-grade digital assets, regardless of their budget.</p>
        
        <h2>What We Offer</h2>
        <ul>
          <li><strong>Premium Reels Bundles</strong> - Thousands of ready-to-use Instagram and Facebook reel templates</li>
          <li><strong>WhatsApp Status Templates</strong> - Beautiful, customizable status templates for your WhatsApp</li>
          <li><strong>Digital Planners</strong> - Comprehensive planners for productivity and organization</li>
          <li><strong>Social Media Content Packs</strong> - Complete content packages for all social media platforms</li>
          <li><strong>Video Templates</strong> - Professional video templates for all your marketing needs</li>
          <li><strong>Animation Bundles</strong> - Engaging animations and graphics for your content</li>
        </ul>
        
        <h2>Why Choose Us?</h2>
        <ul>
          <li><strong>No Watermarks</strong> - All our products come without watermarks, giving you complete freedom</li>
          <li><strong>Lifetime Access</strong> - Download your purchased products anytime, anywhere</li>
          <li><strong>Instant Download</strong> - Get immediate access to your products after purchase</li>
          <li><strong>High Quality</strong> - Professional-grade content created by expert designers</li>
          <li><strong>Affordable Prices</strong> - Best value in the market with regular discounts and offers</li>
          <li><strong>24/7 Support</strong> - Our dedicated support team is always ready to help you</li>
        </ul>
        
        <h2>Our Values</h2>
        <p>At Hamro DIGI CART, we are committed to:</p>
        <ul>
          <li>Providing exceptional customer service</li>
          <li>Maintaining the highest quality standards</li>
          <li>Ensuring customer satisfaction</li>
          <li>Supporting the creator community</li>
          <li>Innovating and improving our offerings</li>
        </ul>
        
        <h2>Join Our Community</h2>
        <p>Join thousands of satisfied customers who trust Hamro DIGI CART for their digital content needs. Whether you're a content creator, small business owner, or marketing professional, we have something for everyone.</p>
        
        <p>Thank you for choosing Hamro DIGI CART. We look forward to helping you create amazing content!</p>
      `);
      }
    } catch (error) {
      console.error('Error loading about us page:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadContent();
    
    // Refresh content every 30 seconds to get updates from admin
    const interval = setInterval(loadContent, 30000);
    
    return () => clearInterval(interval);
  }, []);

  if (loading) {
    return (
      <div className="page-template">
        <div className="page-container">
          <div className="page-loading">Loading...</div>
        </div>
      </div>
    );
  }

  return (
    <div className="page-template">
      <div className="page-container">
        <div className="page-content" dangerouslySetInnerHTML={{ __html: content }} />
      </div>
    </div>
  );
};

export default AboutUs;

