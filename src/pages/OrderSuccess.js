import React, { useEffect } from 'react';
import { useLocation, useNavigate, Link } from 'react-router-dom';
import { stripHTMLSimple } from '../utils/htmlUtils';
import SEO from '../components/SEO';
import './OrderSuccess.css';

const OrderSuccess = () => {
    const location = useLocation();
    const navigate = useNavigate();
    const { order } = location.state || {};

    useEffect(() => {
        // Scroll to top on mount
        window.scrollTo(0, 0);

        // If no order data, redirect to home
        if (!order) {
            const timer = setTimeout(() => {
                navigate('/');
            }, 5000);
            return () => clearTimeout(timer);
        }
    }, [order, navigate]);

    if (!order) {
        return (
            <div className="order-success-page">
                <SEO title="Order Status" robots="noindex, nofollow" />
                <div className="order-success-content">
                    <div className="success-header">
                        <h1>No Order Found</h1>
                        <p>Redirecting you to home page...</p>
                        <Link to="/" className="btn-home">Go Home Now</Link>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="order-success-page">
            <SEO
                title="Order Received"
                description="Your order has been received. Payment verification is pending."
                robots="noindex, nofollow"
            />
            <div className="order-success-content">
            <div className="success-header">
                <div className="success-icon-container">
                    <div className="success-icon"></div>
                </div>
                <h1>Thank You For Your Purchase!</h1>
                <span className="celebration-emoji">🎉</span>
                <p className="success-message">
                    We have received your order and payment proof. Our team will verify your purchase, and your product/access will be activated within the next <strong>30 minutes</strong>.
                </p>
            </div>

            <div className="order-details-card">
                <div className="details-title">
                    <span>📋</span> Order Details
                </div>

                <div className="detail-row">
                    <span className="detail-label">Order ID:</span>
                    <span className="detail-value order-id-value">#{order.id.toString().slice(-4)}</span>
                </div>

                <div className="detail-row">
                    <span className="detail-label">Product:</span>
                    <span className="detail-value">{stripHTMLSimple(order.productTitle || '')}</span>
                </div>

                <div className="detail-row">
                    <span className="detail-label">Quantity:</span>
                    <span className="detail-value">1</span>
                </div>

                <div className="detail-row">
                    <span className="detail-label">Total Amount:</span>
                    <span className="detail-value amount-value">
                        Rs. {order.totalAmount 
                            ? parseFloat(order.totalAmount).toFixed(2) 
                            : order.total_amount 
                                ? parseFloat(order.total_amount).toFixed(2)
                                : '0.00'}
                    </span>
                </div>

                <div className="detail-row">
                    <span className="detail-label">Purchase Status:</span>
                    <span className="detail-value status-value">
                        <span className="status-dot">🕒</span> Pending Verification (Will Be Approved Within 30 Minutes)
                    </span>
                </div>

                <div className="detail-row">
                    <span className="detail-label">Order Date:</span>
                    <span className="detail-value">{order.date}</span>
                </div>

                <div className="detail-row">
                    <span className="detail-label">Email:</span>
                    <span className="detail-value">{order.customerEmail}</span>
                </div>
            </div>

            <div className="info-box">
                <div className="info-title">
                    <span>ℹ️</span> Download Link Coming Soon!
                </div>
                <p className="info-text">
                    Your download link will be sent to your email ({order.customerEmail}) within a few minutes.
                </p>
            </div>

            <div className="info-box">
                <div className="info-title">
                    <span>📧</span> Purchase Verification In Progress
                </div>
                <p className="info-text">
                    We have received your details. Once your payment is verified (within 30 minutes), we will send an email with download/access instructions to <strong>{order.customerEmail}</strong>.
                </p>
            </div>

            <div className="action-buttons">
                <Link to="/" className="btn-home">
                    <span>🏠</span> Back to Home
                </Link>
                <Link to={`/product/${order.productId}`} className="btn-view">
                    <span>👁️</span> View Product
                </Link>
            </div>
            </div>
        </div>
    );
};

export default OrderSuccess;
