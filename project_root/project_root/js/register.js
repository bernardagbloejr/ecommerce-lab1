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

    // Real-time phone number validation
    $('#contact').on('blur', function() {
        const phone = $(this).val().trim();
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,15}$/;
        
        if (phone && !phoneRegex.test(phone)) {
            showFieldError('contact', 'Please enter a valid phone number (10-15 digits)');
        } else {
            clearFieldError('contact');
        }
    });

    // Real-time password validation
    $('#password').on('input', function() {
        const password = $(this).val();
        const minLength = password.length >= 6;
        const hasLower = /[a-z]/.test(password);
        const hasUpper = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        
        if (password && (!minLength || !hasLower || !hasUpper || !hasNumber)) {
            showFieldError('password', 'Password must be 6+ characters with uppercase, lowercase, and number');
        } else {
            clearFieldError('password');
        }
    });

    // Form submission handler
    $('#register-form').submit(function(e) {
        e.preventDefault();

        // Get form data
        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const country = $('#country').val().trim();
        const city = $('#city').val().trim();
        const contact = $('#contact').val().trim();
        const user_role = $('input[name="user_role"]:checked').val();

        // Validate required fields
        if (!name || !email || !password || !country || !city || !contact) {
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

        // Validate password strength
        if (password.length < 6 || !password.match(/[a-z]/) || !password.match(/[A-Z]/) || !password.match(/[0-9]/)) {
            Swal.fire({
                icon: 'error',
                title: 'Weak Password',
                text: 'Password must be at least 6 characters with uppercase, lowercase, and number!',
            });
            return;
        }

        // Validate phone number
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,15}$/;
        if (!phoneRegex.test(contact)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Phone',
                text: 'Please enter a valid phone number (10-15 digits)!',
            });
            return;
        }

        // Validate field lengths
        if (name.length > 100) {
            Swal.fire({
                icon: 'error',
                title: 'Name Too Long',
                text: 'Name must be 100 characters or less!',
            });
            return;
        }

        if (email.length > 50) {
            Swal.fire({
                icon: 'error',
                title: 'Email Too Long',
                text: 'Email must be 50 characters or less!',
            });
            return;
        }

        if (country.length > 30) {
            Swal.fire({
                icon: 'error',
                title: 'Country Name Too Long',
                text: 'Country must be 30 characters or less!',
            });
            return;
        }

        if (city.length > 30) {
            Swal.fire({
                icon: 'error',
                title: 'City Name Too Long',
                text: 'City must be 30 characters or less!',
            });
            return;
        }

        if (contact.length > 15) {
            Swal.fire({
                icon: 'error',
                title: 'Phone Number Too Long',
                text: 'Contact number must be 15 characters or less!',
            });
            return;
        }

        // Show loading state
        const submitBtn = $('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Registering...');

        // Submit form data
        $.ajax({
            url: '../actions/register_customer_action.php',
            type: 'POST',
            data: {
                name: name,
                email: email,
                password: password,
                country: country,
                city: city,
                contact: contact,
                user_role: user_role
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful!',
                        text: response.message,
                        showConfirmButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'login.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
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