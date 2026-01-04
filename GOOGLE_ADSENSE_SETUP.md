# 📢 Google AdSense Integration Guide

This guide will help you integrate Google AdSense into your Hamro Digi Cart website.

---

## 📋 Prerequisites

1. ✅ Google AdSense account (sign up at: https://www.google.com/adsense/)
2. ✅ Website must be live and accessible (https://hamrodigicart.com)
3. ✅ Website must have substantial content (products, pages, etc.)
4. ✅ Website must comply with AdSense policies
5. ✅ SSL certificate must be installed (HTTPS)

---

## 🚀 Step 1: Get Your AdSense Code

1. **Login to Google AdSense**
   - Go to: https://www.google.com/adsense/
   - Login with your Google account

2. **Add Your Website**
   - Go to **Sites** → **Add site**
   - Enter: `https://hamrodigicart.com`
   - Click **Continue**

3. **Get Auto Ads Code** (Recommended)
   - Go to **Ads** → **Overview**
   - Click **Get started** under "Auto ads"
   - Copy the AdSense code (it looks like):
   ```html
   <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXXXXXXXXXXXXXX"
        crossorigin="anonymous"></script>
   ```

4. **Note Your Publisher ID**
   - Your publisher ID format: `ca-pub-XXXXXXXXXXXXXXXX`
   - You'll need this for ad placements

---

## 📝 Step 2: Add AdSense Code to Your Website

### Option A: Auto Ads (Recommended for Beginners)

Auto Ads automatically places ads in optimal locations on your site.

1. **Add to public/index.html**

   Edit `public/index.html` and add the AdSense script in the `<head>` section:

   ```html
   <!DOCTYPE html>
   <html lang="en">
     <head>
       <meta charset="utf-8" />
       <!-- ... other head content ... -->
       
       <!-- Google AdSense -->
       <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXXXXXXXXXXXXXX"
            crossorigin="anonymous"></script>
       
       <!-- ... rest of head content ... -->
     </head>
     <body>
       <!-- ... body content ... -->
     </body>
   </html>
   ```

2. **Rebuild Your App**
   ```bash
   npm run build
   ```

3. **Upload to Server**
   - Upload the new `build/index.html` to your server

### Option B: Manual Ad Placements (More Control)

For manual ad placements, you'll need to:
1. Add the AdSense script (same as above)
2. Create ad components (see Step 3)

---

## 🎨 Step 3: Create AdSense Component (For Manual Ads)

Create a reusable AdSense component for manual ad placements.

### Create `src/components/AdSense.js`

```javascript
import React, { useEffect } from 'react';

const AdSense = ({ 
  adSlot,           // Your ad unit ID (e.g., "1234567890")
  adFormat = "auto",
  fullWidthResponsive = true,
  style = { display: 'block' },
  className = ""
}) => {
  useEffect(() => {
    try {
      // Push ad to Google AdSense
      (window.adsbygoogle = window.adsbygoogle || []).push({});
    } catch (err) {
      console.error('AdSense error:', err);
    }
  }, []);

  return (
    <div className={`adsense-container ${className}`} style={style}>
      <ins
        className="adsbygoogle"
        style={{ display: 'block' }}
        data-ad-client="ca-pub-XXXXXXXXXXXXXXXX"  // Replace with your publisher ID
        data-ad-slot={adSlot}
        data-ad-format={adFormat}
        data-full-width-responsive={fullWidthResponsive ? 'true' : 'false'}
      ></ins>
    </div>
  );
};

export default AdSense;
```

**Important:** Replace `ca-pub-XXXXXXXXXXXXXXXX` with your actual AdSense Publisher ID.

---

## 📍 Step 4: Create Ad Unit in AdSense

Before using manual ads, create ad units in Google AdSense:

1. **Go to Ads** → **By ad unit** → **Display ads**
2. **Click "New ad unit"**
3. **Choose ad type:**
   - **Display ads** (recommended)
   - **In-article ads**
   - **In-feed ads**
   - **Matched content**
4. **Name your ad unit** (e.g., "Homepage Banner", "Sidebar Ad")
5. **Choose ad size:**
   - **Responsive** (recommended - adapts to screen size)
   - **Fixed sizes** (300x250, 728x90, etc.)
6. **Click "Create"**
7. **Copy the Ad Unit ID** (e.g., "1234567890")

---

## 🎯 Step 5: Place Ads on Your Website

### 5.1 Homepage Ads

Edit `src/pages/Home.js`:

```javascript
import AdSense from '../components/AdSense';

// Add after promotional banners section:
<AdSense 
  adSlot="1234567890"  // Your ad unit ID
  adFormat="horizontal"
  fullWidthResponsive={true}
  className="homepage-ad-banner"
/>

// Add in sidebar (if you have one):
<AdSense 
  adSlot="0987654321"  // Different ad unit ID
  adFormat="vertical"
  fullWidthResponsive={true}
  className="sidebar-ad"
/>
```

### 5.2 Product Details Page Ads

Edit `src/pages/ProductDetails.js`:

```javascript
import AdSense from '../components/AdSense';

// Add after product image:
<div className="product-ad">
  <AdSense 
    adSlot="1112223333"
    adFormat="rectangle"
    fullWidthResponsive={true}
  />
</div>

// Add in sidebar:
<AdSense 
  adSlot="4445556666"
  adFormat="vertical"
  fullWidthResponsive={true}
/>
```

### 5.3 Between Product Listings

Edit `src/components/ProductCard.js` or `src/pages/Home.js`:

```javascript
{/* After every 6 products */}
{products.map((product, index) => (
  <React.Fragment key={product.id}>
    <ProductCard product={product} />
    {(index + 1) % 6 === 0 && (
      <div className="inline-ad">
        <AdSense 
          adSlot="7778889999"
          adFormat="horizontal"
          fullWidthResponsive={true}
        />
      </div>
    )}
  </React.Fragment>
))}
```

---

## 💅 Step 6: Style Your Ads (Optional)

Add CSS for ad containers in your component CSS files:

```css
/* Homepage Ad Styles */
.homepage-ad-banner {
  margin: 2rem 0;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
  text-align: center;
  min-height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Sidebar Ad Styles */
.sidebar-ad {
  margin: 1rem 0;
  padding: 1rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  text-align: center;
  min-height: 250px;
}

/* Inline Ad Styles */
.inline-ad {
  margin: 2rem auto;
  padding: 1rem;
  max-width: 728px;
  text-align: center;
}

/* Product Page Ad Styles */
.product-ad {
  margin: 2rem 0;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
  text-align: center;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .homepage-ad-banner,
  .inline-ad {
    margin: 1rem 0;
    padding: 0.5rem;
  }
  
  .sidebar-ad,
  .product-ad {
    margin: 1rem 0;
  }
}
```

---

## 📱 Step 7: Responsive Ads Best Practices

### Ad Placement Guidelines:

1. **Above the Fold** (visible without scrolling):
   - ✅ Good for revenue
   - ⚠️ Don't overcrowd
   - ⚠️ Ensure content is visible

2. **Between Content**:
   - ✅ Natural reading flow
   - ✅ Good user experience
   - ✅ Good revenue potential

3. **Sidebar**:
   - ✅ Doesn't interrupt content
   - ✅ Good for desktop
   - ⚠️ Consider mobile layout

4. **Footer**:
   - ✅ Non-intrusive
   - ✅ Lower revenue but better UX

### Mobile Considerations:

- Use responsive ad units
- Test on actual mobile devices
- Ensure ads don't slow down page load
- Don't place too many ads on mobile

---

## 🔒 Step 8: AdSense Policies Compliance

Make sure your site complies with AdSense policies:

1. **Content Requirements:**
   - ✅ Sufficient unique content
   - ✅ Original content (not duplicated)
   - ✅ Clear navigation
   - ✅ About/Contact pages

2. **Prohibited Content:**
   - ❌ Copyrighted content without permission
   - ❌ Adult content
   - ❌ Gambling or casino content
   - ❌ Violent content
   - ❌ Drug-related content

3. **Technical Requirements:**
   - ✅ HTTPS enabled (SSL certificate)
   - ✅ Fast page load times
   - ✅ Mobile-friendly design
   - ✅ Clear privacy policy

4. **User Experience:**
   - ✅ Don't trick users into clicking ads
   - ✅ Don't place ads in misleading locations
   - ✅ Don't click your own ads (will get banned!)

---

## ✅ Step 9: Verify AdSense Integration

1. **Check Script Loading:**
   - Open browser console (F12)
   - Check for AdSense script errors
   - Verify script loads: `https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js`

2. **Check Ads Display:**
   - Visit your website
   - Look for ad placeholders (gray boxes initially)
   - Ads may take a few hours to start showing

3. **AdSense Dashboard:**
   - Go to AdSense dashboard
   - Check **Sites** → Your site should show "Ready"
   - Check **Ads** → Should show ad units

4. **Use AdSense Test Mode:**
   - In AdSense dashboard, enable test ads
   - Test ads will show even if account not approved
   - Helps verify placement before approval

---

## 📊 Step 10: Monitor Performance

1. **AdSense Dashboard:**
   - Check **Reports** for earnings
   - Monitor **Page RPM** (Revenue Per 1000 pageviews)
   - Check **Click-through rate (CTR)**

2. **Optimization Tips:**
   - Test different ad placements
   - Monitor which pages generate most revenue
   - A/B test ad formats
   - Remove low-performing ad units

---

## 🛠️ Advanced: Environment Variables

To keep your AdSense Publisher ID configurable:

1. **Add to `.env.production`:**
   ```env
   REACT_APP_ADSENSE_PUBLISHER_ID=ca-pub-XXXXXXXXXXXXXXXX
   REACT_APP_ADSENSE_ENABLED=true
   ```

2. **Update AdSense Component:**
   ```javascript
   const publisherId = process.env.REACT_APP_ADSENSE_PUBLISHER_ID || '';
   const adsEnabled = process.env.REACT_APP_ADSENSE_ENABLED === 'true';

   if (!adsEnabled) return null; // Don't show ads in development
   ```

3. **Rebuild:**
   ```bash
   npm run build
   ```

---

## 📋 Checklist

Before submitting for AdSense approval:

- [ ] Website is live at https://hamrodigicart.com
- [ ] SSL certificate installed (HTTPS)
- [ ] AdSense code added to `<head>`
- [ ] Privacy policy page exists
- [ ] About/Contact pages exist
- [ ] Sufficient original content
- [ ] Mobile-friendly design
- [ ] Fast page load times
- [ ] No policy violations
- [ ] Test ads showing correctly

---

## 🚨 Common Issues

### Ads Not Showing:

1. **Account Not Approved:**
   - AdSense may take 1-14 days to approve
   - Use test ads to verify placement

2. **Script Not Loading:**
   - Check browser console for errors
   - Verify script URL is correct
   - Check network connectivity

3. **Ad Blockers:**
   - Users with ad blockers won't see ads
   - This is normal and expected

4. **Invalid Ad Unit ID:**
   - Verify ad slot ID is correct
   - Check AdSense dashboard for correct ID

---

## 📞 Support

- **AdSense Help:** https://support.google.com/adsense
- **AdSense Policies:** https://support.google.com/adsense/answer/48182
- **AdSense Community:** https://support.google.com/adsense/community

---

**Remember:** 
- Never click your own ads
- Wait for account approval (can take 1-14 days)
- Monitor performance regularly
- Test on different devices
- Comply with AdSense policies at all times











