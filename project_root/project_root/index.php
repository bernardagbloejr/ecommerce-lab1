<?php
// Start session to check login status
session_start();

// Check if user is logged in
$is_logged_in = isset($_SESSION['customer_id']);
$customer_name = $is_logged_in ? $_SESSION['customer_name'] : '';
$user_role = $is_logged_in ? $_SESSION['user_role'] : 0;
$role_text = ($user_role == 1) ? 'Administrator' : 'Customer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home - E-Commerce Platform</title>
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
			min-width: 250px;
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
		
		.welcome-section {
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

		.admin-menu {
			margin-top: 8px;
			padding-top: 8px;
			border-top: 1px solid #eee;
		}

		.btn-admin {
			background-color: #D19C97;
			border-color: #D19C97;
			color: white;
			font-size: 0.8em;
		}

		.btn-admin:hover {
			background-color: #b77a7a;
			border-color: #b77a7a;
			color: white;
		}
	</style>
</head>
<body>

	<div class="menu-tray">
		<?php if ($is_logged_in): ?>
			<!-- Logged in user menu -->
			<div class="user-info">
				<div><strong><i class="fa fa-user"></i> <?php echo htmlspecialchars($customer_name); ?></strong></div>
				<div><span class="role-badge"><?php echo $role_text; ?></span></div>
			</div>
			
			<?php if ($user_role == 1): ?>
				<!-- Admin menu -->
				<div class="admin-menu">
					<div class="d-flex flex-wrap gap-2 mb-2">
						<a href="admin/category.php" class="btn btn-sm btn-admin">
							<i class="fa fa-tags"></i> Categories
						</a>
					</div>
				</div>
			<?php endif; ?>
			
			<div class="d-flex justify-content-between align-items-center">
				<span class="me-2 text-muted">Menu:</span>
				<button type="button" class="logout-btn" onclick="logout()">
					<i class="fa fa-sign-out-alt"></i> Logout
				</button>
			</div>
		<?php else: ?>
			<!-- Guest user menu -->
			<span class="me-2">Menu:</span>
			<a href="login/register.php" class="btn btn-sm btn-outline-primary">
				<i class="fa fa-user-plus"></i> Register
			</a>
			<a href="login/login.php" class="btn btn-sm btn-outline-secondary">
				<i class="fa fa-sign-in-alt"></i> Login
			</a>
		<?php endif; ?>
	</div>

	<div class="container welcome-section">
		<div class="text-center">
			<?php if ($is_logged_in): ?>
				<h1>Welcome back, <?php echo htmlspecialchars($customer_name); ?>!</h1>
				<p class="text-muted">You are logged in as a <?php echo strtolower($role_text); ?>.</p>
				<div class="mt-4">
					<?php if ($user_role == 1): ?>
						<div class="alert alert-info">
							<h5><i class="fa fa-shield-alt me-2"></i>Administrator Dashboard</h5>
							<p class="mb-0">You have administrator privileges. Use the menu above to manage categories and other system settings.</p>
						</div>
					<?php else: ?>
						<p>Your customer dashboard and e-commerce features will be available here in future labs.</p>
					<?php endif; ?>
				</div>
			<?php else: ?>
				<h1>Welcome to Bernard's E-Commerce Platform</h1>
				<p class="text-muted">Please register or login to access your account.</p>
			<?php endif; ?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	
	<?php if ($is_logged_in): ?>
	<script>
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
				// Show loading
				Swal.fire({
					title: 'Logging out...',
					allowOutsideClick: false,
					showConfirmButton: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});
				
				// Perform logout
				$.ajax({
					url: 'actions/logout_action.php',
					type: 'POST',
					dataType: 'json',
					success: function(response) {
						if (response.status === 'success') {
							Swal.fire({
								icon: 'success',
								title: 'Logged out successfully!',
								text: 'You have been logged out.',
								timer: 1500,
								showConfirmButton: false
							}).then(() => {
								window.location.reload();
							});
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Logout Failed',
								text: response.message
							});
						}
					},
					error: function() {
						Swal.fire({
							icon: 'error',
							title: 'System Error',
							text: 'An error occurred during logout.'
						});
					}
				});
			}
		});
	}
	</script>
	<?php endif; ?>
</body>
</html>