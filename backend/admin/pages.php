<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$apiUrl = '../api/pages.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pages - Admin Panel</title>
    <?php include 'includes/favicon.php'; ?>
    <link rel="stylesheet" href="assets/admin.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <div class="page-header">
                    <h1>Pages</h1>
                </div>
                
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Route</th>
                                <th>Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="pages-table">
                            <tr><td colspan="5">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Page Editor Modal -->
    <div id="pageModal" class="modal-overlay" style="display: none;">
        <div class="modal" style="max-width: 900px;">
            <div class="modal-header">
                <h2 id="modalTitle">Edit Page</h2>
                <button class="modal-close" onclick="closePageModal()">&times;</button>
            </div>
            <form id="pageForm">
                <input type="hidden" id="pageId">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" id="pageTitle" required readonly>
                </div>
                <div class="form-group">
                    <label>Route *</label>
                    <input type="text" id="pageRoute" required readonly>
                </div>
                <div class="form-group">
                    <label>Content *</label>
                    <textarea id="pageContent"></textarea>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger" onclick="closePageModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="assets/admin.js"></script>
    <script>
        const apiUrl = '<?php echo $apiUrl; ?>';
        let editor;
        
        function loadPages() {
            fetch(apiUrl)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        displayPages(data.data);
                    }
                });
        }
        
        function displayPages(pages) {
            const tbody = document.getElementById('pages-table');
            if (pages.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5">No pages found</td></tr>';
                return;
            }
            
            tbody.innerHTML = pages.map((page, index) => `
                <tr>
                    <td>#${index + 1}</td>
                    <td>${page.title}</td>
                    <td>${page.route}</td>
                    <td>${new Date(page.updated_at).toLocaleDateString()}</td>
                    <td>
                        <button class="btn btn-edit btn-small" onclick="editPage(${page.id})">Edit</button>
                    </td>
                </tr>
            `).join('');
        }
        
        function editPage(id) {
            fetch(`${apiUrl}?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        openPageModal(data.data);
                    } else {
                        alert('Page not found');
                    }
                });
        }
        
        function openPageModal(page) {
            document.getElementById('pageModal').style.display = 'flex';
            document.getElementById('pageId').value = page.id;
            document.getElementById('pageTitle').value = page.title;
            document.getElementById('pageRoute').value = page.route;
            
            if (editor) {
                editor.setData(page.content || '');
            } else {
                ClassicEditor
                    .create(document.querySelector('#pageContent'))
                    .then(newEditor => {
                        editor = newEditor;
                        editor.setData(page.content || '');
                    })
                    .catch(error => {
                        console.error(error);
                        document.getElementById('pageContent').value = page.content || '';
                    });
            }
        }
        
        function closePageModal() {
            document.getElementById('pageModal').style.display = 'none';
        }
        
        document.getElementById('pageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                id: document.getElementById('pageId').value,
                title: document.getElementById('pageTitle').value,
                route: document.getElementById('pageRoute').value,
                content: editor ? editor.getData() : document.getElementById('pageContent').value
            };
            
            fetch(apiUrl, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closePageModal();
                    loadPages();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        });
        
        document.getElementById('pageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePageModal();
            }
        });
        
        loadPages();
    </script>
</body>
</html>

