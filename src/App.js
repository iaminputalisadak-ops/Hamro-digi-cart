import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Layout from './components/Layout';
import Home from './pages/Home';
import ProductDetails from './pages/ProductDetails';
import ProductDownload from './pages/ProductDownload';
import ProductPayment from './pages/ProductPayment';
import Search from './pages/Search';
import AboutUs from './pages/AboutUs';
import PrivacyPolicy from './pages/PrivacyPolicy';
import TermsConditions from './pages/TermsConditions';
import RefundPolicy from './pages/RefundPolicy';
import ContactUs from './pages/ContactUs';
import OrderSuccess from './pages/OrderSuccess';
import './App.css';

function App() {
  return (
    <Router>
      <div className="App">
        <Routes>
          <Route path="/" element={<Layout />}>
            <Route index element={<Home />} />
            <Route path="whatsapp" element={<Home />} />
            <Route path="reels-bundle" element={<Home />} />
            <Route path="combo-reels-bundle" element={<Home />} />
            <Route path="instagram-reels-bundle" element={<Home />} />
            <Route path="reels-bundle-99" element={<Home />} />
            <Route path="reels-bundle-149" element={<Home />} />
            <Route path="reels-bundle-199" element={<Home />} />
            <Route path="follow-us" element={<Home />} />
            <Route path="whatsapp-templates" element={<Home />} />
            <Route path="digital-planner" element={<Home />} />
            <Route path="social-media-pack" element={<Home />} />
            <Route path="video-templates" element={<Home />} />
            <Route path="tiktok-reel-bundle" element={<Home />} />
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
            <Route path="digital-planner-2025" element={<Home />} />
          </Route>
        </Routes>
      </div>
    </Router>
  );
}

export default App;
