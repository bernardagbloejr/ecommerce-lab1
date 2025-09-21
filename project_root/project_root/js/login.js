$(document).ready(function() {
    // Real-time email validation
    $('#email').on('blur', function() {
        const email = $(this).val().trim();
        const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        
        if (email && !emailRegex.test(email)) {
            showFieldError('email', 'Please enter a valid email address');
        } else {
            clearFieldError('email');
        }
    });

    // Form submission handler
    $('#login-form').submit(function(e) {
        e.preventDefault();

        // Get form data
        const email = $('#email').val().trim();
        const password = $('#password').val();

        // Validate required fields
        if (!email || !password) {
            Swal.fire({
                icon: 'error',
                title: 'Missing Information',
                text: 'Please fill in all required fields!',
            });
            return;
        }

        // Validate email format
        const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailRegex.test(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address!',
            });
            return;
        }

        // Basic password validation (not empty)
        if (password.length < 1) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Password',
                text: 'Password cannot be empty!',
            });
            return;
        }

        // Show loading state
        const submitBtn = $('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Logging in...');

        // Submit login data
        $.ajax({
            url: '../actions/login_customer_action.php',
            type: 'POST',
            data: {
                email: email,
                password: password
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Successful!',
                        text: 'Welcome back, ' + response.customer_name + '!',
                        showConfirmButton: true,
                        timer: 2000
                    }).then((result) => {
                        // Redirect to index page after successful login
                        window.location.href = '../index.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'An error occurred! Please check the console and try again.',
                });
            },
            complete: function() {
                // Restore button state
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Helper functions for field validation feedback
    function showFieldError(fieldId, message) {
        const field = $('#' + fieldId);
        field.addClass('is-invalid');
        
        // Remove existing error message
        field.siblings('.invalid-feedback').remove();
        
        // Add new error message
        field.after('<div class="invalid-feedback">' + message + '</div>');
    }

    function clearFieldError(fieldId) {
        const field = $('#' + fieldId);
        field.removeClass('is-invalid');
        field.siblings('.invalid-feedback').remove();
    }
});