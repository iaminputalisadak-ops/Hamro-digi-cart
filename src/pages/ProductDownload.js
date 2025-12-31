import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import Logo from '../components/Logo';
import { fetchProductById } from '../utils/productService';
import './ProductDownload.css';

const ProductDownload = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [includeUpsell, setIncludeUpsell] = useState(true);

  useEffect(() => {
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

  const handlePayment = () => {
    if (!email || !phone) {
      alert('Please fill in your email and phone number to continue.');
      return;
    }

    // Navigate to payment page with state
    navigate(`/product/${id}/payment`, {
      state: {
        email,
        phone,
        includeUpsell,
        totalAmount: discountedTotal.toFixed(2)
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

  // Ensure price is a number
  const productPrice = parseFloat(product.price) || 0;
  const productDiscount = parseFloat(product.discount) || 0;

  const discountedPrice = productDiscount > 0
    ? Math.round(productPrice * (1 - productDiscount / 100))
    : productPrice;

  const upsellPrice = 99;
  const originalTotal = productPrice + (includeUpsell ? upsellPrice : 0);
  const discountedTotal = discountedPrice + (includeUpsell ? upsellPrice : 0);
  const totalDiscount = includeUpsell
    ? Math.round(((originalTotal - discountedTotal) / originalTotal) * 100)
    : productDiscount;

  // Get product image
  const productImage = product.image || (product.images && product.images[0] ? product.images[0] : null);

  // Extract number from product title
  const match = product.title.match(/(\d+)/);
  const productCount = match ? `${match[1]}+` : '500+';

  return (
    <div className="product-download-page">
      {/* Top Header Bar */}
      <div className="download-top-bar">
        <div className="top-bar-container">
          <div className="top-bar-logo">
            <Logo size="small" showText={true} variant="header" />
          </div>
          <div className="top-bar-text">
            Built with ❤️ on DIGICART
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="download-main-content">
        <div className="download-content-container">
          {/* Left Column - Product Info */}
          <div className="download-left-column">
            {/* Product Image */}
            <div className="product-image-section">
              {productImage ? (
                <img src={productImage} alt={product.title} className="product-main-image" />
              ) : (
                <div className="product-image-placeholder">
                  <span>📦</span>
                </div>
              )}
            </div>

            {/* About Section */}
            <div className="about-product-section">
              <h3 className="section-heading">ABOUT THE PRODUCT</h3>
              <h2 className="product-title-main">{product.title}</h2>
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

            {/* Upsell Offer */}
            <div className="upsell-section">
              <div className={`upsell-box ${includeUpsell ? 'selected' : ''}`}>
                <div className="upsell-left">
                  <div className="upsell-icon">📚</div>
                  <div className="upsell-content">
                    <div className="upsell-title">Add Ultimate Editing Course Rs. 99</div>
                    <div className="upsell-description">One-time offer! Add this pack to skyrocket your growth.</div>
                  </div>
                </div>
                <div
                  className="upsell-checkbox"
                  onClick={() => setIncludeUpsell(!includeUpsell)}
                >
                  {includeUpsell && <span className="checkmark">✓</span>}
                </div>
              </div>
            </div>

            {/* Pricing Summary */}
            <div className="pricing-summary">
              <div className="price-row">
                <span className="price-label">Original Price</span>
                <span className="price-value original">Rs. {originalTotal.toFixed(2)}</span>
              </div>
              <div className="price-row">
                <span className="price-label">Discount</span>
                <span className="price-value discount">{totalDiscount}% OFF</span>
              </div>
              <div className="price-row total-row">
                <span className="price-label">Total Amount</span>
                <span className="price-value total">Rs. {discountedTotal.toFixed(2)}</span>
              </div>
            </div>

            {/* Payment Button */}
            <button className="payment-button" onClick={handlePayment}>
              Make Payment Rs. {discountedTotal.toFixed(2)} →
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductDownload;
