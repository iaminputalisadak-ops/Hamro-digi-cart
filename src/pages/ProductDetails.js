import React, { useMemo, useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import SEO from '../components/SEO';
import { fetchAllProducts, fetchProductById, subscribeToProductUpdates } from '../utils/productService';
import { useWebsiteSettings } from '../hooks/useWebsiteSettings';
import DOMPurify from 'dompurify';
import { stripHTMLSimple } from '../utils/htmlUtils';
import './ProductDetails.css';

const ProductDetails = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [product, setProduct] = useState(null);
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const { settings } = useWebsiteSettings();

  const descriptionForDisplay = useMemo(() => {
    const raw = product?.description;
    if (!raw || typeof raw !== 'string') {
      return { kind: 'fallback', html: '', text: '' };
    }

    // CKEditor stores HTML. Some older/seeded items may be plain text.
    const looksLikeHtml = /<\/?[a-z][\s\S]*>/i.test(raw);
    if (looksLikeHtml) {
      const html = DOMPurify.sanitize(raw, {
        USE_PROFILES: { html: true },
        FORBID_TAGS: ['script', 'style', 'iframe', 'object', 'embed'],
        FORBID_ATTR: ['style']
      });
      return { kind: 'html', html, text: '' };
    }

    return { kind: 'text', html: '', text: raw };
  }, [product?.description]);

  useEffect(() => {
    // Scroll to top on mount and when product ID changes
    window.scrollTo(0, 0);

    // Fetch product from API - optimized with parallel fetching
    const loadProduct = async () => {
      try {
        // Fetch product by ID and all products in parallel
        const [foundProduct, allProducts] = await Promise.all([
          fetchProductById(id),
          fetchAllProducts()
        ]);
        
        setProducts(allProducts || []);
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
    // Calculate prices to pass to download page
    const productPrice = parseFloat(product.price) || 0;
    const productDiscount = parseFloat(product.discount) || 0;
    const discountedPrice = productDiscount > 0
      ? Math.round(productPrice * (1 - productDiscount / 100))
      : productPrice;

    // Pass price data to ensure consistency
    navigate(`/product/${id}/download`, {
      state: {
        productPrice,
        productDiscount,
        discountedPrice
      }
    });
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

  // Parse features from settings
  const parseFeatures = () => {
    const featuresText = settings?.product_details_features || "✓ No Logo\n✓ Lifetime Access\n📁 Google Drive Link\n📥 Easy To Download\n✓ No Watermark\n⚡ Instant Download";
    return featuresText.split('\n')
      .filter(line => line.trim())
      .map(line => {
        const trimmed = line.trim();
        // Extract icon (first character/emoji) and text
        const match = trimmed.match(/^(.{1,2})\s*(.+)$/);
        if (match) {
          return { icon: match[1], text: match[2] };
        }
        return { icon: '✓', text: trimmed };
      });
  };

  const features = parseFeatures();

  // Generate tags from settings and product data
  const getTags = () => {
    const defaultTags = settings?.product_details_default_tags || 'Skilcart,Reels Kit,Instagram Reels Bundle,Viral Reels Bundle,Animation reels bundle,AI reels bundle,Free download';
    const tagsArray = defaultTags.split(',').map(t => t.trim()).filter(Boolean);
    // Add product category if not already in tags
    if (product.category && !tagsArray.includes(product.category)) {
      tagsArray.unshift(product.category);
    }
    return tagsArray;
  };

  const tags = getTags();

  // Get description title
  const getDescriptionTitle = () => {
    const titleFormat = settings?.product_details_description_title || '{title} - Fun, Viral & Engaging!';
    const cleanTitle = stripHTMLSimple(product.title);
    return titleFormat.replace('{title}', cleanTitle);
  };

  // Generate structured data for product
  const productStructuredData = product ? {
    "@context": "https://schema.org",
    "@type": "Product",
    "name": stripHTMLSimple(product.title),
    "description": stripHTMLSimple(product.description || ''),
    "image": product.image || (product.images && product.images[0]) || '',
    "offers": {
      "@type": "Offer",
      "url": `${process.env.REACT_APP_SITE_URL || (process.env.NODE_ENV === 'production' ? 'https://hamrodigicart.com' : 'http://localhost:3000')}/product/${product.id}`,
      "priceCurrency": "NPR",
      "price": discountedPrice.toString(),
      "availability": "https://schema.org/InStock",
      "priceValidUntil": new Date(new Date().setFullYear(new Date().getFullYear() + 1)).toISOString().split('T')[0]
    },
    "brand": {
      "@type": "Brand",
      "name": "Hamro Digi Cart"
    },
    "category": product.category || "Digital Products"
  } : null;

  return (
    <div className="product-details-page">
      {product && (
        <SEO 
          title={stripHTMLSimple(product.title)}
          description={stripHTMLSimple(product.description || product.title + ' - Premium digital product available for instant download')}
          keywords={`${product.category || ''}, digital product, ${stripHTMLSimple(product.title)}, download, Nepal`}
          image={product.image || (product.images && product.images[0]) || ''}
          type="product"
          structuredData={productStructuredData}
        />
      )}
      <div className="product-details-container">
        {/* Breadcrumbs */}
        <div className="breadcrumbs">
          <span>{stripHTMLSimple(product.title)}</span>
        </div>

        {/* Main Content */}
        <div className="product-details-layout">
          {/* Left Column - Main Content */}
          <div className="product-main-content">
            {/* Product Title */}
            <h1 className="product-main-title">{stripHTMLSimple(product.title)}</h1>

            {/* Category Badge */}
            <div className="product-category-badge">
              <span className="category-tag">{product.category || 'Skilcart'}</span>
            </div>

            {/* Product Image - Full Image Display */}
            <div className="product-image-display">
              <div className="product-title-overlay">
                <h2>{stripHTMLSimple(product.title)}</h2>
              </div>
              <div className="product-image-container-full">
                {(() => {
                  // Support both old format (images array) and new format (image string)
                  const productImage = product.image || (product.images && product.images[0] ? product.images[0] : null);
                  return productImage ? (
                    <img
                      src={productImage}
                      alt={stripHTMLSimple(product.title)}
                      loading="eager"
                      decoding="async"
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
              {features.map((feature, index) => (
                <div key={index} className="feature-box">
                  <span className="feature-icon">{feature.icon}</span>
                  <span>{feature.text}</span>
                </div>
              ))}
            </div>

            {/* Pricing and Download Button */}
            <div className="product-pricing-section">
              <div className="price-display">
                {product.discount && product.discount > 0 ? (
                  <div className="price-with-discount-details">
                    <div className="price-row">
                      <span className="price-amount-original">रु{product.price}</span>
                      <span className="price-amount">रु{discountedPrice}</span>
                    </div>
                    <span className="discount-badge-large">{product.discount}% OFF</span>
                  </div>
                ) : (
                  <span className="price-amount">रु{product.price}</span>
                )}
              </div>
              <button onClick={handleDownload} className="btn-download">
                <span className="download-icon">🛒</span>
                <span>{settings?.product_details_button_text || 'Download'}</span>
                <span className="download-badge">✨</span>
              </button>
            </div>

            {/* Product Description */}
            <div className="product-description-section">
              <h3>{getDescriptionTitle()}</h3>
              {descriptionForDisplay.kind === 'html' ? (
                <div
                  className="product-description-content"
                  dangerouslySetInnerHTML={{ __html: descriptionForDisplay.html }}
                />
              ) : descriptionForDisplay.kind === 'text' ? (
                <p className="product-description-content product-description-prewrap">
                  {descriptionForDisplay.text}
                </p>
              ) : (
                <p className="product-description-content">
                  {`Get access to ${stripHTMLSimple(product.title)}. Perfect for social media creators looking to create engaging, viral content. This bundle includes high-quality animations and templates that are ready to use. No watermarks, no logos - just pure creative content to help you grow your online presence.`}
                </p>
              )}
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
                      <span className="price-amount-original">रु{product.price}</span>
                      <span className="price-amount">रु{discountedPrice}</span>
                    </div>
                    <span className="discount-badge-large">{product.discount}% OFF</span>
                  </div>
                ) : (
                  <span className="price-amount">रु{product.price}</span>
                )}
              </div>
              <button onClick={handleDownload} className="btn-download-sidebar">
                <span className="download-icon">🛒</span>
                <span>{settings?.product_details_button_text || 'Download'}</span>
                <span className="download-badge">✨</span>
              </button>
            </div>

            {/* Related Bundles */}
            {relatedProducts.length > 0 && (
              <div className="related-bundles">
                <h3>{settings?.product_details_related_title || 'Get Epic Viral Instagram Reels Bundle For Better Video Content'}</h3>
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
                <h4>{settings?.product_details_ad_title || 'Amazon Month of the SALE'}</h4>
                <p>{settings?.product_details_ad_description || 'Special offers and discounts'}</p>
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
