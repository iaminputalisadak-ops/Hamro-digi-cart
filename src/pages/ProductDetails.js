import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import ProductCard from '../components/ProductCard';
import Logo from '../components/Logo';
import { fetchAllProducts, fetchProductById, subscribeToProductUpdates } from '../utils/productService';
import './ProductDetails.css';

const ProductDetails = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [product, setProduct] = useState(null);
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Fetch product from API
    const loadProduct = async () => {
      try {
        const allProducts = await fetchAllProducts();
        setProducts(allProducts);

        const foundProduct = await fetchProductById(id);
        if (foundProduct) {
          setProduct(foundProduct);
        }
      } catch (error) {
        console.error('Error loading product:', error);
      } finally {
        setLoading(false);
      }
    };

    loadProduct();

    // Subscribe to product updates
    const unsubscribe = subscribeToProductUpdates(async () => {
      try {
        const allProducts = await fetchAllProducts();
        setProducts(allProducts);
        const foundProduct = await fetchProductById(id);
        if (foundProduct) {
          setProduct(foundProduct);
        }
      } catch (error) {
        console.error('Error updating product:', error);
      }
    });

    return unsubscribe;
  }, [id]);

  const handleDownload = () => {
    navigate(`/product/${id}/download`);
  };

  if (loading) {
    return (
      <div className="product-details-loading">
        <div className="loading-spinner">Loading...</div>
      </div>
    );
  }

  if (!product) {
    return (
      <div className="product-details-error">
        <h2>Product Not Found</h2>
        <p>The product you're looking for doesn't exist.</p>
        <button onClick={() => navigate('/')} className="btn-primary">Go to Home</button>
      </div>
    );
  }

  const discountedPrice = product.discount
    ? Math.round(product.price * (1 - product.discount / 100))
    : product.price;

  // Get related products (exclude current product)
  const relatedProducts = products
    .filter(p => p.id !== product.id)
    .slice(0, 5);

  // Generate tags from product title and category
  const tags = [
    'Skilcart',
    product.category || 'Reels Bundle',
    'Reels Kit',
    'Instagram Reels Bundle',
    'Viral Reels Bundle',
    'Animation reels bundle',
    'AI reels bundle',
    'Free download'
  ].filter(Boolean);

  return (
    <div className="product-details-page">
      {/* Top Navbar */}
      <div className="details-navbar">
        <div className="details-navbar-container">
          <Link to="/" className="details-navbar-logo">
            <Logo size="default" showText={true} variant="header" />
          </Link>
          <nav className="details-navbar-nav">
            <Link to="/" className="details-navbar-link">Home</Link>
          </nav>
        </div>
      </div>

      <div className="product-details-container">
        {/* Breadcrumbs */}
        <div className="breadcrumbs">
          <span>{product.title}</span>
        </div>

        {/* Main Content */}
        <div className="product-details-layout">
          {/* Left Column - Main Content */}
          <div className="product-main-content">
            {/* Product Title */}
            <h1 className="product-main-title">{product.title}</h1>

            {/* Category Badge */}
            <div className="product-category-badge">
              <span className="category-tag">{product.category || 'Skilcart'}</span>
            </div>

            {/* Product Image - Full Image Display */}
            <div className="product-image-display">
              <div className="product-title-overlay">
                <h2>{product.title}</h2>
              </div>
              <div className="product-image-container-full">
                {(() => {
                  // Support both old format (images array) and new format (image string)
                  const productImage = product.image || (product.images && product.images[0] ? product.images[0] : null);
                  return productImage ? (
                    <img
                      src={productImage}
                      alt={product.title}
                      style={{ width: '100%', height: 'auto', maxHeight: '600px', objectFit: 'contain' }}
                      onError={(e) => {
                        e.target.style.display = 'none';
                        if (e.target.nextElementSibling) {
                          e.target.nextElementSibling.style.display = 'flex';
                        }
                      }}
                    />
                  ) : (
                    <div className="product-image-placeholder" style={{ display: 'flex', width: '100%', minHeight: '400px', alignItems: 'center', justifyContent: 'center', background: '#f3f4f6', fontSize: '3rem' }}>
                      <span>📷</span>
                    </div>
                  );
                })()}
              </div>
            </div>

            {/* Features */}
            <div className="product-features-grid">
              <div className="feature-box">
                <span className="feature-icon">✓</span>
                <span>No Logo</span>
              </div>
              <div className="feature-box">
                <span className="feature-icon">✓</span>
                <span>Lifetime Access</span>
              </div>
              <div className="feature-box">
                <span className="feature-icon">📁</span>
                <span>Google Drive Link</span>
              </div>
              <div className="feature-box">
                <span className="feature-icon">📥</span>
                <span>Easy To Download</span>
              </div>
              <div className="feature-box">
                <span className="feature-icon">✓</span>
                <span>No Watermark</span>
              </div>
              <div className="feature-box">
                <span className="feature-icon">⚡</span>
                <span>Instant Download</span>
              </div>
            </div>

            {/* Pricing and Download Button */}
            <div className="product-pricing-section">
              <div className="price-display">
                {product.discount && product.discount > 0 ? (
                  <div className="price-with-discount-details">
                    <div className="price-row">
                      <span className="price-amount-original">₹{product.price}</span>
                      <span className="price-amount">₹{discountedPrice}</span>
                    </div>
                    <span className="discount-badge-large">{product.discount}% OFF</span>
                  </div>
                ) : (
                  <span className="price-amount">₹{product.price}</span>
                )}
              </div>
              <button onClick={handleDownload} className="btn-download">
                <span className="download-icon">🛒</span>
                <span>Download</span>
                <span className="download-badge">✨</span>
              </button>
            </div>

            {/* Product Description */}
            <div className="product-description-section">
              <h3>{product.title} - Fun, Viral & Engaging!</h3>
              <p>
                {product.description || `Get access to ${product.title}. Perfect for social media creators looking to create engaging, viral content. This bundle includes high-quality animations and templates that are ready to use. No watermarks, no logos - just pure creative content to help you grow your online presence.`}
              </p>
            </div>
          </div>

          {/* Right Column - Sidebar */}
          <div className="product-sidebar">
            {/* Pricing and Download in Sidebar */}
            <div className="sidebar-pricing-box">
              <div className="sidebar-price">
                {product.discount && product.discount > 0 ? (
                  <div className="price-with-discount-details">
                    <div className="price-row">
                      <span className="price-amount-original">₹{product.price}</span>
                      <span className="price-amount">₹{discountedPrice}</span>
                    </div>
                    <span className="discount-badge-large">{product.discount}% OFF</span>
                  </div>
                ) : (
                  <span className="price-amount">₹{product.price}</span>
                )}
              </div>
              <button onClick={handleDownload} className="btn-download-sidebar">
                <span className="download-icon">🛒</span>
                <span>Download</span>
                <span className="download-badge">✨</span>
              </button>
            </div>

            {/* Related Bundles */}
            {relatedProducts.length > 0 && (
              <div className="related-bundles">
                <h3>Get Epic Viral Instagram Reels Bundle For Better Video Content</h3>
                <div className="related-products-list">
                  {relatedProducts.map((relatedProduct) => (
                    <Link
                      key={relatedProduct.id}
                      to={`/product/${relatedProduct.id}`}
                      className="related-product-item"
                    >
                      {relatedProduct.title}
                    </Link>
                  ))}
                </div>
              </div>
            )}

            {/* Advertisement */}
            <div className="advertisement-box">
              <div className="ad-content">
                <h4>Amazon Month of the SALE</h4>
                <p>Special offers and discounts</p>
              </div>
            </div>

            {/* Tags */}
            <div className="product-tags">
              <h3>Tags</h3>
              <div className="tags-container">
                {tags.map((tag, index) => (
                  <span key={index} className="tag-item">{tag}</span>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductDetails;
