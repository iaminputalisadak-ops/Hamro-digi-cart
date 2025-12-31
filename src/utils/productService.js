// Product Service - Fetches products from PHP API

import API_BASE_URL, { apiRequest } from '../config/api';

/**
 * Fetch all products from the API
 * @returns {Promise<Array>} Array of product objects
 */
export const fetchAllProducts = async () => {
  try {
    const data = await apiRequest('products.php');
    if (data.success) {
      // Map API response to match frontend format
      return data.data.map(product => ({
        ...product,
        category: product.category_name || product.category || '',
        // Ensure compatibility with old format
        images: product.image ? [product.image] : []
      }));
    }
    return [];
  } catch (error) {
    console.error('Error fetching products:', error);
    // Fallback to empty array on error
    return [];
  }
};

/**
 * Fetch a single product by ID
 * @param {number|string} productId - The ID of the product to fetch
 * @returns {Promise<Object|null>} Product object or null if not found
 */
export const fetchProductById = async (productId) => {
  try {
    const data = await apiRequest(`products.php?id=${productId}`);
    if (data.success && data.data) {
      const product = data.data;
      return {
        ...product,
        category: product.category_name || product.category || '',
        images: product.image ? [product.image] : []
      };
    }
    return null;
  } catch (error) {
    console.error('Error fetching product by ID:', error);
    return null;
  }
};

/**
 * Fetch products by category
 * @param {string} categoryName - The category name to filter by
 * @returns {Promise<Array>} Array of products in the specified category
 */
export const fetchProductsByCategory = async (categoryName) => {
  try {
    // First get all categories to find category ID
    const categoriesData = await apiRequest('categories.php');
    if (!categoriesData.success) return [];
    
    const category = categoriesData.data.find(cat => 
      cat.name === categoryName || cat.slug === categoryName
    );
    
    if (!category) return [];
    
    // Fetch products by category ID
    const productsData = await apiRequest(`products.php?category_id=${category.id}`);
    if (productsData.success) {
      return productsData.data.map(product => ({
        ...product,
        category: product.category_name || product.category || '',
        images: product.image ? [product.image] : []
      }));
    }
    return [];
  } catch (error) {
    console.error('Error fetching products by category:', error);
    return [];
  }
};

/**
 * Search products by query
 * @param {string} query - Search query string
 * @returns {Promise<Array>} Array of matching products
 */
export const searchProducts = async (query) => {
  try {
    if (!query || !query.trim()) {
      return await fetchAllProducts();
    }

    const data = await apiRequest(`products.php?search=${encodeURIComponent(query)}`);
    if (data.success) {
      return data.data.map(product => ({
        ...product,
        category: product.category_name || product.category || '',
        images: product.image ? [product.image] : []
      }));
    }
    return [];
  } catch (error) {
    console.error('Error searching products:', error);
    return [];
  }
};

/**
 * Fetch all categories from the API
 * @returns {Promise<Array>} Array of category objects
 */
export const fetchAllCategories = async () => {
  try {
    const data = await apiRequest('categories.php');
    if (data.success) {
      return data.data;
    }
    return [];
  } catch (error) {
    console.error('Error fetching categories:', error);
    return [];
  }
};

/**
 * Submit a new order
 * @param {Object} orderData - The order details
 * @returns {Promise<Object>} The created order
 */
export const submitOrder = async (orderData) => {
  try {
    // Map frontend order data to API format
    const apiOrderData = {
      product_id: orderData.productId || orderData.product_id,
      customer_name: orderData.customerName || '',
      customer_email: orderData.customerEmail || orderData.email || '',
      customer_phone: orderData.customerPhone || orderData.phone || '',
      total_amount: parseFloat(orderData.totalAmount || orderData.total || 0),
      payment_screenshot: orderData.paymentScreenshot || orderData.payment_screenshot || '',
      notes: orderData.notes || (orderData.hasUpsell ? 'Includes upsell product' : '')
    };
    
    // Ensure required fields are present
    if (!apiOrderData.customer_email) {
      throw new Error('Customer email is required');
    }
    if (!apiOrderData.product_id) {
      throw new Error('Product ID is required');
    }

    try {
      const data = await apiRequest('orders.php', {
        method: 'POST',
        body: JSON.stringify(apiOrderData)
      });

      if (data.success) {
        // Return order in format expected by frontend
        return {
          id: data.data.id,
          ...apiOrderData,
          status: 'pending',
          date: new Date().toLocaleString()
        };
      } else {
        throw new Error(data.error || 'Failed to submit order');
      }
    } catch (error) {
      // If it's already an Error object, rethrow it
      if (error instanceof Error) {
        throw error;
      }
      // Otherwise, create a new error with the message
      throw new Error(error.message || 'Failed to submit order. Please check your connection and try again.');
    }
  } catch (error) {
    console.error('Error submitting order:', error);
    throw error;
  }
};

/**
 * Fetch all orders (admin only - not used in frontend)
 * @returns {Promise<Array>} Array of order objects
 */
export const fetchAllOrders = async () => {
  try {
    const data = await apiRequest('orders.php');
    if (data.success) {
      return data.data;
    }
    return [];
  } catch (error) {
    console.error('Error fetching orders:', error);
    return [];
  }
};

/**
 * Fetch payment QR code from settings
 * @returns {Promise<string|null>} QR code image URL or null if not found
 */
export const fetchPaymentQRCode = async () => {
  try {
    const data = await apiRequest('settings.php?key=payment_qr_code');
    if (data.success && data.data && data.data.value) {
      return data.data.value;
    }
    return null;
  } catch (error) {
    console.error('Error fetching QR code:', error);
    return null;
  }
};

/**
 * Listen for product updates
 * This is a simplified version that polls the API periodically
 * @param {Function} callback - Callback function to execute when products are updated
 * @returns {Function} Cleanup function to stop polling
 */
export const subscribeToProductUpdates = (callback) => {
  // Poll every 15 seconds for faster updates when admin adds products
  const intervalId = setInterval(async () => {
    try {
      await callback();
    } catch (error) {
      console.error('Error in product update subscription:', error);
    }
  }, 15000);

  // Return cleanup function
  return () => {
    clearInterval(intervalId);
  };
};
