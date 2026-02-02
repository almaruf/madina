import axios from 'axios';

// Configure axios with auth token from storage
const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');

if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';

// Request interceptor to ensure token is always sent
axios.interceptors.request.use(
    config => {
        const currentToken = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        if (currentToken) {
            config.headers.Authorization = `Bearer ${currentToken}`;
        }
        console.log('[Vite Axios] Making request to:', config.url, 'with auth:', !!config.headers.Authorization);
        return config;
    },
    error => {
        console.error('[Vite Axios] Request interceptor error:', error);
        return Promise.reject(error);
    }
);

// Response interceptor for error handling
axios.interceptors.response.use(
    response => response,
    error => {
        console.error('[Vite Axios] API Error:', error.response?.status, error.config?.url);
        
        // Handle 401 Unauthorized errors
        if (error.response?.status === 401) {
            console.warn('[Vite Axios] Token expired or invalid (401), redirecting to login...');
            localStorage.removeItem('auth_token');
            sessionStorage.removeItem('auth_token');
            
            // Only redirect if we're on an admin page
            if (window.location.pathname.startsWith('/admin') && !window.location.pathname.includes('/login')) {
                window.location.replace('/admin/login');
            }
        }
        
        // Handle 403 Forbidden errors
        if (error.response?.status === 403) {
            console.error('[Vite Axios] Access forbidden (403):', error);
        }
        
        return Promise.reject(error);
    }
);

window.axios = axios;
window.axiosConfigured = true;
window.axiosInterceptorsRegistered = true;

console.log('[Vite Axios] Configured with token:', token ? token.substring(0, 20) + '...' : 'none');
