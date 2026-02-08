// Login Form Handler
handleFormSubmit('loginForm');

// Register Form Handler
handleFormSubmit('registerForm');

// Password Validation for Register
const registerForm = document.getElementById('registerForm');

if (registerForm) {
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    
    passwordConfirmation.addEventListener('input', () => {
        if (password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity('Passwords do not match');
        } else {
            passwordConfirmation.setCustomValidity('');
        }
    });
    
    password.addEventListener('input', () => {
        if (passwordConfirmation.value !== '') {
            if (password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('Passwords do not match');
            } else {
                passwordConfirmation.setCustomValidity('');
            }
        }
    });
}

// Auto-fill demo credentials (optional - for testing)
const loginForm = document.getElementById('loginForm');

if (loginForm) {
    const demoCredentials = document.querySelectorAll('.demo-credentials p');
    
    demoCredentials.forEach(p => {
        p.style.cursor = 'pointer';
        p.addEventListener('click', () => {
            const text = p.textContent;
            
            if (text.includes('admin@foodorder.com')) {
                document.getElementById('email').value = 'admin@foodorder.com';
                document.getElementById('password').value = 'password123';
            } else if (text.includes('customer@example.com')) {
                document.getElementById('email').value = 'customer@example.com';
                document.getElementById('password').value = 'password123';
            }
        });
    });
}