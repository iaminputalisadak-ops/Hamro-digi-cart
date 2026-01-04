# 🚀 AdSense Quick Start Guide

Quick reference for adding Google AdSense to your website.

---

## 📝 Step 1: Get Your AdSense Code

1. **Sign up:** https://www.google.com/adsense/
2. **Add your site:** `https://hamrodigicart.com`
3. **Get your Publisher ID:** Format: `ca-pub-XXXXXXXXXXXXXXXX`
4. **Copy the AdSense script** from AdSense dashboard

---

## ⚙️ Step 2: Add AdSense Script to index.html

Edit `public/index.html` and add this in the `<head>` section (before `</head>`):

```html
<!-- Google AdSense -->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXXXXXXXXXXXXXX"
     crossorigin="anonymous"></script>
```

**Replace `ca-pub-XXXXXXXXXXXXXXXX` with your actual Publisher ID!**

---

## 🎨 Step 3: Use the AdSense Component

### Example: Add Ad to Homepage

Edit `src/pages/Home.js`:

```javascript
// At the top, import the component:
import AdSense from '../components/AdSense';

// Then use it wherever you want ads:
<AdSense 
  adSlot="1234567890"              // Your ad unit ID from AdSense
  adFormat="horizontal"            // horizontal, vertical, auto, rectangle
  fullWidthResponsive={true}
  className="homepage-ad-banner"
/>
```

### Where to Place Ads:

1. **Homepage - After Banners:**
```javascript
{/* After promotional banners section */}
<AdSense 
  adSlot="YOUR_AD_UNIT_ID"
  adFormat="horizontal"
  className="homepage-ad-banner"
/>
```

2. **Between Products:**
```javascript
{products.map((product, index) => (
  <React.Fragment key={product.id}>
    <ProductCard product={product} />
    {(index + 1) % 6 === 0 && (
      <AdSense 
        adSlot="YOUR_AD_UNIT_ID"
        adFormat="horizontal"
        className="inline-ad"
      />
    )}
  </React.Fragment>
))}
```

3. **Product Details Page:**
```javascript
{/* After product image */}
<AdSense 
  adSlot="YOUR_AD_UNIT_ID"
  adFormat="rectangle"
  className="product-ad"
/>
```

---

## 🔧 Step 4: Configure Environment Variables (Optional)

For better management, add to `.env` file:

```env
REACT_APP_ADSENSE_PUBLISHER_ID=ca-pub-XXXXXXXXXXXXXXXX
REACT_APP_ADSENSE_ENABLED=true
```

Then the component will use these values automatically.

---

## ✅ Step 5: Create Ad Units in AdSense

1. Go to AdSense → **Ads** → **By ad unit** → **Display ads**
2. Click **"New ad unit"**
3. Name it (e.g., "Homepage Banner")
4. Choose **Responsive** format
5. Click **Create**
6. Copy the **Ad Unit ID** (the numbers, e.g., "1234567890")
7. Use this ID in the `adSlot` prop

---

## 📍 Recommended Ad Placements

| Location | Ad Format | Frequency |
|----------|-----------|-----------|
| Homepage top | horizontal | 1 |
| Between products | horizontal | Every 6 products |
| Product page sidebar | vertical | 1 |
| Footer | horizontal | 1 |

---

## 🚨 Important Notes

1. **Don't click your own ads** (will get banned!)
2. **Wait for approval** (can take 1-14 days)
3. **Use test ads** initially to verify placement
4. **Ensure HTTPS** is enabled (required)
5. **Follow AdSense policies** strictly

---

## 📖 Full Documentation

See `GOOGLE_ADSENSE_SETUP.md` for complete guide with:
- Detailed setup instructions
- Advanced configurations
- Styling options
- Troubleshooting
- Best practices

---

**Ready to monetize!** 🎉











