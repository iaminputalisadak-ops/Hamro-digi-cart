import React, { useState, useEffect } from 'react';
import { fetchPageByKey } from '../utils/pageService';
import './PageTemplate.css';

const ContactUs = () => {
  const [content, setContent] = useState('');
  const [loading, setLoading] = useState(true);

  const loadContent = async () => {
    try {
      setLoading(true);
      const page = await fetchPageByKey('contact-us');
      
      if (page && page.content) {
        setContent(page.content);
      } else {
        // Default content if page doesn't exist in database
        setContent(`
        <h1>Contact Us</h1>
        <p>Have a question, need help, or want to provide feedback? We're here to assist you! Our dedicated support team is ready to help with any inquiries you may have.</p>
        
        <h2>Get in Touch</h2>
        <p>We value your feedback and are committed to providing excellent customer service. Whether you have a question about our products, need technical support, or want to share your experience, we'd love to hear from you.</p>
        
        <h2>Contact Information</h2>
        <ul>
          <li><strong>Email:</strong> support@hamrodigicart.com</li>
          <li><strong>Business Hours:</strong> Monday - Saturday, 9:00 AM - 6:00 PM IST</li>
          <li><strong>Response Time:</strong> We typically respond within 24-48 hours</li>
        </ul>
        
        <h2>What Can We Help You With?</h2>
        <ul>
          <li>Product inquiries and recommendations</li>
          <li>Technical support and troubleshooting</li>
          <li>Order status and delivery questions</li>
          <li>Refund and return requests</li>
          <li>Partnership and collaboration opportunities</li>
          <li>General feedback and suggestions</li>
        </ul>
        
        <h2>Before Contacting Us</h2>
        <p>To help us assist you better, please check:</p>
        <ul>
          <li>Our <a href="/about-us">About Us</a> page for general information</li>
          <li>Our <a href="/refund-policy">Refund Policy</a> for refund-related questions</li>
          <li>Our <a href="/terms-conditions">Terms & Conditions</a> for usage policies</li>
          <li>Product descriptions and system requirements before purchase</li>
        </ul>
      `);
      }
    } catch (error) {
      console.error('Error loading contact us page:', error);
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

export default ContactUs;

