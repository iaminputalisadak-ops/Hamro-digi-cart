import React, { useState, useEffect } from 'react';
import { fetchPageByKey } from '../utils/pageService';
import './PageTemplate.css';

const PrivacyPolicy = () => {
  const [content, setContent] = useState('');
  const [loading, setLoading] = useState(true);

  const loadContent = async () => {
    try {
      setLoading(true);
      const page = await fetchPageByKey('privacy-policy');
      
      if (page && page.content) {
        setContent(page.content);
      } else {
        // Default content if page doesn't exist in database
        setContent(`
          <h1>Privacy Policy</h1>
          <p><strong>Last Updated: ${new Date().toLocaleDateString()}</strong></p>
          
          <p>At <strong>Hamro DIGI CART</strong>, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website and use our services.</p>
          
          <h2>Information We Collect</h2>
          <p>We collect information that you provide directly to us, including:</p>
          <ul>
            <li><strong>Account Information:</strong> Name, email address, phone number, and password when you create an account</li>
            <li><strong>Purchase Information:</strong> Payment details, billing address, and transaction history</li>
            <li><strong>Communication Data:</strong> Messages, inquiries, and feedback you send to us</li>
            <li><strong>Usage Data:</strong> Information about how you interact with our website, including pages visited and products viewed</li>
          </ul>
          
          <h2>How We Use Your Information</h2>
          <p>We use the information we collect to:</p>
          <ul>
            <li>Process and fulfill your orders</li>
            <li>Send you order confirmations and updates</li>
            <li>Respond to your inquiries and provide customer support</li>
            <li>Send you promotional materials and newsletters (with your consent)</li>
            <li>Improve our website, products, and services</li>
            <li>Detect and prevent fraud and abuse</li>
            <li>Comply with legal obligations</li>
          </ul>
          
          <h2>Data Security</h2>
          <p>We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the Internet or electronic storage is 100% secure.</p>
          
          <h2>Data Sharing</h2>
          <p>We do not sell your personal information. We may share your information with:</p>
          <ul>
            <li><strong>Service Providers:</strong> Third-party companies that help us operate our business (payment processors, hosting providers, etc.)</li>
            <li><strong>Legal Requirements:</strong> When required by law or to protect our rights</li>
            <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
          </ul>
          
          <h2>Your Rights</h2>
          <p>You have the right to:</p>
          <ul>
            <li>Access and receive a copy of your personal data</li>
            <li>Correct inaccurate or incomplete information</li>
            <li>Request deletion of your personal data</li>
            <li>Object to processing of your personal data</li>
            <li>Withdraw consent at any time</li>
          </ul>
          
          <h2>Cookies</h2>
          <p>We use cookies and similar tracking technologies to track activity on our website and store certain information. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.</p>
          
          <h2>Third-Party Links</h2>
          <p>Our website may contain links to third-party websites. We are not responsible for the privacy practices of these external sites. We encourage you to review their privacy policies.</p>
          
          <h2>Children's Privacy</h2>
          <p>Our services are not intended for children under 18 years of age. We do not knowingly collect personal information from children.</p>
          
          <h2>Changes to This Policy</h2>
          <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.</p>
          
          <h2>Contact Us</h2>
          <p>If you have any questions about this Privacy Policy, please contact us through our <a href="/contact-us">Contact Us</a> page.</p>
        `);
      }
    } catch (error) {
      console.error('Error loading privacy policy:', error);
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

export default PrivacyPolicy;

