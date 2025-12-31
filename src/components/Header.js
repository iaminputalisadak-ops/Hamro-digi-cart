import React from 'react';
import { Link } from 'react-router-dom';
import './Header.css';

const Header = () => {
  return (
    <nav className="secondary-nav">
      <div className="secondary-nav-container">
        <Link to="/whatsapp">WhatsApp</Link>
        <Link to="/reels-bundle">Reels Bundle</Link>
        <Link to="/combo-reels-bundle">Combo Reels bundle</Link>
        <Link to="/instagram-reels-bundle">Instagram Reels Bundle</Link>
        <Link to="/reels-bundle-99">Reels bundle ₹99</Link>
        <Link to="/reels-bundle-149">Reels bundle ₹149</Link>
        <Link to="/reels-bundle-199">Reels bundle ₹199</Link>
        <Link to="/reels-bundle">Reels bundle</Link>
        <div className="dropdown">
          <Link to="/follow-us">Follow Us</Link>
          <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
            <path d="M6 9L1 4h10z" />
          </svg>
        </div>
      </div>
    </nav>
  );
};

export default Header;

