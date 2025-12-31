# Product Link Management Guide

## How to Save Product Links in Admin Panel

### Step 1: Access Products Page
1. Login to Admin Panel: `http://localhost:8000/admin/login.php`
2. Navigate to **Products** from the sidebar menu

### Step 2: Add/Edit Product Link
When creating a new product or editing an existing product:

1. Click **"+ Add Product"** button (for new product) or **"Edit"** button (for existing product)
2. Fill in all the product details (Title, Description, Price, etc.)
3. **Find the "Product Download Link" field** (located after the Product Image field)
4. Enter the download link URL:
   - Google Drive share link: `https://drive.google.com/file/d/...`
   - Dropbox share link: `https://dropbox.com/s/...`
   - Any other direct download link
5. Click **"Save"** button

### Step 3: Verify Link is Saved
- Products with configured links will show a **🔗 green link icon** next to the product title
- Products without links will show a **⚠️ red warning icon**

## How It Works

### Automatic Email Delivery
When a customer purchases a product and the order is approved:

1. **Customer places order** → Order status: `pending`
2. **Admin approves order** → Order status changes to `approved`
3. **System automatically**:
   - Fetches the product's download link from the product settings
   - Sends email to customer's email address with the download link
   - No manual steps required!

### Product-Specific Links
- Each product can have its own unique download link
- When a customer buys Product A, they receive Product A's link
- When a customer buys Product B, they receive Product B's link
- Each product link is stored with the product and automatically used

## Important Notes

1. **Link Format**: The link should be a direct download link or a shareable link (Google Drive, Dropbox, etc.)
2. **Link Visibility**: Products without links will show a warning icon, but orders can still be approved
3. **Manual Override**: Admins can override the product link for specific orders if needed (in Order Details)
4. **Email Configuration**: Make sure SMTP settings are configured in Admin Panel → Settings → SMTP Email Settings

## Example Workflow

1. Admin creates "Premium Reels Bundle" product
2. Admin adds download link: `https://drive.google.com/file/d/ABC123XYZ`
3. Customer purchases "Premium Reels Bundle"
4. Admin approves the order
5. Customer automatically receives email with download link: `https://drive.google.com/file/d/ABC123XYZ`

---

**Need Help?**
- Check if SMTP is configured: Admin Panel → Settings → SMTP Email Settings
- Verify product link is saved: Look for 🔗 icon next to product title
- Test email delivery: Approve a test order and check customer's email




