// Admin Login Page JS

let otpSent = false;

function showError(message) {
    const errorDiv = document.getElementById('error-message');
    errorDiv.textContent = message;
    errorDiv.classList.remove('hidden');
    errorDiv.classList.remove('bg-green-50', 'border-green-200', 'text-green-700');
    errorDiv.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
}

function showSuccess(message) {
    const errorDiv = document.getElementById('error-message');
    errorDiv.textContent = message;
    errorDiv.classList.remove('hidden');
    errorDiv.classList.add('bg-green-50', 'border-green-200', 'text-green-700');
    errorDiv.classList.remove('bg-red-50', 'border-red-200', 'text-red-700');
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
    // Check if user is already logged in
    try {
        const response = await axios.get('/api/auth/user');
        if (response.data) {
            window.location.href = '/admin';
        }
    } catch (error) {
        // User not logged in, stay on login page
    }
    
    // Login form submission
    document.getElementById('login-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const phone = document.getElementById('phone').value;
        const otp = document.getElementById('otp').value;
        const errorDiv = document.getElementById('error-message');
        const loadingDiv = document.getElementById('loading-message');
        const loadingText = document.getElementById('loading-text');
        const submitBtn = document.getElementById('submit-btn');

        // Reset errors
        errorDiv.classList.add('hidden');

        // Hide OTP section by default
        if (!otpSent) {
            // Send OTP phase
            if (!phone) {
                showError('Please enter your phone number');
                return;
            }

            loadingDiv.classList.remove('hidden');
            loadingText.textContent = 'Sending OTP...';
            submitBtn.disabled = true;

            try {
                const prefix = document.getElementById('phone-prefix').value;
                const fullPhone = prefix + phone;
                const response = await axios.post('/api/admin/login', { phone: fullPhone });
                loadingDiv.classList.add('hidden');
                submitBtn.disabled = false;
                
                otpSent = true;
                document.getElementById('otp-section').classList.remove('hidden');
                submitBtn.textContent = 'Verify & Login';
                showSuccess('OTP sent! Check your SMS.');
            } catch (error) {
                loadingDiv.classList.add('hidden');
                submitBtn.disabled = false;
                showError(error.response?.data?.message || 'Failed to send OTP');
            }
        } else {
            // Verify OTP phase
            if (!otp || otp.length !== 6) {
                showError('Please enter a valid 6-digit OTP');
                return;
            }

            loadingDiv.classList.remove('hidden');
            loadingText.textContent = 'Verifying OTP...';
            submitBtn.disabled = true;

            try {
                const prefix = document.getElementById('phone-prefix').value;
                const fullPhone = prefix + phone;
                const response = await axios.post('/api/admin/verify-otp', { phone: fullPhone, otp });

                loadingDiv.classList.add('hidden');
                
                // Store the token in localStorage
                const token = response.data.token;
                console.log('Token received:', token);
                localStorage.setItem('auth_token', token);
                console.log('Token stored in localStorage:', localStorage.getItem('auth_token'));
                
                // Also store in session storage as backup
                sessionStorage.setItem('auth_token', token);
                
                showSuccess('Login successful! Redirecting...');
                
                setTimeout(() => {
                    console.log('Redirecting to /admin...');
                    window.location.href = '/admin';
                }, 500);
            } catch (error) {
                loadingDiv.classList.add('hidden');
                submitBtn.disabled = false;
                console.error('OTP verification error:', error);
                showError(error.response?.data?.message || 'Failed to verify OTP');
            }
        }
    });
});
