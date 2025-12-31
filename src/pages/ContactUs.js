import React, { useState, useEffect } from 'react';
import './PageTemplate.css';

const ContactUs = () => {
  const [content, setContent] = useState('');
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    subject: '',
    message: ''
  });

  const loadContent = () => {
    const savedContent = localStorage.getItem('page_contact-us');
    if (savedContent) {
      setContent(savedContent);
    } else {
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
        
        <p>Fill out the form below and we'll get back to you as soon as possible!</p>
      `);
    }
  };

  useEffect(() => {
    loadContent();

    // Listen for page content updates
    const handlePageContentUpdate = (event) => {
      if (event.detail && event.detail.pageKey === 'contact-us') {
        loadContent();
      }
    };

    window.addEventListener('pageContentUpdated', handlePageContentUpdate);

    // Also listen for storage changes
    const handleStorageChange = (e) => {
      if (e.key === 'page_contact-us') {
        loadContent();
      }
    };

    window.addEventListener('storage', handleStorageChange);

    return () => {
      window.removeEventListener('pageContentUpdated', handlePageContentUpdate);
      window.removeEventListener('storage', handleStorageChange);
    };
  }, []);

  const handleSubmit = (e) => {
    e.preventDefault();
    alert('Thank you for contacting us! We will get back to you soon.\n\n(This is a demo - in a real application, this would send an email)');
    setFormData({ name: '', email: '', subject: '', message: '' });
  };

  return (
    <div className="page-template">
      <div className="page-container">
        <div className="page-content" dangerouslySetInnerHTML={{ __html: content }} />
        <form className="contact-form" onSubmit={handleSubmit}>
          <div className="form-group">
            <label htmlFor="name">Name *</label>
            <input
              type="text"
              id="name"
              value={formData.name}
              onChange={(e) => setFormData({ ...formData, name: e.target.value })}
              required
            />
          </div>
          <div className="form-group">
            <label htmlFor="email">Email *</label>
            <input
              type="email"
              id="email"
              value={formData.email}
              onChange={(e) => setFormData({ ...formData, email: e.target.value })}
              required
            />
          </div>
          <div className="form-group">
            <label htmlFor="subject">Subject *</label>
            <input
              type="text"
              id="subject"
              value={formData.subject}
              onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
              required
            />
          </div>
          <div className="form-group">
            <label htmlFor="message">Message *</label>
            <textarea
              id="message"
              rows="6"
              value={formData.message}
              onChange={(e) => setFormData({ ...formData, message: e.target.value })}
              required
            />
          </div>
          <button type="submit" className="btn-submit">Send Message</button>
        </form>
      </div>
    </div>
  );
};

export default ContactUs;

