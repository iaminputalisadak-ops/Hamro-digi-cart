import React, { useState, useEffect, useRef, memo } from 'react';
import { useNavigate } from 'react-router-dom';
import { useWebsiteSettings } from '../hooks/useWebsiteSettings';
import { stripHTMLSimple } from '../utils/htmlUtils';
import LazyImage from './LazyImage';
import './ProductCard.css';

const ProductCard = memo(({ product, onViewDetails, index = 0, onCardClick, onDownloadClick }) => {
  const [imageErrors, setImageErrors] = useState({});
  const [isVisible, setIsVisible] = useState(false);
  const cardRef = useRef(null);
  const navigate = useNavigate();
  const { settings } = useWebsiteSettings();

  // Intersection Observer for scroll animations
  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            setIsVisible(true);
            // Add visible class for CSS animations
            if (cardRef.current) {
              cardRef.current.classList.add('visible');
            }
            // Unobserve after animation
            observer.unobserve(entry.target);
          }
        });
      },
      {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      }
    );

    if (cardRef.current) {
      observer.observe(cardRef.current);
    }

    return () => {
      if (cardRef.current) {
        observer.unobserve(cardRef.current);
      }
    };
  }, []);

  const handleImageError = (index) => {
    setImageErrors(prev => ({ ...prev, [index]: true }));
  };

  const handleCardClick = () => {
    if (onCardClick) {
      onCardClick(product);
      return;
    }
    // Card tap/click: open Product Details page
    navigate(`/product/${product.id}`);
  };

  const handleViewDetailsClick = (e) => {
    e.stopPropagation();
    // Download CTA: open Contact Details (download) page
    if (onDownloadClick) {
      onDownloadClick(product);
      return;
    }
    // Back-compat: if callers still pass onViewDetails, treat it as download click
    if (onViewDetails) {
      onViewDetails(product);
      return;
    }
    navigate(`/product/${product.id}/download`);
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
    <div 
      ref={cardRef}
      className={`product-card ${isVisible ? 'visible' : ''}`}
      onClick={handleCardClick}
      style={{
        animationDelay: `${index * 0.1}s`
      }}
    >
      <div className="product-image-container">
        <div className="product-image-full">
          {productImage && !imageErrors[0] ? (
            <LazyImage
              src={productImage}
              alt={stripHTMLSimple(product.title)}
              onError={() => handleImageError(0)}
              style={{width: '100%', height: '100%', objectFit: 'cover'}}
              className="product-image-full-img"
            />
          ) : (
            <div style={{width: '100%', height: '100%', background: '#e5e7eb', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '2rem'}}>📷</div>
          )}
        </div>
      </div>
      <div className="product-info">
        <h3 className="product-title">{stripHTMLSimple(product.title)}</h3>
        {settings?.product_card_promotional_text && (
          <span className="product-promotional-badge">{settings.product_card_promotional_text}</span>
        )}
        <p className="product-description">
          {stripHTMLSimple(product.description || (settings?.product_card_default_description 
            ? settings.product_card_default_description.replace('{title}', stripHTMLSimple(product.title))
            : ''))}
        </p>
        {settings?.product_card_show_price !== '0' && (
          <div className="product-price-section">
            {product.discount && product.discount > 0 && settings?.product_card_show_discount_badge !== '0' ? (
              <>
                <div className="price-with-discount">
                  {settings?.product_card_price_label && (
                    <span className="price-label">{settings.product_card_price_label} </span>
                  )}
                  <span className="product-price-original">रु{product.price}</span>
                  <span className="product-price-discounted">
                    रु{Math.round(product.price * (1 - product.discount / 100))}
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
                रु{product.price}
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
});

ProductCard.displayName = 'ProductCard';

export default ProductCard;

