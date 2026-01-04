import React, { Suspense, lazy } from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { HelmetProvider } from 'react-helmet-async';
import { WebsiteSettingsProvider } from './contexts/WebsiteSettingsContext';
import Layout from './components/Layout';
import './App.css';

// Lazy load pages for code splitting and better performance
const Home = lazy(() => import('./pages/Home'));
const ProductDetails = lazy(() => import('./pages/ProductDetails'));
const ProductDownload = lazy(() => import('./pages/ProductDownload'));
const ProductPayment = lazy(() => import('./pages/ProductPayment'));
const Search = lazy(() => import('./pages/Search'));
const AboutUs = lazy(() => import('./pages/AboutUs'));
const PrivacyPolicy = lazy(() => import('./pages/PrivacyPolicy'));
const TermsConditions = lazy(() => import('./pages/TermsConditions'));
const RefundPolicy = lazy(() => import('./pages/RefundPolicy'));
const ContactUs = lazy(() => import('./pages/ContactUs'));
const OrderSuccess = lazy(() => import('./pages/OrderSuccess'));

// Loading component
const PageLoader = () => (
  <div style={{ 
    display: 'flex', 
    justifyContent: 'center', 
    alignItems: 'center', 
    minHeight: '50vh',
    fontSize: '1.2rem',
    color: '#666'
  }}>
    Loading...
  </div>
);

function App() {
  return (
    <HelmetProvider>
      <WebsiteSettingsProvider>
        <Router>
          <div className="App">
            <Suspense fallback={<PageLoader />}>
              <Routes>
                <Route path="/" element={<Layout />}>
                  <Route index element={<Home />} />
                  <Route path="product/:id" element={<ProductDetails />} />
                  <Route path="product/:id/download" element={<ProductDownload />} />
                  <Route path="product/:id/payment" element={<ProductPayment />} />
                  <Route path="search" element={<Search />} />
                  <Route path="about-us" element={<AboutUs />} />
                  <Route path="privacy-policy" element={<PrivacyPolicy />} />
                  <Route path="terms-conditions" element={<TermsConditions />} />
                  <Route path="refund-policy" element={<RefundPolicy />} />
                  <Route path="contact-us" element={<ContactUs />} />
                  <Route path="order-success" element={<OrderSuccess />} />
                  {/* All other routes (offers, categories, legacy routes) are handled by Home component */}
                  <Route path="*" element={<Home />} />
                </Route>
              </Routes>
            </Suspense>
          </div>
        </Router>
      </WebsiteSettingsProvider>
    </HelmetProvider>
  );
}

export default App;
