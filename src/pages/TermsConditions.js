import React, { useState, useEffect } from 'react';
import SEO from '../components/SEO';
import { fetchPageByKey } from '../utils/pageService';
import './PageTemplate.css';

const TermsConditions = () => {
  const [content, setContent] = useState('');
  const [loading, setLoading] = useState(true);

  const loadContent = async () => {
    try {
      setLoading(true);
      const page = await fetchPageByKey('terms-conditions');
      
      if (page && page.content) {
        setContent(page.content);
      } else {
        // Default content if page doesn't exist in database
        setContent(`
          <h1>Terms & Conditions</h1>
          <p><strong>Last Updated: ${new Date().toLocaleDateString()}</strong></p>
          
          <p>Welcome to <strong>Hamro DIGI CART</strong>. By accessing and using our website, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to these terms, please do not use our services.</p>
          
          <h2>Acceptance of Terms</h2>
          <p>By accessing and using Hamro DIGI CART, you accept and agree to be bound by the terms and provision of this agreement. These Terms apply to all visitors, users, and others who access or use our service.</p>
          
          <h2>Use License</h2>
          <p>Permission is granted to temporarily download one copy of the materials on Hamro DIGI CART's website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
          <ul>
            <li>Modify or copy the materials</li>
            <li>Use the materials for any commercial purpose or for any public display</li>
            <li>Attempt to reverse engineer any software contained on the website</li>
            <li>Remove any copyright or other proprietary notations from the materials</li>
          </ul>
          
          <h2>Product Usage</h2>
          <p>All digital products purchased from Hamro DIGI CART are subject to the following terms:</p>
          <ul>
            <li><strong>Personal and Commercial Use:</strong> All digital products are licensed for both personal and commercial use</li>
            <li><strong>No Redistribution:</strong> You may not redistribute, resell, or share the original files with others</li>
            <li><strong>No Claim of Ownership:</strong> You may not claim ownership of the original designs or templates</li>
            <li><strong>Modifications Allowed:</strong> You are allowed to modify the products for your own use</li>
            <li><strong>Attribution:</strong> Attribution is appreciated but not required</li>
          </ul>
          
          <h2>Payment Terms</h2>
          <p>All payments are processed securely through our payment gateway partners. By making a purchase, you agree to:</p>
          <ul>
            <li>Provide accurate and complete payment information</li>
            <li>Pay all charges incurred by your account</li>
            <li>Accept that all sales are final unless otherwise stated</li>
          </ul>
          <p>Once payment is confirmed, you will receive instant access to your purchased products via download links.</p>
          
          <h2>Refund Policy</h2>
          <p>Due to the digital nature of our products, all sales are generally final. However, we offer refunds in exceptional circumstances such as:</p>
          <ul>
            <li>Technical issues preventing product download</li>
            <li>Product not as described</li>
            <li>Duplicate purchases (accidental)</li>
          </ul>
          <p>Please see our <a href="/refund-policy">Refund Policy</a> for detailed information.</p>
          
          <h2>Account Responsibility</h2>
          <p>You are responsible for:</p>
          <ul>
            <li>Maintaining the confidentiality of your account and password</li>
            <li>All activities that occur under your account</li>
            <li>Notifying us immediately of any unauthorized use</li>
          </ul>
          
          <h2>Prohibited Uses</h2>
          <p>You may not use our website:</p>
          <ul>
            <li>In any way that violates any applicable law or regulation</li>
            <li>To transmit any malicious code or viruses</li>
            <li>To impersonate or attempt to impersonate the company</li>
            <li>To engage in any automated use of the system</li>
          </ul>
          
          <h2>Intellectual Property</h2>
          <p>The content, features, and functionality of Hamro DIGI CART are owned by us and are protected by international copyright, trademark, and other intellectual property laws.</p>
          
          <h2>Limitation of Liability</h2>
          <p>In no event shall Hamro DIGI CART, nor its directors, employees, partners, or agents, be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of our services.</p>
          
          <h2>Changes to Terms</h2>
          <p>We reserve the right to modify these Terms at any time. We will notify users of any changes by updating the "Last Updated" date. Your continued use of our services after changes constitutes acceptance of the new terms.</p>
          
          <h2>Contact Information</h2>
          <p>If you have any questions about these Terms & Conditions, please contact us through our <a href="/contact-us">Contact Us</a> page.</p>
        `);
      }
    } catch (error) {
      console.error('Error loading terms and conditions:', error);
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
        <SEO
          title="Terms & Conditions"
          description="Read the terms and conditions for using Hamro Digi Cart."
          keywords="terms, conditions, hamro digi cart, digital products"
        />
        <div className="page-container">
          <div className="page-loading">Loading...</div>
        </div>
      </div>
    );
  }

  return (
    <div className="page-template">
      <SEO
        title="Terms & Conditions"
        description="Read the terms and conditions for using Hamro Digi Cart."
        keywords="terms, conditions, hamro digi cart, digital products"
      />
      <div className="page-container">
        <article className="page-content" dangerouslySetInnerHTML={{ __html: content }} />
      </div>
    </div>
  );
};

export default TermsConditions;

