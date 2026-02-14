import React, { useState, useEffect } from 'react';
import { useSearchParams, useNavigate } from 'react-router-dom';
import ProductCard from '../components/ProductCard';
import SEO from '../components/SEO';
import { searchProducts, subscribeToProductUpdates } from '../utils/productService';
import './Home.css';

const Search = () => {
  const [searchParams] = useSearchParams();
  const query = searchParams.get('q') || '';
  const [filteredProducts, setFilteredProducts] = useState([]);
  const navigate = useNavigate();

  useEffect(() => {
    // Subscribe to product updates from admin panel to refresh search results
    const unsubscribe = subscribeToProductUpdates(async () => {
      try {
        // Refresh search results when products are updated
        const results = await searchProducts(query);
        setFilteredProducts(results);
      } catch (error) {
        console.error('Error updating products:', error);
      }
    });
    
    return unsubscribe;
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

  return (
    <div className="home-page">
      <SEO 
        title={`Search${query ? `: ${query}` : ''}`}
        description={`Search for digital products${query ? ` matching "${query}"` : ''}. Find reels bundles, templates, and more.`}
        keywords={`search, ${query || ''}, digital products, templates, Nepal`}
      />

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

