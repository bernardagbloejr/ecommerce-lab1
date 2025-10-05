<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management - E-Commerce Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .menu-tray {
            position: fixed;
            top: 16px;
            right: 16px;
            background: rgba(255,255,255,0.95);
            border: 1px solid #e6e6e6;
            border-radius: 8px;
            padding: 8px 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            z-index: 1000;
            min-width: 200px;
        }
        
        .menu-tray a, .menu-tray button { 
            margin-left: 6px; 
        }
        
        .user-info {
            font-size: 0.85em;
            color: #666;
            margin-bottom: 8px;
            border-bottom: 1px solid #eee;
            padding-bottom: 6px;
        }
        
        .main-content {
            padding-top: 120px;
        }
        
        .role-badge {
            font-size: 0.75em;
            padding: 2px 6px;
            border-radius: 10px;
            background: #D19C97;
            color: white;
        }
        
        .logout-btn {
            background: none;
            border: none;
            color: #dc3545;
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            cursor: pointer;
        }
        
        .logout-btn:hover {
            color: #a02622;
            text-decoration: underline;
        }

        .btn-custom {
            background-color: #D19C97;
            border-color: #D19C97;
            color: #fff;
        }

        .btn-custom:hover {
            background-color: #b77a7a;
            border-color: #b77a7a;
            color: #fff;
        }

        .card-header {
            background-color: #D19C97;
            color: white;
        }

        .table-actions {
            white-space: nowrap;
        }

        .category-stats {
            background: linear-gradient(135deg, #D19C97, #b77a7a);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .edit-form {
            display: none;
        }
    </style>
</head>
<body>
    <?php
    // Start session and check admin privileges
    session_start();
    require_once '../settings/core.php';
    
    // Check if user is logged in and is admin
    if (!is_logged_in() || !is_admin_user()) {
        header("Location: ../login/login.php");
        exit();
    }
    ?>

    <!-- Menu Tray -->
    <div class="menu-tray">
        <div class="user-info">
            <div><strong><i class="fa fa-user"></i> <?php echo htmlspecialchars($_SESSION['customer_name']); ?></strong></div>
            <div><span class="role-badge">Administrator</span></div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <a href="../index.php" class="btn btn-sm btn-outline-primary me-2">
                <i class="fa fa-home"></i> Home
            </a>
            <button type="button" class="logout-btn" onclick="logout()">
                <i class="fa fa-sign-out-alt"></i> Logout
            </button>
        </div>
    </div>

    <div class="container main-content">
        <!-- Category Statistics -->
        <div class="category-stats">
            <div class="row">
                <div class="col-md-6">
                    <h4><i class="fa fa-tags me-2"></i>Category Management</h4>
                    <p class="mb-0">Manage your product categories efficiently</p>
                </div>
                <div class="col-md-6 text-end">
                    <h2><span id="total-categories">0</span></h2>
                    <small>Total Categories</small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Add Category Form -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-plus me-2"></i>Add New Category</h5>
                    </div>
                    <div class="card-body">
                        <form id="add-category-form">
                            <div class="mb-3">
                                <label for="category-name" class="form-label">Category Name *</label>
                                <input type="text" class="form-control" id="category-name" 
                                       name="category_name" required maxlength="100"
                                       placeholder="Enter category name">
                                <div class="form-text">Category names must be unique</div>
                            </div>
                            <button type="submit" class="btn btn-custom w-100">
                                <i class="fa fa-plus me-2"></i>Add Category
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Categories List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa fa-list me-2"></i>Categories</h5>
                        <button class="btn btn-sm btn-outline-light" onclick="loadCategories()">
                            <i class="fa fa-refresh"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Category Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="categories-table-body">
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <div class="spinner-border text-secondary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #D19C97; color: white;">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-category-form">
                        <input type="hidden" id="edit-category-id" name="category_id">
                        <div class="mb-3">
                            <label for="edit-category-name" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="edit-category-name" 
                                   name="category_name" required maxlength="100">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-custom" onclick="updateCategory()">
                        <i class="fa fa-save me-2"></i>Update Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Global variables
        let editModal;

        $(document).ready(function() {
            // Initialize modal
            editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            
            // Load categories on page load
            loadCategories();
            
            // Add category form submission
            $('#add-category-form').submit(function(e) {
                e.preventDefault();
                addCategory();
            });

            // Real-time validation for category name
            $('#category-name, #edit-category-name').on('input', function() {
                const value = $(this).val().trim();
                if (value.length > 100) {
                    $(this).addClass('is-invalid');
                    $(this).siblings('.invalid-feedback').remove();
                    $(this).after('<div class="invalid-feedback">Category name must be 100 characters or less</div>');
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').remove();
                }
            });
        });

        // Load all categories
        function loadCategories() {
            $('#categories-table-body').html(`
                <tr>
                    <td colspan="3" class="text-center">
                        <div class="spinner-border text-secondary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            `);

            $.ajax({
                url: '../actions/fetch_category_action.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        displayCategories(response.categories);
                        $('#total-categories').text(response.categories.length);
                    } else {
                        $('#categories-table-body').html(`
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    <i class="fa fa-exclamation-circle me-2"></i>${response.message}
                                </td>
                            </tr>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading categories:', error);
                    $('#categories-table-body').html(`
                        <tr>
                            <td colspan="3" class="text-center text-danger">
                                <i class="fa fa-times-circle me-2"></i>Error loading categories. Please try again.
                            </td>
                        </tr>
                    `);
                }
            });
        }

        // Display categories in table
        function displayCategories(categories) {
            if (categories.length === 0) {
                $('#categories-table-body').html(`
                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            <i class="fa fa-inbox me-2"></i>No categories found. Add your first category!
                        </td>
                    </tr>
                `);
                return;
            }

            let html = '';
            categories.forEach(function(category) {
                html += `
                    <tr>
                        <td>${category.cat_id}</td>
                        <td><strong>${escapeHtml(category.cat_name)}</strong></td>
                        <td class="table-actions">
                            <button class="btn btn-sm btn-outline-primary me-2" 
                                    onclick="editCategory(${category.cat_id}, '${escapeHtml(category.cat_name)}')">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="deleteCategory(${category.cat_id}, '${escapeHtml(category.cat_name)}')">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                `;
            });
            $('#categories-table-body').html(html);
        }

        // Add new category
        function addCategory() {
            const categoryName = $('#category-name').val().trim();
            
            if (!categoryName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please enter a category name!'
                });
                return;
            }

            if (categoryName.length > 100) {
                Swal.fire({
                    icon: 'error',
                    title: 'Name Too Long',
                    text: 'Category name must be 100 characters or less!'
                });
                return;
            }

            // Show loading
            const submitBtn = $('#add-category-form button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Adding...');

            $.ajax({
                url: '../actions/add_category_action.php',
                type: 'POST',
                data: { category_name: categoryName },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Category added successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#add-category-form')[0].reset();
                        loadCategories();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to add category'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error adding category:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'System Error',
                        text: 'An error occurred while adding the category!'
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        }

        // Edit category - open modal
        function editCategory(categoryId, categoryName) {
            $('#edit-category-id').val(categoryId);
            $('#edit-category-name').val(categoryName);
            editModal.show();
        }

        // Update category
        function updateCategory() {
            const categoryId = $('#edit-category-id').val();
            const categoryName = $('#edit-category-name').val().trim();

            if (!categoryName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please enter a category name!'
                });
                return;
            }

            if (categoryName.length > 100) {
                Swal.fire({
                    icon: 'error',
                    title: 'Name Too Long',
                    text: 'Category name must be 100 characters or less!'
                });
                return;
            }

            // Show loading in modal button
            const updateBtn = $('#editCategoryModal .btn-custom');
            const originalText = updateBtn.html();
            updateBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Updating...');

            $.ajax({
                url: '../actions/update_category_action.php',
                type: 'POST',
                data: { 
                    category_id: categoryId,
                    category_name: categoryName 
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Category updated successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        editModal.hide();
                        loadCategories();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update category'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating category:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'System Error',
                        text: 'An error occurred while updating the category!'
                    });
                },
                complete: function() {
                    updateBtn.prop('disabled', false).html(originalText);
                }
            });
        }

        // Delete category
        function deleteCategory(categoryId, categoryName) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Delete category "${categoryName}"? This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading in confirmation
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the category',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '../actions/delete_category_action.php',
                        type: 'POST',
                        data: { category_id: categoryId },
                        dataType: 'json',
                        success: function(response) {
                            Swal.close();
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Category deleted successfully!',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadCategories();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete category'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            console.error('Error deleting category:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'System Error',
                                text: 'An error occurred while deleting the category!'
                            });
                        }
                    });
                }
            });
        }

        // Logout function
        function logout() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will be logged out of your account.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../actions/logout_action.php';
                }
            });
        }

        // Utility function to escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    </script>
</body>
</html>