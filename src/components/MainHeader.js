import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import Logo from './Logo';
import './MainHeader.css';

const MainHeader = () => {
  const [searchQuery, setSearchQuery] = useState('');
  const navigate = useNavigate();

  const handleSearch = (e) => {
    e.preventDefault();
    const trimmedQuery = searchQuery.trim();
    if (trimmedQuery) {
      navigate(`/search?q=${encodeURIComponent(trimmedQuery)}`);
    } else {
      navigate('/search');
    }
    setSearchQuery('');
  };

  return (
    <header className="main-top-header">
      <div className="main-header-container">
        <Link to="/" className="main-logo-link">
          <Logo size="default" showText={true} variant="header" />
        </Link>
        <nav className="main-header-nav">
          <Link to="/" className="main-header-nav-link">Home</Link>
          <Link to="/about-us" className="main-header-nav-link">About Us</Link>
          <Link to="/contact-us" className="main-header-nav-link">Contact Us</Link>
        </nav>

        <div className="main-header-center">
          <form onSubmit={handleSearch} className="main-search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input
              type="text"
              placeholder="Search Products..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="main-search-input"
            />
            <button type="submit" className="main-search-submit-button" aria-label="Search">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
              </svg>
            </button>
          </form>
        </div>
      </div>
    </header>
  );
};

export default MainHeader;

