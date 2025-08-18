// Form validation and interaction functions

// Toggle password visibility
function togglePassword(fieldId = 'password') {
    const passwordField = document.getElementById(fieldId);
    const toggleIcon = passwordField.nextElementSibling;
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Close login modal
function closeLogin() {
    if (confirm('Are you sure you want to close the login form?')) {
        window.location.href = 'index.html';
    }
}

// Close register modal
function closeRegister() {
    if (confirm('Are you sure you want to close the registration form?')) {
        window.location.href = 'index.html';
    }
}

// Show register page
function showRegister() {
    window.location.href = 'register.html';
}

// Form validation functions
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

function validatePassword(password) {
    return password.length >= 6;
}

// Show message function
function showMessage(message, type = 'error') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    
    const form = document.querySelector('form');
    if (form) {
        form.insertBefore(messageDiv, form.firstChild);
        
        // Remove message after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.parentNode.removeChild(messageDiv);
            }
        }, 5000);
    }
}

// Remove existing messages
function removeMessages() {
    const messages = document.querySelectorAll('.message');
    messages.forEach(message => {
        if (message.parentNode) {
            message.parentNode.removeChild(message);
        }
    });
}

// Login form validation and submission
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            removeMessages();
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            // Basic validation
            if (!email) {
                showMessage('Please enter your email or profile ID');
                return;
            }
            
            if (!password) {
                showMessage('Please enter your password');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Logging in...';
            submitBtn.classList.add('loading');
            
            // Submit form via AJAX
            const formData = new FormData(this);
            
            fetch('php/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || 'dashboard.html';
                    }, 1500);
                } else {
                    showMessage(data.message || 'Login failed. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred. Please try again.');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.classList.remove('loading');
            });
        });
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            removeMessages();
            
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const gender = document.getElementById('gender').value;
            const dateOfBirth = document.getElementById('dateOfBirth').value;
            const religion = document.getElementById('religion').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const terms = document.getElementById('terms').checked;
            
            // Validation
            if (!firstName || !lastName) {
                showMessage('Please enter your full name');
                return;
            }
            
            if (!validateEmail(email)) {
                showMessage('Please enter a valid email address');
                return;
            }
            
            if (!validatePhone(phone)) {
                showMessage('Please enter a valid phone number');
                return;
            }
            
            if (!gender) {
                showMessage('Please select your gender');
                return;
            }
            
            if (!dateOfBirth) {
                showMessage('Please enter your date of birth');
                return;
            }
            
            // Check age (minimum 18 years)
            const birthDate = new Date(dateOfBirth);
            const today = new Date();
            const age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (age < 18 || (age === 18 && monthDiff < 0) || 
                (age === 18 && monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                showMessage('You must be at least 18 years old to register');
                return;
            }
            
            if (!religion) {
                showMessage('Please select your religion');
                return;
            }
            
            if (!validatePassword(password)) {
                showMessage('Password must be at least 6 characters long');
                return;
            }
            
            if (password !== confirmPassword) {
                showMessage('Passwords do not match');
                return;
            }
            
            if (!terms) {
                showMessage('Please accept the terms and conditions');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Creating Account...';
            submitBtn.classList.add('loading');
            
            // Submit form via AJAX
            const formData = new FormData(this);
            
            fetch('php/register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Registration successful! Redirecting to complete your profile...', 'success');
                    setTimeout(() => {
                        window.location.href = 'profile-form.html';
                    }, 1500);
                } else {
                    showMessage(data.message || 'Registration failed. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred. Please try again.');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.classList.remove('loading');
            });
        });
    }
});

// Real-time validation feedback
document.addEventListener('DOMContentLoaded', function() {
    // Email validation feedback
    const emailField = document.getElementById('email');
    if (emailField && emailField.type === 'email') {
        emailField.addEventListener('blur', function() {
            if (this.value && !validateEmail(this.value)) {
                this.style.borderColor = '#f44336';
            } else {
                this.style.borderColor = '#e0e0e0';
            }
        });
    }
    
    // Phone validation feedback
    const phoneField = document.getElementById('phone');
    if (phoneField) {
        phoneField.addEventListener('blur', function() {
            if (this.value && !validatePhone(this.value)) {
                this.style.borderColor = '#f44336';
            } else {
                this.style.borderColor = '#e0e0e0';
            }
        });
    }
    
    // Password strength indicator
    const passwordField = document.getElementById('password');
    if (passwordField) {
        passwordField.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;
            
            // Visual feedback could be added here
            if (password.length > 0 && password.length < 6) {
                this.style.borderColor = '#f44336';
            } else if (strength >= 2) {
                this.style.borderColor = '#8BC34A';
            } else {
                this.style.borderColor = '#e0e0e0';
            }
        });
    }
    
    // Confirm password validation
    const confirmPasswordField = document.getElementById('confirmPassword');
    if (confirmPasswordField) {
        confirmPasswordField.addEventListener('blur', function() {
            const password = document.getElementById('password').value;
            if (this.value && this.value !== password) {
                this.style.borderColor = '#f44336';
            } else {
                this.style.borderColor = '#e0e0e0';
            }
        });
    }
});
