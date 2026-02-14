import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, useLocation } from 'react-router-dom';
import { fetchProductById, submitOrder, fetchPaymentQRCode } from '../utils/productService';
import SEO from '../components/SEO';
import qrCodeFallback from '../assets/qr_code.png';
import './ProductPayment.css';

const ProductPayment = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const location = useLocation();
    const [product, setProduct] = useState(null);
    const [loading, setLoading] = useState(true);
    const [screenshot, setScreenshot] = useState(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState('');
    const [qrCode, setQrCode] = useState(qrCodeFallback);

    // Get data from location state (from ProductDownload page)
    const { 
      email, 
      phone, 
      productPrice,
      productDiscount,
      discountedPrice
    } = location.state || {};

    useEffect(() => {
        // Scroll to top on mount and when product ID changes
        window.scrollTo(0, 0);

        const loadProduct = async () => {
            try {
                const foundProduct = await fetchProductById(id);
                if (foundProduct) {
                    setProduct(foundProduct);
                } else {
                    setError('Product not found');
                }
            } catch (error) {
                console.error('Error loading product:', error);
                setError('Failed to load product');
            } finally {
                setLoading(false);
            }
        };

        const loadQRCode = async () => {
            try {
                const qrCodeUrl = await fetchPaymentQRCode();
                if (qrCodeUrl) {
                    setQrCode(qrCodeUrl);
                }
            } catch (error) {
                console.error('Error loading QR code:', error);
                // Keep fallback QR code if API fails
            }
        };

        loadProduct();
        loadQRCode();

        // If no contact info, redirect back
        if (!email || !phone) {
            navigate(`/product/${id}/download`);
        }
    }, [id, email, phone, navigate]);

    const handleScreenshotChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                setError('Image size should be less than 5MB');
                return;
            }
            const reader = new FileReader();
            reader.onloadend = () => {
                setScreenshot(reader.result);
                setError('');
            };
            reader.readAsDataURL(file);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!screenshot) {
            setError('Please upload the payment screenshot first.');
            return;
        }

        setIsSubmitting(true);
        setError(''); // Clear previous errors
        
        // Validate required data
        if (!product || !product.id) {
            setError('Product information is missing. Please refresh the page.');
            setIsSubmitting(false);
            return;
        }
        
        if (!email || !phone) {
            setError('Email and phone number are required.');
            setIsSubmitting(false);
            return;
        }
        
        try {
            // Use discounted price (product price only, without upsell)
            const finalTotalAmount = discountedPrice || productPrice || 0;
            
            const orderData = {
                productId: product.id,
                productTitle: product.title,
                customerEmail: email,
                customerPhone: phone,
                totalAmount: finalTotalAmount.toFixed(2),
                paymentScreenshot: screenshot
            };

            const newOrder = await submitOrder(orderData);
            // Ensure admin notification email was attempted before moving to success page
            // (The backend sends the email in the same request and returns adminEmailSent flag)
            if (newOrder && newOrder.adminEmailSent === false) {
                alert('Order saved, but admin notification email could not be sent. Please check SMTP settings in Admin → Settings.');
            }
            navigate('/order-success', { state: { order: newOrder } });
        } catch (err) {
            console.error('Order submission error:', err);
            const errorMessage = err.message || 'Failed to submit order. Please try again.';
            setError(errorMessage);
        } finally {
            setIsSubmitting(false);
        }
    };

    if (loading) {
        return (
            <div className="payment-loading">
                <SEO title="Payment" robots="noindex, nofollow" />
                <div className="loading-spinner">Loading product...</div>
            </div>
        );
    }
    
    if (!product || !product.id) {
        return (
            <div className="payment-error">
                <SEO title="Payment" robots="noindex, nofollow" />
                <h2>Product Not Found</h2>
                <p>Unable to load product information. Please try again.</p>
                <button onClick={() => navigate('/')} className="btn-primary">Go to Home</button>
            </div>
        );
    }

    return (
        <div className="product-payment-page">
            <SEO
                title={`Payment - ${product ? product.title : ''}`}
                description="Secure payment page. Upload payment screenshot to complete your order."
                robots="noindex, nofollow"
            />
            <div className="payment-content-wrapper">
                <div className="payment-card">
                <div className="payment-header">
                    <div className="secure-lock">🔒</div>
                    <h1>Secure Payment</h1>
                    <p>Complete your purchase securely</p>
                </div>

                <div className="order-summary-box">
                    <div className="box-header">
                        <span>📋</span> Order Summary
                    </div>
                    <div className="summary-item">
                        <span className="label">Product:</span>
                        <span className="value">{product.title}</span>
                    </div>
                    {productPrice !== undefined && (
                        <div className="summary-item">
                            <span className="label">Original Price:</span>
                            <span className="value">Rs. {parseFloat(productPrice).toFixed(2)}</span>
                        </div>
                    )}
                    {productDiscount > 0 && (
                        <div className="summary-item">
                            <span className="label">Discount:</span>
                            <span className="value discount-value">{productDiscount}% OFF</span>
                        </div>
                    )}
                    <div className="total-row">
                        <span className="label">Total Amount:</span>
                        <span className="value">Rs. {(discountedPrice || productPrice || 0).toFixed(2)}</span>
                    </div>
                </div>

                <div className="scan-pay-section">
                    <div className="section-title">
                        <span>🔳</span> Scan & Pay
                    </div>
                    <div className="qr-container">
                        <img src={qrCode} alt="Payment QR Code" className="qr-image" />
                        <p className="qr-instruction">
                            Scan this QR with your Banking/Esewa/Khalti app and complete the payment.
                            <strong>After paying, upload the payment screenshot below.</strong>
                        </p>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="upload-section">
                    <div className="section-title">
                        <span>📤</span> Upload Payment Screenshot
                    </div>
                    <p className="upload-hint">
                        Please upload a clear screenshot of your payment confirmation. This will be visible in the admin panel for verification.
                    </p>

                    <div className="file-input-wrapper">
                        <input
                            type="file"
                            accept="image/*"
                            onChange={handleScreenshotChange}
                            id="payment-screenshot"
                            className="file-input"
                        />
                        <label htmlFor="payment-screenshot" className="file-label">
                            {screenshot ? '✅ Screenshot Selected' : 'Choose File'}
                        </label>
                        <span className="file-name">{screenshot ? 'Screenshot ready' : 'No file chosen'}</span>
                    </div>

                    {error && <div className="error-message">{error}</div>}

                    <button
                        type="submit"
                        className={`submit-payment-btn ${isSubmitting ? 'loading' : ''}`}
                        disabled={isSubmitting}
                    >
                        {isSubmitting ? 'Submitting...' : 'Submit Payment Proof'}
                    </button>
                </form>

                <div className="security-badge">
                    <span>🛡️</span> Your upload is secured with 256-bit SSL encryption
                </div>
                </div>
            </div>
        </div>
    );
};

export default ProductPayment;
