import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, Link, useLocation } from 'react-router-dom';
import { fetchProductById } from '../utils/productService';
import { stripHTMLSimple } from '../utils/htmlUtils';
import './ProductDownload.css';

const ProductDownload = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const location = useLocation();
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');

  // Get price data from navigation state (passed from ProductDetails)
  const { productPrice: passedPrice, productDiscount: passedDiscount, discountedPrice: passedDiscountedPrice } = location.state || {};

  useEffect(() => {
    // Scroll to top on mount and when product ID changes
    window.scrollTo(0, 0);

    const loadProduct = async () => {
      try {
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
  }, [id]);

  // Calculate prices function - used in both handlePayment and render
  const calculatePrices = () => {
    // Use passed price data if available (for consistency), otherwise use product data from API
    const calculatedProductPrice = passedPrice !== undefined ? passedPrice : (parseFloat(product?.price) || 0);
    const calculatedProductDiscount = passedDiscount !== undefined ? passedDiscount : (parseFloat(product?.discount) || 0);
    const calculatedDiscountedPrice = passedDiscountedPrice !== undefined 
      ? passedDiscountedPrice 
      : (calculatedProductDiscount > 0
          ? Math.round(calculatedProductPrice * (1 - calculatedProductDiscount / 100))
          : calculatedProductPrice);

    // No upsell calculations - prices are product prices only
    return {
      productPrice: calculatedProductPrice,
      productDiscount: calculatedProductDiscount,
      discountedPrice: calculatedDiscountedPrice
    };
  };

  const handlePayment = () => {
    if (!email || !phone) {
      alert('Please fill in your email and phone number to continue.');
      return;
    }

    // Calculate prices to ensure consistency
    const prices = calculatePrices();

    // Navigate to payment page with state - pass price data (product prices only, no upsell)
    navigate(`/product/${id}/payment`, {
      state: {
        email,
        phone,
        productPrice: prices.productPrice,
        productDiscount: prices.productDiscount,
        discountedPrice: prices.discountedPrice
      }
    });
  };

  if (loading) {
    return (
      <div className="download-loading">
        <div className="loading-spinner">Loading...</div>
      </div>
    );
  }

  if (!product) {
    return (
      <div className="download-error">
        <h2>Product Not Found</h2>
        <p>The product you're looking for doesn't exist.</p>
        <button onClick={() => navigate('/')} className="btn-primary">Go to Home</button>
      </div>
    );
  }

  // Calculate prices for display (product prices only, no upsell)
  const { productPrice, productDiscount, discountedPrice } = calculatePrices();

  // Get product image
  const productImage = product.image || (product.images && product.images[0] ? product.images[0] : null);

  // Extract number from product title (strip HTML first)
  const cleanTitle = stripHTMLSimple(product.title);
  const match = cleanTitle.match(/(\d+)/);
  const productCount = match ? `${match[1]}+` : '500+';

  // Handle logo/Home link click - refresh if on homepage, navigate otherwise
  const handleHomeLinkClick = (e) => {
    if (location.pathname === '/') {
      // If already on homepage, refresh the page
      e.preventDefault();
      window.location.reload();
    }
    // Otherwise let React Router handle navigation normally
  };

  return (
    <div className="product-download-page">
      {/* Main Content */}
      <div className="download-main-content">
        <div className="download-content-container">
          {/* Left Column - Product Info */}
          <div className="download-left-column">
            {/* Product Image */}
            <div className="product-image-section">
              {productImage ? (
                <img src={productImage} alt={stripHTMLSimple(product.title)} className="product-main-image" />
              ) : (
                <div className="product-image-placeholder">
                  <span>📦</span>
                </div>
              )}
            </div>

            {/* About Section */}
            <div className="about-product-section">
              <h3 className="section-heading">ABOUT THE PRODUCT</h3>
              <h2 className="product-title-main">{stripHTMLSimple(product.title)}</h2>
            </div>

            {/* What's Inside Section */}
            <div className="whats-inside-section">
              <h3 className="section-heading">WHAT'S INSIDE</h3>
              <ul className="features-list">
                <li>
                  <span className="check-icon">✓</span>
                  {productCount} {product.category || 'TikTok'} Templates
                </li>
                <li>
                  <span className="check-icon">✓</span>
                  High-Quality Video Content
                </li>
                <li>
                  <span className="check-icon">✓</span>
                  Ready-to-Use Designs
                </li>
                <li>
                  <span className="check-icon">✓</span>
                  No Watermark
                </li>
              </ul>
            </div>
          </div>

          {/* Right Column - Contact & Payment */}
          <div className="download-right-column">
            {/* Contact Details */}
            <div className="contact-details-section">
              <h3 className="section-heading">CONTACT DETAILS</h3>
              <div className="form-group">
                <label>Email Address</label>
                <input
                  type="email"
                  placeholder="Enter your email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="form-input"
                />
              </div>
              <div className="form-group">
                <label>Phone Number</label>
                <input
                  type="tel"
                  placeholder="Enter your mobile number"
                  value={phone}
                  onChange={(e) => setPhone(e.target.value)}
                  className="form-input"
                />
              </div>
            </div>

            {/* Trust & Benefits Section */}
            <div className="trust-section">
              <div className="trust-box">
                <div className="trust-icon">✨</div>
                <div className="trust-content">
                  <div className="trust-title">What You'll Get</div>
                  <div className="trust-items">
                    <div className="trust-item">
                      <span className="trust-check">✓</span>
                      <span>Instant Download Access</span>
                    </div>
                    <div className="trust-item">
                      <span className="trust-check">✓</span>
                      <span>Lifetime Access to Files</span>
                    </div>
                    <div className="trust-item">
                      <span className="trust-check">✓</span>
                      <span>No Watermarks Included</span>
                    </div>
                    <div className="trust-item">
                      <span className="trust-check">✓</span>
                      <span>Premium Quality Content</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* Pricing Summary */}
            <div className="pricing-summary">
              <div className="price-row">
                <span className="price-label">Original Price</span>
                <span className="price-value original">Rs. {productPrice}</span>
              </div>
              {productDiscount > 0 && (
                <div className="price-row">
                  <span className="price-label">Discount</span>
                  <span className="price-value discount">{productDiscount}% OFF</span>
                </div>
              )}
              <div className="price-row total-row">
                <span className="price-label">Total Amount</span>
                <span className="price-value total">Rs. {discountedPrice}</span>
              </div>
            </div>

            {/* Payment Button */}
            <button className="payment-button" onClick={handlePayment}>
              Make Payment Rs. {discountedPrice} →
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductDownload;
