// Category Create Page - Handle category creation
document.addEventListener('DOMContentLoaded', function() {
    console.log('Category Create Page: Initializing...');

    // Initialize
    loadParentCategories();
    setupSlugGeneration();
    setupFormSubmission();

    // Load parent categories for dropdown
    async function loadParentCategories() {
        try {
            const response = await axios.get('/api/admin/categories');
            const categories = response.data.data || response.data;
            
            const parentSelect = document.getElementById('parent_id');
            
            // Clear existing options except the first one (None)
            while (parentSelect.options.length > 1) {
                parentSelect.remove(1);
            }
            
            // Add categories
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                parentSelect.appendChild(option);
            });
            
            console.log('Parent categories loaded:', categories.length);
        } catch (error) {
            console.error('Failed to load categories:', error);
        }
    }

    // Auto-generate slug from name
    function setupSlugGeneration() {
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');

        nameInput.addEventListener('input', function() {
            const name = this.value;
            const slug = name
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        });
    }

    // Setup form submission
    function setupFormSubmission() {
        const form = document.getElementById('createForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validate required fields
            const name = document.getElementById('name').value.trim();
            const slug = document.getElementById('slug').value.trim();

            if (!name || !slug) {
                alert('Please fill in all required fields');
                return;
            }

            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';

            try {
                // Collect form data
                const categoryData = {
                    name: name,
                    slug: slug,
                    description: document.getElementById('description').value.trim() || null,
                    parent_id: document.getElementById('parent_id').value || null,
                    order: parseInt(document.getElementById('order').value) || 0,
                    is_active: document.getElementById('is_active').checked,
                    is_featured: document.getElementById('is_featured').checked,
                };

                console.log('Creating category:', categoryData);

                // Submit to API
                const response = await axios.post('/api/admin/categories', categoryData);
                
                console.log('Category created:', response.data);

                // Show success message
                alert('Category created successfully!');

                // Redirect to category detail page
                window.location.href = `/admin/categories/${response.data.slug}`;

            } catch (error) {
                console.error('Error creating category:', error);

                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Create Category';

                // Show error message
                if (error.response && error.response.data) {
                    const errors = error.response.data.errors;
                    if (errors) {
                        let errorMessage = 'Validation errors:\n\n';
                        Object.keys(errors).forEach(field => {
                            errorMessage += `${field}: ${errors[field].join(', ')}\n`;
                        });
                        alert(errorMessage);
                    } else {
                        alert(error.response.data.message || 'Failed to create category');
                    }
                } else {
                    alert('Failed to create category. Please try again.');
                }
            }
        });
    }

    console.log('Category Create Page: Ready');
});
