import React, { useState, useEffect } from 'react';
import { useSearchParams, useNavigate, Link } from 'react-router-dom';
import ProductCard from '../components/ProductCard';
import Logo from '../components/Logo';
import { fetchAllProducts, searchProducts, subscribeToProductUpdates } from '../utils/productService';
import './Home.css';

const Search = () => {
  const [searchParams] = useSearchParams();
  const query = searchParams.get('q') || '';
  const [products, setProducts] = useState([]);
  const [filteredProducts, setFilteredProducts] = useState([]);
  const [searchQuery, setSearchQuery] = useState(query);
  const navigate = useNavigate();

  useEffect(() => {
    // Fetch all products from API
    const loadProducts = async () => {
      try {
        const allProducts = await fetchAllProducts();
        setProducts(allProducts);
      } catch (error) {
        console.error('Error loading products:', error);
      }
    };

    // Initial load
    loadProducts();
    
    // Subscribe to product updates from admin panel
    const unsubscribe = subscribeToProductUpdates(async () => {
      try {
        const allProducts = await fetchAllProducts();
        setProducts(allProducts);
      } catch (error) {
        console.error('Error updating products:', error);
      }
    });
    
    return unsubscribe;
  }, []);

  useEffect(() => {
    // Update search query when URL changes
    setSearchQuery(query);
  }, [query]);

  useEffect(() => {
    // Search products using the service function
    const performSearch = async () => {
      try {
        const results = await searchProducts(query);
        setFilteredProducts(results);
      } catch (error) {
        console.error('Error searching products:', error);
        setFilteredProducts([]);
      }
    };
    
    performSearch();
  }, [query]);

  const handleSearch = (e) => {
    e.preventDefault();
    const trimmedQuery = searchQuery.trim();
    if (trimmedQuery) {
      navigate(`/search?q=${encodeURIComponent(trimmedQuery)}`);
    } else {
      navigate('/search');
    }
  };

  const handleDownload = (product) => {
    navigate(`/product/${product.id}`);
  };

  return (
    <div className="home-page">
      {/* Top Header - Same as Home Page */}
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

      <section className="products-section">
        <div className="products-header">
          <h2>
            {query && query.trim() 
              ? `Search Results for "${query.trim()}"` 
              : 'All Products'}
          </h2>
          {filteredProducts.length > 0 && (
            <span className="view-all">{filteredProducts.length} product(s) found</span>
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
              {query && query.trim() ? (
                <>
                  <p>No products found matching "{query.trim()}"</p>
                  <button onClick={() => navigate('/')} className="btn-primary" style={{marginTop: '1rem'}}>
                    Go to Home
                  </button>
                </>
              ) : (
                <p>No products available</p>
              )}
            </div>
          )}
        </div>
      </section>
    </div>
  );
};

export default Search;

