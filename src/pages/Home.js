import React, { useState, useEffect, useRef } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import ProductCard from '../components/ProductCard';
import { fetchAllProducts, fetchAllCategories, subscribeToProductUpdates } from '../utils/productService';
import { fetchAllOffers, generateOfferSlug } from '../utils/offerService';
import { fetchActiveHomepageBanners } from '../utils/homepageBannerService';
import { useWebsiteSettings } from '../hooks/useWebsiteSettings';
import './Home.css';

const Home = () => {
  const [products, setProducts] = useState([]);
  const [filteredProducts, setFilteredProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [offers, setOffers] = useState([]);
  const [homepageBanners, setHomepageBanners] = useState([]);
  const bannerRowRef = useRef(null);
  const [activeBannerIndex, setActiveBannerIndex] = useState(0);
  const [showPopup, setShowPopup] = useState(false);
  const [isMobile, setIsMobile] = useState(window.matchMedia ? window.matchMedia('(max-width: 768px)').matches : window.innerWidth <= 768);
  const [expandedCategories, setExpandedCategories] = useState(() => new Set());
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

      // Fetch offers from API
      const loadedOffers = await fetchAllOffers();
      setOffers(loadedOffers || []);

      // Fetch active homepage banners (for slider)
      const loadedBanners = await fetchActiveHomepageBanners();
      setHomepageBanners(loadedBanners || []);
    } catch (error) {
      console.error('Error loading products and categories:', error);
      // Set empty arrays on error to prevent undefined issues
      setCategories([]);
      setProducts([]);
      setOffers([]);
      setHomepageBanners([]);
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

  // Track mobile breakpoint for homepage category layout (mobile-only changes)
  useEffect(() => {
    const mq = window.matchMedia ? window.matchMedia('(max-width: 768px)') : null;
    const update = () => setIsMobile(mq ? mq.matches : window.innerWidth <= 768);

    update();

    if (!mq) {
      window.addEventListener('resize', update);
      return () => window.removeEventListener('resize', update);
    }

    if (mq.addEventListener) {
      mq.addEventListener('change', update);
      return () => mq.removeEventListener('change', update);
    }

    // Safari fallback
    mq.addListener(update);
    return () => mq.removeListener(update);
  }, []);

  // Reset banner active index when banners list changes
  useEffect(() => {
    setActiveBannerIndex(0);
    // scroll back to start
    if (bannerRowRef.current) {
      bannerRowRef.current.scrollTo({ left: 0, behavior: 'auto' });
    }
  }, [homepageBanners.length]);

  // Track active banner index based on scroll position (manual scrolling)
  useEffect(() => {
    const el = bannerRowRef.current;
    if (!el) return;

    let raf = 0;
    const onScroll = () => {
      if (raf) cancelAnimationFrame(raf);
      raf = requestAnimationFrame(() => {
        const cards = Array.from(el.querySelectorAll('.hero-banner-card'));
        if (!cards.length) return;
        const containerRect = el.getBoundingClientRect();
        const containerCenter = containerRect.left + containerRect.width / 2;
        let bestIdx = 0;
        let bestDist = Number.POSITIVE_INFINITY;
        cards.forEach((card, idx) => {
          const rect = card.getBoundingClientRect();
          const center = rect.left + rect.width / 2;
          const dist = Math.abs(center - containerCenter);
          if (dist < bestDist) {
            bestDist = dist;
            bestIdx = idx;
          }
        });
        setActiveBannerIndex(bestIdx);
      });
    };

    el.addEventListener('scroll', onScroll, { passive: true });
    // initial calc
    onScroll();
    return () => {
      if (raf) cancelAnimationFrame(raf);
      el.removeEventListener('scroll', onScroll);
    };
  }, [homepageBanners.length, isMobile]);

  useEffect(() => {
    // Filter products based on route
    let filtered = [...products];
    const path = location.pathname;

    // First check if path matches an offer
    const matchingOffer = offers.find(offer => {
      const offerSlug = generateOfferSlug(offer.title);
      return `/${offerSlug}` === path;
    });

    if (matchingOffer) {
      // Filter products based on offer criteria
      const offerTitle = matchingOffer.title.toLowerCase();
      const offerLink = matchingOffer.link ? matchingOffer.link.trim() : '';

      // If offer has a link (category name), filter by that
      if (offerLink) {
        filtered = products.filter(p => 
          p.category === offerLink ||
          p.category_name === offerLink ||
          p.title.toLowerCase().includes(offerLink.toLowerCase())
        );
      } else {
        // Try to match by offer title keywords
        // Extract price if present (e.g., "Reels bundle ₹99")
        const priceMatch = offerTitle.match(/₹?\s*(\d+)/);
        if (priceMatch) {
          const price = parseInt(priceMatch[1]);
          filtered = products.filter(p => p.price === price);
        } else {
          // Match by keywords in offer title
          const keywords = offerTitle.split(/\s+/).filter(k => k.length > 2);
          filtered = products.filter(p => {
            const productTitle = p.title.toLowerCase();
            const productCategory = (p.category || p.category_name || '').toLowerCase();
            
            // Check if any keyword matches product title or category
            return keywords.some(keyword => 
              productTitle.includes(keyword) || 
              productCategory.includes(keyword)
            );
          });
        }
      }
    } else if (path === '/whatsapp' || path === '/whatsapp-templates') {
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
        filtered = products.filter(p => p.category === category.name || p.category_name === category.name);
      }
    }

    setFilteredProducts(filtered);
  }, [products, location.pathname, categories, offers]);

  // Product cards now handle:
  // - card click -> /product/:id
  // - download button -> /product/:id/download

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

  // Get limited products for homepage display - always show 5 products
  const getLimitedProductsByCategory = (categoryName) => {
    const allProducts = getProductsByCategory(categoryName);
    return allProducts.slice(0, isMobile ? 4 : 5);
  };

  const toggleCategoryExpanded = (categoryId) => {
    setExpandedCategories((prev) => {
      const next = new Set(prev);
      if (next.has(categoryId)) next.delete(categoryId);
      else next.add(categoryId);
      return next;
    });
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

      {homepageBanners && homepageBanners.length > 0 ? (
        <section className="promotional-banners">
          {(() => {
            // Only show banners created from the Homepage Banner admin module (no fallback banners)
            const bannersToShow = (homepageBanners || []).filter((b) => {
              const hasAnyImage =
                !!b?.image_path ||
                !!b?.image_path_desktop ||
                !!b?.image_path_mobile;
              return hasAnyImage;
            });

            if (bannersToShow.length === 0) return null;

            // Single-row horizontal scroller with manual controls (desktop + mobile)
            const getBannerImage = (b) => {
              const useDevice = String(b?.use_device_images || '0') === '1' || b?.use_device_images === 1;
              if (useDevice) {
                const candidate = isMobile ? (b.image_path_mobile || '') : (b.image_path_desktop || '');
                if (candidate) return candidate;
              }
              return b.image_path;
            };

            const scrollToBanner = (idx) => {
              const el = bannerRowRef.current;
              if (!el) return;
              const cards = el.querySelectorAll('.hero-banner-card');
              const target = cards[idx];
              if (target && target.scrollIntoView) {
                target.scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' });
              }
            };

            const total = bannersToShow.length;
            const canPrev = total > 1 && activeBannerIndex > 0;
            const canNext = total > 1 && activeBannerIndex < total - 1;

            return (
              <div className="hero-banners-row-wrap" aria-label="Homepage banners">
                {total > 1 ? (
                  <>
                    <button
                      type="button"
                      className="hero-banners-nav hero-banners-nav--left"
                      onClick={() => scrollToBanner(Math.max(0, activeBannerIndex - 1))}
                      disabled={!canPrev}
                      aria-label="Previous banner"
                    >
                      ‹
                    </button>
                    <button
                      type="button"
                      className="hero-banners-nav hero-banners-nav--right"
                      onClick={() => scrollToBanner(Math.min(total - 1, activeBannerIndex + 1))}
                      disabled={!canNext}
                      aria-label="Next banner"
                    >
                      ›
                    </button>
                  </>
                ) : null}

                <div className="hero-banners-grid" ref={bannerRowRef}>
                  {bannersToShow.map((b, idx) => {
                    const hasContent = !!(b?.title || b?.subtitle || b?.button_text);
                    const buttonHref = b?.button_link || b?.link_url || '';

                    return (
                      <div className="hero-banner-card" key={b.id ?? `hero-banner-${idx}`}>
                        <img className="hero-banner-bg" src={getBannerImage(b)} alt="Homepage banner" />
                        {hasContent ? <div className="hero-banner-overlay" /> : null}

                        {hasContent ? (
                          <div className="hero-banner-content">
                            {b?.title ? <h2 className="hero-banner-title">{b.title}</h2> : null}
                            {b?.subtitle ? <p className="hero-banner-subtitle">{b.subtitle}</p> : null}
                            {b?.button_text && buttonHref ? (
                              <a className="hero-banner-cta" href={buttonHref}>
                                {b.button_text}
                              </a>
                            ) : null}
                          </div>
                        ) : null}

                        {b?.link_url ? (
                          <a className="hero-banner-link-overlay" href={b.link_url} aria-label={b.title || 'Open banner link'} />
                        ) : null}
                      </div>
                    );
                  })}
                </div>

                {total > 1 ? (
                  <div className="hero-banners-dots" aria-label="Banner navigation dots">
                    {bannersToShow.map((_, i) => (
                      <button
                        key={`hero-dot-${i}`}
                        type="button"
                        className={`hero-banners-dot ${i === activeBannerIndex ? 'active' : ''}`}
                        onClick={() => scrollToBanner(i)}
                        aria-label={`Go to banner ${i + 1}`}
                      />
                    ))}
                  </div>
                ) : null}
              </div>
            );
          })()}
        </section>
      ) : null}

      {/* Display All Categories in Body Section */}
      {
        location.pathname === '/' && (
          <div className="home-body-section">
            {categories.length > 0 ? (
              categories.map((category) => {
                const categoryProducts = getProductsByCategory(category.name);
                const limit = isMobile ? 4 : 5;
                const isExpanded = expandedCategories.has(category.id);
                const visibleProducts = isExpanded ? categoryProducts : categoryProducts.slice(0, limit);
                const canExpand = categoryProducts.length > limit;

                return (
                  <section key={category.id} className="category-section">
                    <div className="category-section-container">
                      <div className="category-header homepage-category-header">
                        <h2>{category.name}</h2>
                        {canExpand && (
                          <button
                            type="button"
                            className="see-more-products-btn see-more-products-btn--desktop"
                            onClick={() => toggleCategoryExpanded(category.id)}
                            aria-expanded={isExpanded}
                          >
                            {isExpanded ? 'See Less Products' : 'See More Products'}
                          </button>
                        )}
                      </div>
                      {categoryProducts.length > 0 ? (
                        <>
                          <div className={`homepage-products-scroll ${isExpanded ? 'is-expanded' : ''}`} aria-label={`${category.name} products`}>
                            <div className={isExpanded ? 'homepage-products-expanded-grid' : 'homepage-products-row'}>
                            {visibleProducts.map((product, idx) => (
                              <ProductCard
                                key={product.id}
                                product={product}
                                index={idx}
                              />
                            ))}
                            </div>
                          </div>
                          {/* Mobile-only placement for See More Products under the grid */}
                          <div className="see-more-products-mobile-wrap">
                            {canExpand && (
                              <button
                                type="button"
                                className="see-more-products-btn see-more-products-btn--mobile"
                                onClick={() => toggleCategoryExpanded(category.id)}
                                aria-expanded={isExpanded}
                              >
                                {isExpanded ? 'See Less Products' : 'See More Products'}
                              </button>
                            )}
                          </div>
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
            // First check if path matches an offer
            const matchingOffer = offers.find(offer => {
              const offerSlug = generateOfferSlug(offer.title);
              return `/${offerSlug}` === location.pathname;
            });
            
            if (matchingOffer) {
              return matchingOffer.title;
            }
            
            // Legacy routes
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
            
            // Check if path matches any category slug
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

