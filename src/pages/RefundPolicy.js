import React, { useState, useEffect } from 'react';
import { fetchPageByKey } from '../utils/pageService';
import './PageTemplate.css';

const RefundPolicy = () => {
  const [content, setContent] = useState('');
  const [loading, setLoading] = useState(true);

  const loadContent = async () => {
    try {
      setLoading(true);
      const page = await fetchPageByKey('refund-policy');
      
      if (page && page.content) {
        setContent(page.content);
      } else {
        // Default content if page doesn't exist in database
        setContent(`
          <h1>Refund Policy</h1>
          <p><strong>Last Updated: ${new Date().toLocaleDateString()}</strong></p>
          
          <p>At <strong>Hamro DIGI CART</strong>, we strive to provide the best digital products and customer service. This Refund Policy outlines the circumstances under which refunds may be issued.</p>
          
          <h2>Digital Products Policy</h2>
          <p>Due to the digital nature of our products, all sales are generally final. Once a digital product has been downloaded, it cannot be "returned" in the traditional sense. However, we understand that sometimes issues may arise, and we are committed to resolving them fairly.</p>
          
          <h2>Refund Eligibility</h2>
          <p>We may consider refunds in the following circumstances:</p>
          <ul>
            <li><strong>Technical Issues:</strong> If you are unable to download the product due to technical problems on our end</li>
            <li><strong>Product Not as Described:</strong> If the product significantly differs from its description</li>
            <li><strong>Duplicate Purchase:</strong> If you accidentally purchase the same product twice</li>
            <li><strong>Payment Processing Errors:</strong> If there was an error in the payment processing</li>
            <li><strong>Defective Files:</strong> If the downloaded files are corrupted or unusable</li>
          </ul>
          
          <h2>Non-Refundable Situations</h2>
          <p>Refunds will NOT be issued for:</p>
          <ul>
            <li>Change of mind after purchase</li>
            <li>Inability to use the product due to lack of technical knowledge</li>
            <li>Compatibility issues with your software (please check system requirements before purchase)</li>
            <li>Products that have been successfully downloaded and used</li>
            <li>Discount codes or promotional offers that were not applied</li>
          </ul>
          
          <h2>Refund Process</h2>
          <p>To request a refund, please follow these steps:</p>
          <ol>
            <li><strong>Contact Us:</strong> Reach out to us within 7 days of purchase through our <a href="/contact-us">Contact Us</a> page</li>
            <li><strong>Provide Details:</strong> Include your order number, product name, and reason for refund request</li>
            <li><strong>Review Period:</strong> We will review your request within 48 hours</li>
            <li><strong>Resolution:</strong> If approved, refunds will be processed within 5-10 business days to your original payment method</li>
          </ol>
          
          <h2>Refund Timeline</h2>
          <ul>
            <li><strong>Request Submission:</strong> Within 7 days of purchase</li>
            <li><strong>Review Period:</strong> 48 hours</li>
            <li><strong>Processing Time:</strong> 5-10 business days after approval</li>
          </ul>
          
          <h2>Partial Refunds</h2>
          <p>In some cases, we may offer a partial refund if:</p>
          <ul>
            <li>Only part of a bundle is defective</li>
            <li>You have used some but not all of the product</li>
            <li>There are minor issues that don't warrant a full refund</li>
          </ul>
          
          <h2>Store Credit</h2>
          <p>In certain situations, we may offer store credit instead of a refund. Store credit can be used for future purchases and never expires.</p>
          
          <h2>Chargebacks</h2>
          <p>If you file a chargeback or dispute with your payment provider, your account may be suspended until the matter is resolved. We encourage you to contact us directly first so we can resolve any issues amicably.</p>
          
          <h2>Contact for Refunds</h2>
          <p>For refund requests or questions about this policy, please contact us through our <a href="/contact-us">Contact Us</a> page. Be sure to include:</p>
          <ul>
            <li>Your order number</li>
            <li>Product name</li>
            <li>Reason for refund</li>
            <li>Any relevant screenshots or documentation</li>
          </ul>
          
          <h2>Changes to This Policy</h2>
          <p>We reserve the right to modify this Refund Policy at any time. Changes will be effective immediately upon posting to this page.</p>
          
          <p>Thank you for understanding our refund policy. We are committed to providing excellent products and customer service.</p>
        `);
      }
    } catch (error) {
      console.error('Error loading refund policy:', error);
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

export default RefundPolicy;

