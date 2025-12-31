import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useWebsiteSettings } from '../hooks/useWebsiteSettings';
import './ProductCard.css';

const ProductCard = ({ product, onViewDetails }) => {
  const [imageErrors, setImageErrors] = useState({});
  const navigate = useNavigate();
  const { settings } = useWebsiteSettings();

  const handleImageError = (index) => {
    setImageErrors(prev => ({ ...prev, [index]: true }));
  };

  const handleCardClick = () => {
    if (onViewDetails) {
      onViewDetails(product);
    } else {
      navigate(`/product/${product.id}`);
    }
  };

  const handleViewDetailsClick = (e) => {
    e.stopPropagation();
    if (onViewDetails) {
      onViewDetails(product);
    } else {
      navigate(`/product/${product.id}`);
    }
  };

  // Get image - support both old format (images array) and new format (image string)
  const getProductImage = () => {
    if (product.image) return product.image;
    if (product.images && Array.isArray(product.images) && product.images.length > 0) {
      return product.images[0]; // Fallback to first image from old format
    }
    return null;
  };

  const productImage = getProductImage();

  return (
    <div className="product-card" onClick={handleCardClick}>
      <div className="product-image-container">
        <div className="product-image-full">
          {productImage && !imageErrors[0] ? (
            <img 
              src={productImage} 
              alt={product.title} 
              onError={() => handleImageError(0)}
              style={{width: '100%', height: '100%', objectFit: 'cover'}}
            />
          ) : (
            <div style={{width: '100%', height: '100%', background: '#e5e7eb', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '2rem'}}>📷</div>
          )}
        </div>
      </div>
      <div className="product-info">
        <h3 className="product-title">{product.title}</h3>
        {settings?.product_card_promotional_text && (
          <span className="product-promotional-badge">{settings.product_card_promotional_text}</span>
        )}
        <p className="product-description">
          {product.description || (settings?.product_card_default_description 
            ? settings.product_card_default_description.replace('{title}', product.title)
            : '')}
        </p>
        {settings?.product_card_show_price !== '0' && (
          <div className="product-price-section">
            {product.discount && product.discount > 0 && settings?.product_card_show_discount_badge !== '0' ? (
              <>
                <div className="price-with-discount">
                  {settings?.product_card_price_label && (
                    <span className="price-label">{settings.product_card_price_label} </span>
                  )}
                  <span className="product-price-original">₹{product.price}</span>
                  <span className="product-price-discounted">
                    ₹{Math.round(product.price * (1 - product.discount / 100))}
                  </span>
                </div>
                <span className="product-discount">
                  {product.discount}{settings?.product_card_discount_badge_text || '% OFF'}
                </span>
              </>
            ) : (
              <span className="product-price">
                {settings?.product_card_price_label && (
                  <span className="price-label">{settings.product_card_price_label} </span>
                )}
                ₹{product.price}
              </span>
            )}
          </div>
        )}
        <button 
          className="product-btn" 
          onClick={handleViewDetailsClick}
          style={settings?.product_card_button_color ? { backgroundColor: settings.product_card_button_color } : {}}
        >
          {settings?.product_card_button_text || 'Download'}
        </button>
      </div>
    </div>
  );
};

export default ProductCard;

