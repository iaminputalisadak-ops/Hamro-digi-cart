<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$pdo = getDBConnection();
$apiUrl = '../api/products.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Panel</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <div class="page-header">
                    <h1>Products</h1>
                    <button class="btn btn-primary" onclick="openProductModal()">+ Add Product</button>
                </div>
                
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="products-table">
                            <tr><td colspan="8">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Modal -->
    <div id="productModal" class="modal-overlay" style="display: none;">
        <div class="modal">
            <div class="modal-header">
                <h2 id="modalTitle">Add Product</h2>
                <button class="modal-close" onclick="closeProductModal()">&times;</button>
            </div>
            <form id="productForm">
                <input type="hidden" id="productId">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" id="productTitle" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="productDescription"></textarea>
                </div>
                <div class="form-group">
                    <label>Price (₹) *</label>
                    <input type="number" id="productPrice" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Discount (%)</label>
                    <input type="number" id="productDiscount" step="0.01" min="0" max="100">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select id="productCategory"></select>
                </div>
                <div class="form-group">
                    <label>Product Image</label>
                    <div style="display: flex; gap: 15px; align-items: flex-start; margin-bottom: 10px;">
                        <div style="flex: 1;">
                            <input type="file" id="productImageFile" accept="image/*" style="margin-bottom: 10px; width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            <div style="text-align: center; margin: 10px 0; color: #666;">OR</div>
                            <input type="url" id="productImage" placeholder="Enter image URL" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div id="imagePreview" style="width: 120px; height: 120px; border: 2px dashed #ddd; border-radius: 8px; display: none; align-items: center; justify-content: center; overflow: hidden; background: #f9f9f9; flex-shrink: 0;">
                            <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                        </div>
                    </div>
                    <small style="color: #666; display: block; margin-top: 5px;">Upload an image from your device or enter an image URL</small>
                </div>
                <div class="form-group">
                    <label>Product Download Link</label>
                    <input type="url" id="productLink" placeholder="https://drive.google.com/... or https://dropbox.com/...">
                    <small style="color: #666; display: block; margin-top: 5px;">This link will be automatically sent to customers when their order is approved</small>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="productStatus">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger" onclick="closeProductModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="assets/admin.js"></script>
    <script>
        const apiUrl = '<?php echo $apiUrl; ?>';
        
        // Load products
        function loadProducts() {
            fetch(apiUrl)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        displayProducts(data.data);
                    }
                });
        }
        
        // Load categories
        function loadCategories() {
            fetch('../api/categories.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('productCategory');
                        select.innerHTML = '<option value="">Select Category</option>';
                        data.data.forEach(cat => {
                            select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                        });
                    }
                });
        }
        
        function displayProducts(products) {
            const tbody = document.getElementById('products-table');
            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8">No products found</td></tr>';
                return;
            }
            
            tbody.innerHTML = products.map(product => {
                const hasLink = product.product_link && product.product_link.trim() !== '';
                return `
                <tr>
                    <td>#${product.id}</td>
                    <td><img src="${product.image || 'https://via.placeholder.com/50'}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"></td>
                    <td>
                        ${product.title}
                        ${hasLink ? '<span style="margin-left: 8px; color: #22c55e; font-size: 12px;" title="Product link configured">🔗</span>' : '<span style="margin-left: 8px; color: #ef4444; font-size: 12px;" title="No product link configured">⚠️</span>'}
                    </td>
                    <td>${product.category_name || 'Uncategorized'}</td>
                    <td>₹${product.price}</td>
                    <td>${product.discount > 0 ? product.discount + '%' : '-'}</td>
                    <td><span class="status-badge status-${product.status}">${product.status}</span></td>
                    <td>
                        <button class="btn btn-edit btn-small" onclick="editProduct(${product.id})">Edit</button>
                        <button class="btn btn-danger btn-small" onclick="deleteProduct(${product.id})">Delete</button>
                    </td>
                </tr>
            `;
            }).join('');
        }
        
        function openProductModal(product = null) {
            document.getElementById('productModal').style.display = 'flex';
            document.getElementById('modalTitle').textContent = product ? 'Edit Product' : 'Add Product';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            document.getElementById('productImageFile').value = '';
            document.getElementById('imagePreview').style.display = 'none';
            
            if (product) {
                document.getElementById('productId').value = product.id;
                document.getElementById('productTitle').value = product.title;
                document.getElementById('productDescription').value = product.description || '';
                document.getElementById('productPrice').value = product.price;
                document.getElementById('productDiscount').value = product.discount || 0;
                document.getElementById('productCategory').value = product.category_id || '';
                document.getElementById('productImage').value = product.image || '';
                document.getElementById('productLink').value = product.product_link || '';
                document.getElementById('productStatus').value = product.status;
                
                // Show preview if image exists
                if (product.image) {
                    document.getElementById('previewImg').src = product.image;
                    document.getElementById('imagePreview').style.display = 'flex';
                }
            }
        }
        
        // Handle image file selection
        document.getElementById('productImageFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.match('image.*')) {
                    alert('Please select an image file (JPEG, PNG, GIF, or WebP)');
                    this.value = '';
                    return;
                }
                
                // Show preview immediately from file
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImg = document.getElementById('previewImg');
                    const imagePreview = document.getElementById('imagePreview');
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'flex';
                    
                    // Add error handler for preview
                    previewImg.onerror = function() {
                        console.error('Failed to load preview image');
                        previewImg.src = '';
                    };
                };
                reader.onerror = function() {
                    alert('Error reading file. Please try again.');
                };
                reader.readAsDataURL(file);
                
                // Clear URL input when file is selected
                document.getElementById('productImage').value = '';
            } else {
                // Hide preview if no file selected
                document.getElementById('imagePreview').style.display = 'none';
            }
        });
        
        // Handle URL input change
        document.getElementById('productImage').addEventListener('input', function(e) {
            const url = e.target.value.trim();
            const previewImg = document.getElementById('previewImg');
            const imagePreview = document.getElementById('imagePreview');
            
            if (url) {
                // Set up error handler
                previewImg.onerror = function() {
                    console.error('Failed to load image from URL:', url);
                    previewImg.src = '';
                    imagePreview.style.display = 'none';
                };
                
                previewImg.onload = function() {
                    imagePreview.style.display = 'flex';
                };
                
                previewImg.src = url;
                imagePreview.style.display = 'flex';
                
                // Clear file input when URL is entered
                document.getElementById('productImageFile').value = '';
            } else {
                imagePreview.style.display = 'none';
            }
        });
        
        // Upload file function
        async function uploadImageFile(file) {
            const formData = new FormData();
            formData.append('file', file);
            
            try {
                console.log('Starting upload for file:', file.name, 'Size:', file.size, 'Type:', file.type);
                
                const response = await fetch('../api/upload.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Upload response error:', response.status, errorText);
                    throw new Error(`Upload failed: ${response.status} ${response.statusText}`);
                }
                
                const responseText = await response.text();
                console.log('Upload response text:', responseText);
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Failed to parse JSON response:', parseError);
                    console.error('Response was:', responseText);
                    throw new Error('Invalid response from server. Please check server logs.');
                }
                
                console.log('Upload response data:', data);
                
                if (data.success && data.data && data.data.url) {
                    const imageUrl = data.data.url;
                    console.log('Image uploaded successfully. Full URL:', imageUrl);
                    
                    // Verify URL is complete
                    if (!imageUrl.includes('http://') && !imageUrl.includes('https://')) {
                        console.warn('URL seems incomplete, attempting to fix...');
                        // If URL is relative, make it absolute
                        const baseUrl = window.location.origin;
                        const fullUrl = imageUrl.startsWith('/') ? baseUrl + imageUrl : baseUrl + '/' + imageUrl;
                        console.log('Fixed URL:', fullUrl);
                        return fullUrl;
                    }
                    
                    return imageUrl;
                } else {
                    console.error('Upload failed - invalid response:', data);
                    throw new Error(data.error || 'Upload failed - no URL returned');
                }
            } catch (error) {
                console.error('Upload error:', error);
                throw error;
            }
        }
        
        function closeProductModal() {
            document.getElementById('productModal').style.display = 'none';
        }
        
        function editProduct(id) {
            fetch(`${apiUrl}?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        openProductModal(data.data);
                    }
                });
        }
        
        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                fetch(`${apiUrl}?id=${id}`, { method: 'DELETE' })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            loadProducts();
                        } else {
                            alert('Error: ' + data.error);
                        }
                    });
            }
        }
        
        document.getElementById('productForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';
            
            try {
                let imageUrl = document.getElementById('productImage').value;
                
                // If file is selected, upload it first
                const fileInput = document.getElementById('productImageFile');
                if (fileInput.files.length > 0) {
                    try {
                        console.log('Uploading image file:', fileInput.files[0].name);
                        imageUrl = await uploadImageFile(fileInput.files[0]);
                        console.log('Image URL received:', imageUrl);
                        
                        if (!imageUrl || imageUrl.trim() === '') {
                            throw new Error('Upload succeeded but no URL was returned');
                        }
                        
                        // Update the URL input field
                        document.getElementById('productImage').value = imageUrl;
                        
                        // Update preview with the uploaded image URL
                        const previewImg = document.getElementById('previewImg');
                        const imagePreview = document.getElementById('imagePreview');
                        
                        previewImg.onerror = function() {
                            console.error('Failed to load uploaded image:', imageUrl);
                            alert('Image uploaded but preview failed to load. The image URL has been saved: ' + imageUrl);
                        };
                        
                        previewImg.onload = function() {
                            console.log('Preview image loaded successfully');
                            imagePreview.style.display = 'flex';
                        };
                        
                        previewImg.src = imageUrl;
                        imagePreview.style.display = 'flex';
                        
                        // Clear the file input after successful upload
                        fileInput.value = '';
                    } catch (error) {
                        console.error('Image upload failed:', error);
                        alert('Error uploading image: ' + error.message + '\n\nPlease check browser console for details.');
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                        return;
                    }
                }
                
                // Ensure we have a valid image URL (either uploaded or from input)
                if (!imageUrl || imageUrl.trim() === '') {
                    imageUrl = null; // Allow products without images
                }
                
                const formData = {
                    title: document.getElementById('productTitle').value,
                    description: document.getElementById('productDescription').value,
                    price: parseFloat(document.getElementById('productPrice').value),
                    discount: parseFloat(document.getElementById('productDiscount').value) || 0,
                    category_id: document.getElementById('productCategory').value || null,
                    image: imageUrl,
                    product_link: document.getElementById('productLink').value || '',
                    status: document.getElementById('productStatus').value
                };
                
                const productId = document.getElementById('productId').value;
                const method = productId ? 'PUT' : 'POST';
                
                if (productId) {
                    formData.id = productId;
                }
                
                const response = await fetch(apiUrl, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                if (data.success) {
                    closeProductModal();
                    loadProducts();
                    showNotification('Product saved successfully!', 'success');
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
        
        // Close modal on overlay click
        document.getElementById('productModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProductModal();
            }
        });
        
        // Load on page load
        loadProducts();
        loadCategories();
    </script>
</body>
</html>

