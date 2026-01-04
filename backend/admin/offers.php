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
    <?php include 'includes/favicon.php'; ?>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <div class="page-header">
                    <div>
                        <h1>Offers</h1>
                        <p style="color: #666; margin-top: 5px; font-size: 14px;">Manage offer names that appear in the navigation menu</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn btn-primary" onclick="openOfferModal()">+ Add Offer</button>
                    </div>
                </div>
                
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Offer Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="offers-table">
                            <tr><td colspan="3">Loading...</td></tr>
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
                    <label>Offer Name *</label>
                    <input type="text" id="offerTitle" placeholder="e.g., Reels bundle रु99" required>
                    <small style="color: #666;">Enter the offer name that will be displayed in the navigation</small>
                </div>
                <div class="form-group">
                    <label>Filter Products By Category (Optional)</label>
                    <select id="offerCategory">
                        <option value="">Auto-detect from offer name</option>
                    </select>
                    <small style="color: #666;">Select a category to filter products. If empty, products will be matched by keywords in the offer name.</small>
                </div>
                <div class="form-group" id="categorySuggestionsContainer" style="display: none;">
                    <label style="font-size: 0.85rem; color: #666;">Suggested Categories:</label>
                    <div id="categorySuggestions" style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;"></div>
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
                tbody.innerHTML = '<tr><td colspan="3">No offers found. Click "+ Add Offer" to create one.</td></tr>';
                return;
            }
            
            tbody.innerHTML = offers.map(offer => {
                return `
                    <tr>
                        <td>#${offer.id}</td>
                        <td><strong>${escapeHtml(offer.title || 'Untitled Offer')}</strong></td>
                        <td>
                            <button class="btn btn-edit btn-small" onclick="editOffer(${offer.id})">Edit</button>
                            <button class="btn btn-danger btn-small" onclick="deleteOffer(${offer.id})">Delete</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function openOfferModal(offer = null) {
            document.getElementById('offerModal').style.display = 'flex';
            document.getElementById('modalTitle').textContent = offer ? 'Edit Offer' : 'Add Offer';
            document.getElementById('offerForm').reset();
            document.getElementById('offerId').value = '';
            document.getElementById('offerCategory').value = '';
            document.getElementById('categorySuggestionsContainer').style.display = 'none';
            
            if (offer) {
                document.getElementById('offerId').value = offer.id;
                document.getElementById('offerTitle').value = offer.title || '';
                // Use link field to store category name for filtering
                document.getElementById('offerCategory').value = offer.link || '';
            }
        }
        
        function closeOfferModal() {
            document.getElementById('offerModal').style.display = 'none';
            document.getElementById('categorySuggestionsContainer').style.display = 'none';
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
            if (confirm('Are you sure you want to delete this offer? This action cannot be undone.')) {
                fetch(`${apiUrl}?id=${id}`, { method: 'DELETE' })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            loadOffers();
                            showNotification('Offer deleted successfully!', 'success');
                        } else {
                            alert('Error: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error: ' + error.message);
                    });
            }
        }
        
        document.getElementById('offerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';
            
            const offerName = document.getElementById('offerTitle').value.trim();
            
            if (!offerName) {
                alert('Please enter an offer name');
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                return;
            }
            
            const categoryFilter = document.getElementById('offerCategory').value.trim();
            
            const formData = {
                title: offerName,
                link: categoryFilter // Use link field to store category name
            };
            
            const offerId = document.getElementById('offerId').value;
            const method = offerId ? 'PUT' : 'POST';
            
            if (offerId) {
                formData.id = parseInt(offerId);
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
                    showNotification(offerId ? 'Offer updated successfully!' : 'Offer added successfully!', 'success');
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            });
        });
        
        document.getElementById('offerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOfferModal();
            }
        });
        
        let allCategories = [];
        
        // Load categories from API
        function loadCategories() {
            fetch('../api/categories.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        allCategories = data.data || [];
                        populateCategoryDropdown();
                    }
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                });
        }
        
        // Populate category dropdown
        function populateCategoryDropdown() {
            const select = document.getElementById('offerCategory');
            // Keep the first option (Auto-detect)
            const firstOption = select.options[0];
            select.innerHTML = '';
            select.appendChild(firstOption);
            
            // Add all categories
            allCategories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.name;
                option.textContent = cat.name;
                select.appendChild(option);
            });
        }
        
        // Show category suggestions based on offer name
        function showCategorySuggestions(offerName) {
            const suggestionsContainer = document.getElementById('categorySuggestionsContainer');
            const suggestionsDiv = document.getElementById('categorySuggestions');
            
            if (!offerName || offerName.trim().length === 0) {
                suggestionsContainer.style.display = 'none';
                return;
            }
            
            const searchTerm = offerName.toLowerCase().trim();
            // Split search term into words for better matching
            const searchWords = searchTerm.split(/\s+/).filter(word => word.length > 1);
            
            const matchedCategories = allCategories.filter(cat => {
                const catName = cat.name.toLowerCase();
                // Check if category name contains any of the search words
                if (searchWords.length > 0) {
                    return searchWords.some(word => catName.includes(word));
                }
                return catName.includes(searchTerm);
            }).sort((a, b) => {
                // Sort by relevance - exact matches first, then starts-with matches, then alphabetical
                const aName = a.name.toLowerCase();
                const bName = b.name.toLowerCase();
                if (aName === searchTerm) return -1;
                if (bName === searchTerm) return 1;
                if (aName.startsWith(searchTerm)) return -1;
                if (bName.startsWith(searchTerm)) return 1;
                return aName.localeCompare(bName);
            });
            
            if (matchedCategories.length === 0) {
                suggestionsContainer.style.display = 'none';
                return;
            }
            
            // Show suggestions (limit to 5 most relevant)
            suggestionsDiv.innerHTML = '';
            matchedCategories.slice(0, 5).forEach(cat => {
                const suggestionBtn = document.createElement('button');
                suggestionBtn.type = 'button';
                suggestionBtn.className = 'category-suggestion-btn';
                suggestionBtn.textContent = cat.name;
                suggestionBtn.style.cssText = 'padding: 6px 12px; background: #e3f2fd; border: 1px solid #2196f3; border-radius: 4px; color: #1976d2; cursor: pointer; font-size: 0.85rem; transition: all 0.2s;';
                suggestionBtn.onmouseover = function() { this.style.background = '#bbdefb'; };
                suggestionBtn.onmouseout = function() { this.style.background = '#e3f2fd'; };
                suggestionBtn.onclick = function() {
                    document.getElementById('offerCategory').value = cat.name;
                    suggestionsContainer.style.display = 'none';
                };
                suggestionsDiv.appendChild(suggestionBtn);
            });
            
            suggestionsContainer.style.display = 'block';
        }
        
        // Add event listener to offer name input for suggestions
        const offerNameInput = document.getElementById('offerTitle');
        if (offerNameInput) {
            offerNameInput.addEventListener('input', function() {
                showCategorySuggestions(this.value);
            });
            
            offerNameInput.addEventListener('blur', function() {
                // Hide suggestions after a short delay (to allow clicking on suggestions)
                setTimeout(() => {
                    document.getElementById('categorySuggestionsContainer').style.display = 'none';
                }, 200);
            });
        }
        
        loadOffers();
        loadCategories();
    </script>
</body>
</html>




