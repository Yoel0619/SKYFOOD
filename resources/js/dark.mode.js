// Dark Mode Toggle
document.addEventListener('DOMContentLoaded', function() {
    // Create dark mode toggle button
    const darkModeToggle = document.createElement('div');
    darkModeToggle.className = 'dark-mode-toggle';
    darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    document.body.appendChild(darkModeToggle);
    
    // Check for saved dark mode preference
    const darkMode = localStorage.getItem('darkMode');
    if (darkMode === 'enabled') {
        enableDarkMode();
    }
    
    // Toggle dark mode
    darkModeToggle.addEventListener('click', function() {
        const darkMode = localStorage.getItem('darkMode');
        
        if (darkMode !== 'enabled') {
            enableDarkMode();
        } else {
            disableDarkMode();
        }
    });
    
    function enableDarkMode() {
        document.body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'enabled');
        darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        
        // Add animation
        darkModeToggle.style.transform = 'scale(1.2) rotate(360deg)';
        setTimeout(() => {
            darkModeToggle.style.transform = 'scale(1) rotate(0deg)';
        }, 300);
    }
    
    function disableDarkMode() {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', null);
        darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        
        // Add animation
        darkModeToggle.style.transform = 'scale(1.2) rotate(-360deg)';
        setTimeout(() => {
            darkModeToggle.style.transform = 'scale(1) rotate(0deg)';
        }, 300);
    }
});

// Add animation classes on scroll
window.addEventListener('scroll', function() {
    const elements = document.querySelectorAll('.card, .stat-card, .product-card');
    
    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (elementTop < windowHeight - 100) {
            element.classList.add('fade-in-up');
        }
    });
});

// Add stagger animation to list items
document.addEventListener('DOMContentLoaded', function() {
    const listItems = document.querySelectorAll('table tr, .product-card');
    
    listItems.forEach((item, index) => {
        item.classList.add('stagger-item', 'fade-in-up');
        item.style.animationDelay = `${index * 0.1}s`;
    });
});