<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$apiUrl = '../api/categories.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Admin Panel</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <div class="page-header">
                    <h1>Categories</h1>
                    <button class="btn btn-primary" onclick="openCategoryModal()">+ Add Category</button>
                </div>
                
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categories-table">
                            <tr><td colspan="5">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Category Modal -->
    <div id="categoryModal" class="modal-overlay" style="display: none;">
        <div class="modal">
            <div class="modal-header">
                <h2 id="modalTitle">Add Category</h2>
                <button class="modal-close" onclick="closeCategoryModal()">&times;</button>
            </div>
            <form id="categoryForm">
                <input type="hidden" id="categoryId">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" id="categoryName" required>
                </div>
                <div class="form-group">
                    <label>Slug *</label>
                    <input type="text" id="categorySlug" required>
                    <small style="color: #7f8c8d; font-size: 12px;">URL-friendly version (e.g., reels-bundle)</small>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="categoryDescription"></textarea>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger" onclick="closeCategoryModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="assets/admin.js"></script>
    <script>
        const apiUrl = '<?php echo $apiUrl; ?>';
        
        function loadCategories() {
            fetch(apiUrl)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        displayCategories(data.data);
                    }
                });
        }
        
        function displayCategories(categories) {
            const tbody = document.getElementById('categories-table');
            if (categories.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5">No categories found</td></tr>';
                return;
            }
            
            tbody.innerHTML = categories.map(cat => `
                <tr>
                    <td>#${cat.id}</td>
                    <td>${cat.name}</td>
                    <td>/${cat.slug}</td>
                    <td>${cat.description || '-'}</td>
                    <td>
                        <button class="btn btn-edit btn-small" onclick="editCategory(${cat.id})">Edit</button>
                        <button class="btn btn-danger btn-small" onclick="deleteCategory(${cat.id})">Delete</button>
                    </td>
                </tr>
            `).join('');
        }
        
        function openCategoryModal(category = null) {
            document.getElementById('categoryModal').style.display = 'flex';
            document.getElementById('modalTitle').textContent = category ? 'Edit Category' : 'Add Category';
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryId').value = '';
            
            if (category) {
                document.getElementById('categoryId').value = category.id;
                document.getElementById('categoryName').value = category.name;
                document.getElementById('categorySlug').value = category.slug;
                document.getElementById('categoryDescription').value = category.description || '';
            }
        }
        
        function closeCategoryModal() {
            document.getElementById('categoryModal').style.display = 'none';
        }
        
        function editCategory(id) {
            fetch(`${apiUrl}?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        openCategoryModal(data.data);
                    }
                });
        }
        
        function deleteCategory(id) {
            if (confirm('Are you sure you want to delete this category?')) {
                fetch(`${apiUrl}?id=${id}`, { method: 'DELETE' })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            loadCategories();
                        } else {
                            alert('Error: ' + data.error);
                        }
                    });
            }
        }
        
        // Auto-generate slug from name
        document.getElementById('categoryName').addEventListener('input', function() {
            if (!document.getElementById('categoryId').value) {
                const slug = this.value.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
                document.getElementById('categorySlug').value = slug;
            }
        });
        
        document.getElementById('categoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                name: document.getElementById('categoryName').value,
                slug: document.getElementById('categorySlug').value,
                description: document.getElementById('categoryDescription').value
            };
            
            const categoryId = document.getElementById('categoryId').value;
            const method = categoryId ? 'PUT' : 'POST';
            
            if (categoryId) {
                formData.id = categoryId;
            }
            
            fetch(apiUrl, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeCategoryModal();
                    loadCategories();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        });
        
        document.getElementById('categoryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCategoryModal();
            }
        });
        
        loadCategories();
    </script>
</body>
</html>





