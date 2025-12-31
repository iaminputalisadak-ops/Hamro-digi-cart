<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$apiUrl = '../api/offers.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offers - Admin Panel</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <div class="page-header">
                    <h1>Offers</h1>
                    <button class="btn btn-primary" onclick="openOfferModal()">+ Add Offer</button>
                </div>
                
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Discount</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="offers-table">
                            <tr><td colspan="8">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Offer Modal -->
    <div id="offerModal" class="modal-overlay" style="display: none;">
        <div class="modal">
            <div class="modal-header">
                <h2 id="modalTitle">Add Offer</h2>
                <button class="modal-close" onclick="closeOfferModal()">&times;</button>
            </div>
            <form id="offerForm">
                <input type="hidden" id="offerId">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" id="offerTitle" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="offerDescription"></textarea>
                </div>
                <div class="form-group">
                    <label>Discount Type *</label>
                    <select id="offerDiscountType" required>
                        <option value="percentage">Percentage (%)</option>
                        <option value="amount">Amount (₹)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Discount Value *</label>
                    <input type="number" id="offerDiscountValue" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" id="offerStartDate">
                </div>
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" id="offerEndDate">
                </div>
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="text" id="offerImage" placeholder="https://example.com/image.jpg">
                </div>
                <div class="form-group">
                    <label>Link</label>
                    <input type="text" id="offerLink" placeholder="/product/123 or /category/reels-bundle">
                </div>
                <div class="form-group">
                    <label>Status *</label>
                    <select id="offerStatus" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger" onclick="closeOfferModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="assets/admin.js"></script>
    <script>
        const apiUrl = '<?php echo $apiUrl; ?>';
        
        function loadOffers() {
            fetch(apiUrl)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        displayOffers(data.data);
                    }
                });
        }
        
        function displayOffers(offers) {
            const tbody = document.getElementById('offers-table');
            if (offers.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8">No offers found</td></tr>';
                return;
            }
            
            tbody.innerHTML = offers.map(offer => {
                const discount = offer.discount_type === 'percentage' 
                    ? `${offer.discount_value}%` 
                    : `₹${offer.discount_value}`;
                
                const imageCell = offer.image 
                    ? `<img src="${offer.image}" alt="${offer.title}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">`
                    : '<span style="color: #999;">No image</span>';
                
                const statusBadge = offer.status === 'active' 
                    ? '<span class="status-badge status-active">Active</span>'
                    : '<span class="status-badge status-inactive">Inactive</span>';
                
                const startDate = offer.start_date || '-';
                const endDate = offer.end_date || '-';
                
                return `
                    <tr>
                        <td>#${offer.id}</td>
                        <td>${imageCell}</td>
                        <td><strong>${offer.title}</strong></td>
                        <td>${discount}</td>
                        <td>${startDate}</td>
                        <td>${endDate}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="btn btn-edit btn-small" onclick="editOffer(${offer.id})">Edit</button>
                            <button class="btn btn-danger btn-small" onclick="deleteOffer(${offer.id})">Delete</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        function openOfferModal(offer = null) {
            document.getElementById('offerModal').style.display = 'flex';
            document.getElementById('modalTitle').textContent = offer ? 'Edit Offer' : 'Add Offer';
            document.getElementById('offerForm').reset();
            document.getElementById('offerId').value = '';
            
            if (offer) {
                document.getElementById('offerId').value = offer.id;
                document.getElementById('offerTitle').value = offer.title;
                document.getElementById('offerDescription').value = offer.description || '';
                document.getElementById('offerDiscountType').value = offer.discount_type || 'percentage';
                document.getElementById('offerDiscountValue').value = offer.discount_value;
                document.getElementById('offerStartDate').value = offer.start_date || '';
                document.getElementById('offerEndDate').value = offer.end_date || '';
                document.getElementById('offerImage').value = offer.image || '';
                document.getElementById('offerLink').value = offer.link || '';
                document.getElementById('offerStatus').value = offer.status || 'active';
            }
        }
        
        function closeOfferModal() {
            document.getElementById('offerModal').style.display = 'none';
        }
        
        function editOffer(id) {
            fetch(`${apiUrl}?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        openOfferModal(data.data);
                    }
                });
        }
        
        function deleteOffer(id) {
            if (confirm('Are you sure you want to delete this offer?')) {
                fetch(`${apiUrl}?id=${id}`, { method: 'DELETE' })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            loadOffers();
                        } else {
                            alert('Error: ' + data.error);
                        }
                    });
            }
        }
        
        document.getElementById('offerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                title: document.getElementById('offerTitle').value,
                description: document.getElementById('offerDescription').value,
                discount_type: document.getElementById('offerDiscountType').value,
                discount_value: parseFloat(document.getElementById('offerDiscountValue').value),
                start_date: document.getElementById('offerStartDate').value || null,
                end_date: document.getElementById('offerEndDate').value || null,
                image: document.getElementById('offerImage').value,
                link: document.getElementById('offerLink').value,
                status: document.getElementById('offerStatus').value
            };
            
            const offerId = document.getElementById('offerId').value;
            const method = offerId ? 'PUT' : 'POST';
            
            if (offerId) {
                formData.id = offerId;
            }
            
            fetch(apiUrl, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeOfferModal();
                    loadOffers();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        });
        
        document.getElementById('offerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOfferModal();
            }
        });
        
        loadOffers();
    </script>
</body>
</html>




