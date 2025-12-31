import React, { useState, useEffect } from 'react';
import { useLocation, useNavigate, Link } from 'react-router-dom';
import ProductCard from '../components/ProductCard';
import banner1 from '../assets/banner1.png';
import banner2 from '../assets/banner2.png';
import Logo from '../components/Logo';
import { fetchAllProducts, fetchAllCategories, fetchProductsByCategory, subscribeToProductUpdates } from '../utils/productService';
import { useWebsiteSettings } from '../hooks/useWebsiteSettings';
import './Home.css';

const Home = () => {
  const [products, setProducts] = useState([]);
  const [filteredProducts, setFilteredProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [showPopup, setShowPopup] = useState(false);
  const location = useLocation();
  const navigate = useNavigate();
  const { settings } = useWebsiteSettings();

  // Function to load products and categories from API
  const loadProductsAndCategories = async () => {
    try {
      // Fetch categories from API
      const loadedCategories = await fetchAllCategories();
      setCategories(loadedCategories || []);

      // Fetch products from API
      const loadedProducts = await fetchAllProducts();
      setProducts(loadedProducts || []);
    } catch (error) {
      console.error('Error loading products and categories:', error);
      // Set empty arrays on error to prevent undefined issues
      setCategories([]);
      setProducts([]);
    }
  };

  useEffect(() => {
    // Initial load
    loadProductsAndCategories();

    // Subscribe to product updates from admin panel (poll every 15 seconds for faster updates)
    const unsubscribe = subscribeToProductUpdates(async () => {
      // Reload products and categories when admin makes changes
      try {
        const updatedProducts = await fetchAllProducts();
        const updatedCategories = await fetchAllCategories();
        setProducts(updatedProducts || []);
        setCategories(updatedCategories || []);
      } catch (error) {
        console.error('Error updating products:', error);
      }
    });

    // Cleanup subscription on unmount
    return unsubscribe;
  }, []);

  // Show popup on page load if enabled (every time page loads/refreshes)
  useEffect(() => {
    if (settings && settings.popup_enabled === '1' && location.pathname === '/') {
      // Check if popup has content to show
      if (settings.popup_title || settings.popup_content || settings.popup_image) {
        setShowPopup(true);
      }
    } else {
      setShowPopup(false);
    }
  }, [settings, location.pathname]);

  useEffect(() => {
    // Filter products based on route
    let filtered = [...products];
    const path = location.pathname;

    if (path === '/whatsapp' || path === '/whatsapp-templates') {
      filtered = products.filter(p =>
        p.category === 'WhatsApp Templates' ||
        p.title.toLowerCase().includes('whatsapp')
      );
    } else if (path === '/reels-bundle' || path === '/combo-reels-bundle' || path === '/instagram-reels-bundle' || path === '/tiktok-reel-bundle') {
      filtered = products.filter(p =>
        p.category === 'Reels Bundle' ||
        p.title.toLowerCase().includes('reels') ||
        p.title.toLowerCase().includes('tiktok')
      );
    } else if (path === '/reels-bundle-99') {
      filtered = products.filter(p => p.price === 99);
    } else if (path === '/reels-bundle-149') {
      filtered = products.filter(p => p.price === 149);
    } else if (path === '/reels-bundle-199') {
      filtered = products.filter(p => p.price === 199);
    } else if (path === '/follow-us') {
      // Show all products for follow-us page
      filtered = products;
    } else if (path === '/digital-planner' || path === '/digital-planner-2025') {
      filtered = products.filter(p =>
        p.category === 'Digital Planner' ||
        p.title.toLowerCase().includes('planner') ||
        p.title.toLowerCase().includes('digital planner')
      );
    } else if (path === '/social-media-pack') {
      filtered = products.filter(p =>
        p.category === 'Social Media Pack' ||
        p.title.toLowerCase().includes('social media') ||
        p.title.toLowerCase().includes('media pack')
      );
    } else if (path === '/video-templates') {
      filtered = products.filter(p =>
        p.category === 'Video Templates' ||
        p.title.toLowerCase().includes('video') ||
        p.title.toLowerCase().includes('template')
      );
    } else {
      // Check if path matches any category slug
      const category = categories.find(cat => `/${cat.slug}` === path);
      if (category) {
        filtered = products.filter(p => p.category === category.name);
      }
    }

    setFilteredProducts(filtered);
  }, [products, location.pathname, categories]);

  const handleDownload = (product) => {
    navigate(`/product/${product.id}`);
  };

  const handleViewAll = (categorySlug) => {
    navigate(`/${categorySlug}`);
  };

  // Group products by category - show all products
  const getProductsByCategory = (categoryName) => {
    return products.filter(p => {
      // Match category exactly, with trimmed whitespace
      const productCategory = p.category ? p.category.trim() : '';
      const matchCategory = categoryName ? categoryName.trim() : '';
      return productCategory === matchCategory;
    });
  };

  // Get limited products for homepage display (first 6 products per category)
  const getLimitedProductsByCategory = (categoryName, limit = 6) => {
    const allProducts = getProductsByCategory(categoryName);
    return allProducts.slice(0, limit);
  };

  // Handle search
  const handleSearch = (e) => {
    e.preventDefault();
    const trimmedQuery = searchQuery.trim();
    if (trimmedQuery) {
      navigate(`/search?q=${encodeURIComponent(trimmedQuery)}`);
    } else {
      // If search is empty, show all products
      navigate('/search');
    }
    setSearchQuery('');
  };

  // Handle popup close
  const handleClosePopup = () => {
    setShowPopup(false);
  };

  return (
    <div className="home-page">
      {/* Popup Notification */}
      {showPopup && settings?.popup_enabled === '1' && (settings?.popup_title || settings?.popup_content || settings?.popup_image) && (
        <div className="notification-popup-overlay" onClick={handleClosePopup}>
          <div className="notification-popup" onClick={(e) => e.stopPropagation()}>
            <button className="notification-popup-close" onClick={handleClosePopup}>×</button>
            {settings?.popup_image && (
              <div className="notification-popup-image">
                <img src={settings.popup_image} alt="Notification" />
              </div>
            )}
            {settings?.popup_title && (
              <h2 className="notification-popup-title">{settings.popup_title}</h2>
            )}
            {settings?.popup_content && (
              <div 
                className="notification-popup-content" 
                dangerouslySetInnerHTML={{ __html: settings.popup_content.replace(/\n/g, '<br />').replace(/\r/g, '') }}
              />
            )}
          </div>
        </div>
      )}

      {/* Top Header - Same as Admin Dashboard */}
      <header className="admin-top-header">
        <div className="header-container">
          <Link to="/" className="logo-link">
            <Logo size="default" showText={true} variant="header" />
          </Link>
          <nav className="header-nav">
            <Link to="/" className="header-nav-link">Home</Link>
          </nav>

          <div className="header-center">
            <form onSubmit={handleSearch} className="search-box">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
              </svg>
              <input
                type="text"
                placeholder="Search Products..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="search-input"
              />
              <button type="submit" className="search-submit-button" aria-label="Search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.35-4.35"></path>
                </svg>
              </button>
            </form>
          </div>
        </div>
      </header>

      <section className="promotional-banners">
        <div className="banner-container">
          <div className="banner">
            <div className="banner-content">
              <h2>{settings?.banner1_title || 'WE ARE Creators DIGITAL PRODUCT'}</h2>
              <p>{settings?.banner1_subtitle || 'Sell Digital Products For Free create Store'}</p>
            </div>
            <div className="banner-image">
              <img 
                src={settings?.banner1_image || banner1} 
                alt={settings?.banner1_title || 'Digital Growth Solutions'} 
                className="banner-showcase-img" 
              />
            </div>
          </div>
          <div className="banner">
            <div className="banner-content">
              <h2>{settings?.banner2_title || 'WE ARE Creators DIGITAL PRODUCT'}</h2>
              <p>{settings?.banner2_subtitle || 'Digital Products Selling Website'}</p>
            </div>
            <div className="banner-image">
              <img 
                src={settings?.banner2_image || banner2} 
                alt={settings?.banner2_title || 'Digital Product Packs'} 
                className="banner-showcase-img" 
              />
            </div>
          </div>
        </div>
      </section>

      {/* Display All Categories in Body Section */}
      {
        location.pathname === '/' && (
          <div className="home-body-section">
            {categories.length > 0 ? (
              categories.map((category) => {
                const categoryProducts = getProductsByCategory(category.name);
                const limitedProducts = getLimitedProductsByCategory(category.name, 6);

                return (
                  <section key={category.id} className="category-section">
                    <div className="category-section-container">
                      <div className="category-header">
                        <h2>{category.name}</h2>
                      </div>
                      {categoryProducts.length > 0 ? (
                        <>
                          <div className="products-grid">
                            {limitedProducts.map((product) => (
                              <ProductCard
                                key={product.id}
                                product={product}
                                onViewDetails={handleDownload}
                              />
                            ))}
                          </div>
                          {categoryProducts.length > 6 && (
                            <div className="view-all-button-container">
                              <button
                                className="view-all-button"
                                onClick={() => handleViewAll(category.slug)}
                              >
                                {settings?.product_card_see_all_text || 'See All Products'}
                              </button>
                            </div>
                          )}
                        </>
                      ) : (
                        <div className="no-products-in-category">
                          <p>{settings?.product_card_no_products_message || 'No products available in this category yet.'}</p>
                        </div>
                      )}
                    </div>
                  </section>
                );
              })
            ) : (
              <section className="category-section">
                <div className="category-section-container">
                  <div className="no-categories-message">
                    <p>No categories available. Please add categories from the admin dashboard.</p>
                  </div>
                </div>
              </section>
            )}
          </div>
        )
      }

      {/* Display filtered products for category pages */}
      {
        location.pathname !== '/' && (() => {
          const getCategoryTitle = () => {
            if (location.pathname === '/whatsapp' || location.pathname === '/whatsapp-templates') return 'WhatsApp Templates';
            if (location.pathname === '/reels-bundle') return 'Reels Bundle';
            if (location.pathname === '/combo-reels-bundle') return 'Combo Reels Bundle';
            if (location.pathname === '/instagram-reels-bundle') return 'Instagram Reels Bundle';
            if (location.pathname === '/tiktok-reel-bundle') return 'TikTok Reel Bundle';
            if (location.pathname === '/reels-bundle-99') return 'Reels Bundle ₹99';
            if (location.pathname === '/reels-bundle-149') return 'Reels Bundle ₹149';
            if (location.pathname === '/reels-bundle-199') return 'Reels Bundle ₹199';
            if (location.pathname === '/follow-us') return 'All Products';
            if (location.pathname === '/digital-planner') return 'Digital Planner';
            if (location.pathname === '/digital-planner-2025') return 'Digital Planner 2025';
            if (location.pathname === '/social-media-pack') return 'Social Media Pack';
            if (location.pathname === '/video-templates') return 'Video Templates';
            const category = categories.find(cat => `/${cat.slug}` === location.pathname);
            return category ? category.name : 'Products';
          };

          return (
            <section className="products-section">
              <div className="products-header">
                <h2>{getCategoryTitle()}</h2>
                {filteredProducts.length > 0 && (
                  <span className="products-count">({filteredProducts.length} products)</span>
                )}
              </div>
              <div className="products-grid">
                {filteredProducts.length > 0 ? (
                  filteredProducts.map((product) => (
                    <ProductCard
                      key={product.id}
                      product={product}
                      onViewDetails={handleDownload}
                    />
                  ))
                ) : (
                  <div className="no-products">
                    <p>{settings?.product_card_no_products_message || 'No products found in this category.'}</p>
                  </div>
                )}
              </div>
            </section>
          );
        })()
      }
    </div >
  );
};

export default Home;

