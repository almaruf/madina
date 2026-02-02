// Admin Layout JS - Toast System and Common UI handlers

// Toast Notification System
const toast = {
    success: (message) => showToast(message, 'success'),
    error: (message) => showToast(message, 'error'),
    warning: (message) => showToast(message, 'warning'),
    info: (message) => showToast(message, 'info')
};

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const toastEl = document.createElement('div');
    toastEl.className = `toast ${type}`;
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    toastEl.innerHTML = `
        <i class="fas ${icons[type]} toast-icon"></i>
        <span class="flex-1">${message}</span>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(toastEl);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        removeToast(toastEl);
    }, 4000);
}

function removeToast(toastEl) {
    toastEl.classList.add('removing');
    setTimeout(() => {
        toastEl.remove();
    }, 300);
}

// Expose toast to window
window.toast = toast;

// Initialize layout handlers
document.addEventListener('DOMContentLoaded', () => {
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.getElementById('sidebar');
    
    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }

    // Close mobile menu when clicking nav links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 768 && sidebar) {
                sidebar.classList.remove('active');
            }
        });
    });

    // Logout button handler
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
            try {
                await axios.post('/api/admin/logout');
                localStorage.removeItem('auth_token');
                sessionStorage.removeItem('auth_token');
                window.location.href = '/admin/login';
            } catch (error) {
                localStorage.removeItem('auth_token');
                sessionStorage.removeItem('auth_token');
                window.location.href = '/admin/login';
            }
        });
    }
});
