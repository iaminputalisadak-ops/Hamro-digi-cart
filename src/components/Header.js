import React, { useState, useEffect, useRef } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import Logo from './Logo';
import SearchSuggestions from './SearchSuggestions';
import './Header.css';

const Header = () => {
  const [searchQuery, setSearchQuery] = useState('');
  const [showSuggestions, setShowSuggestions] = useState(false);
  const searchInputRef = useRef(null);
  const searchContainerRef = useRef(null);
  const secondaryNavRef = useRef(null);
  const mainHeaderRef = useRef(null);
  const navigate = useNavigate();
  const location = useLocation();

  // Static navigation menu items as shown in the image
  const navigationItems = [
    { label: 'Fitness & Health Planner', path: '/fitness-health-planner' },
    { label: 'Follow us', path: '/follow-us' },
    { label: 'Reel Bundles', path: '/reels-bundle' },
    { label: 'Instagram Reels Bundle', path: '/instagram-reels-bundle' },
    { label: 'Combo Reels Bundle', path: '/combo-reels-bundle' },
    { label: 'Whatsapp', path: '/whatsapp' },
    { label: 'Reels Bundle 99', path: '/reels-bundle-99' }
  ];

  // Update body padding so content starts below fixed navbar + fixed header (both constant)
  useEffect(() => {
    const applyOffset = () => {
      const navHeight = secondaryNavRef.current?.offsetHeight || 50;
      const headerHeight = mainHeaderRef.current?.offsetHeight || 0;
      document.body.style.paddingTop = (navHeight + headerHeight) + 'px';
    };

    // Apply twice to avoid any initial layout shift (fonts/padding)
    applyOffset();
    requestAnimationFrame(applyOffset);
    document.body.classList.add('has-secondary-nav');
    
    // Handle resize
    const handleResize = () => applyOffset();
    
    window.addEventListener('resize', handleResize);
    
    return () => {
      document.body.style.paddingTop = '';
      document.body.classList.remove('has-secondary-nav');
      window.removeEventListener('resize', handleResize);
    };
  }, []);

  // Handle search submission
  const handleSearch = (e) => {
    e.preventDefault();
    const trimmedQuery = searchQuery.trim();
    if (trimmedQuery) {
      navigate(`/search?q=${encodeURIComponent(trimmedQuery)}`);
      setShowSuggestions(false);
    } else {
      navigate('/search');
    }
  };

  // Handle suggestion selection
  const handleSuggestionSelect = (suggestionText) => {
    navigate(`/search?q=${encodeURIComponent(suggestionText)}`);
    setSearchQuery('');
    setShowSuggestions(false);
  };

  // Handle home link click - refresh if on homepage, navigate otherwise
  const handleHomeLinkClick = (e) => {
    if (location.pathname === '/') {
      e.preventDefault();
      window.location.reload();
    }
  };

  return (
    <>
      {/* Main Header with Logo, Home, and Search in One Row - Light Green Background */}
      <header className="admin-top-header" ref={mainHeaderRef}>
        <div className="header-container">
          {/* Logo on Left */}
          <Link to="/" className="logo-link" onClick={handleHomeLinkClick}>
            <Logo size="default" showText={true} variant="header" />
          </Link>
          
          {/* Search Bar on Right */}
          <div className="header-center">
            <div ref={searchContainerRef} className="search-container">
              <form onSubmit={handleSearch} className="search-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.35-4.35"></path>
                </svg>
                <input
                  ref={searchInputRef}
                  type="text"
                  placeholder="Search Products..."
                  value={searchQuery}
                  onChange={(e) => {
                    setSearchQuery(e.target.value);
                    setShowSuggestions(true);
                  }}
                  onFocus={() => {
                    if (searchQuery.trim().length >= 2) {
                      setShowSuggestions(true);
                    }
                  }}
                  className="search-input"
                />
                <button type="submit" className="search-submit-button" aria-label="Search">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                  </svg>
                </button>
              </form>
              {showSuggestions && (
                <SearchSuggestions
                  query={searchQuery}
                  onSelect={handleSuggestionSelect}
                  onClose={() => setShowSuggestions(false)}
                  inputRef={searchInputRef}
                />
              )}
            </div>
          </div>
        </div>
      </header>

      {/* Secondary Navigation Bar - Dark Green Background */}
      <div className="secondary-nav-wrapper" ref={secondaryNavRef}>
        <nav className="secondary-nav">
          <div className="secondary-nav-container">
            {navigationItems.map((item, index) => (
              <Link key={index} to={item.path}>
                {item.label}
              </Link>
            ))}
          </div>
        </nav>
      </div>
    </>
  );
};

export default Header;

