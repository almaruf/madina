// Admin Category Show Page
// Handles category details display and management

let currentCategory = null;

// Get category slug from the page
function getCategorySlug() {
    const slugElement = document.querySelector('[data-category-slug]');
    return slugElement ? slugElement.dataset.categorySlug : null;
}

// Load category data from API
async function loadCategory() {
    const categorySlug = getCategorySlug();
    if (!categorySlug) {
        console.error('Category slug not found');
        return;
    }

    try {
        const response = await axios.get(`/api/admin/categories/${categorySlug}`);
        currentCategory = response.data;
        renderCategory(currentCategory);
    } catch (error) {
        console.error('Error loading category:', error);
        document.getElementById('loading').classList.add('hidden');
        const errorDiv = document.getElementById('error');
        errorDiv.classList.remove('hidden');
        errorDiv.querySelector('p').textContent = error.response?.data?.message || 'Failed to load category details';
    }
}

// Render category details to the page
function renderCategory(category) {
    document.getElementById('loading').classList.add('hidden');
    document.getElementById('category-details').classList.remove('hidden');

    const isArchived = category.deleted_at !== null;

    // Header
    document.getElementById('category-header').innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">${category.name}</h2>
                ${isArchived ? '<span class="inline-block mt-2 px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">Archived</span>' : ''}
                ${category.is_active && !isArchived ? '<span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Active</span>' : ''}
                ${!category.is_active && !isArchived ? '<span class="inline-block mt-2 px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">Inactive</span>' : ''}
            </div>
        </div>
    `;

    // Info
    const hasImage = category.path !== null;
    
    document.getElementById('category-info').innerHTML = `
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Category Name</h3>
            <p class="text-lg text-gray-900">${category.name}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Slug</h3>
            <p class="text-lg text-gray-900">${category.slug}</p>
        </div>
        ${category.description ? `
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                <p class="text-gray-900">${category.description}</p>
            </div>
        ` : ''}
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Status</h3>
            <p class="text-gray-900">${category.is_active ? 'Active' : 'Inactive'}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Created</h3>
            <p class="text-gray-900">${new Date(category.created_at).toLocaleString()}</p>
        </div>
        ${!isArchived ? `
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Category Image</h3>
                
                ${hasImage ? `
                    <div class="relative inline-block group">
                        <img src="${category.signed_url || category.url}" alt="${category.name}" class="w-48 h-48 object-cover rounded-lg border-2 border-gray-200">
                        <!-- Hover overlay with delete button -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                            <button id="delete-image-btn" class="bg-red-600 hover:bg-red-700 text-white p-3 rounded-lg">
                                <i class="fas fa-trash mr-2"></i>Delete
                            </button>
                        </div>
                    </div>
                ` : `
                    <div class="flex flex-col gap-3">
                        <input type="file" id="image-upload" accept="image/jpeg,image/png,image/webp" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button id="upload-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold w-fit">
                            <i class="fas fa-upload mr-2"></i>Upload Image
                        </button>
                        <div id="upload-progress" class="hidden mt-2">
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div id="progress-bar" class="bg-blue-600 h-2 transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p id="progress-text" class="text-sm text-gray-600 mt-1">Uploading...</p>
                        </div>
                        <div id="validation-errors" class="hidden mt-2 bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-sm"></div>
                    </div>
                `}
            </div>
        ` : ''}
    `;

    // Attach image event listeners if not archived
    if (!isArchived) {
        if (hasImage) {
            const deleteBtn = document.getElementById('delete-image-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', deleteImage);
            }
        } else {
            const uploadBtn = document.getElementById('upload-btn');
            if (uploadBtn) {
                uploadBtn.addEventListener('click', handleImageUpload);
            }
        }
    }

    // Products section
    const productsCount = category.products_count || 0;
    const products = category.products || [];
    
    let productsHtml = `
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Products</h3>
                <p class="text-sm text-gray-600">${productsCount} product${productsCount !== 1 ? 's' : ''} in this category</p>
            </div>
            <a href="/admin/products/create" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 inline-flex items-center gap-2">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
    `;
    
    if (products.length > 0) {
        productsHtml += `
            <div class="space-y-2">
                ${products.map(product => {
                    const defaultVariation = product.variations?.find(v => v.is_default) || product.variations?.[0];
                    const price = defaultVariation ? `£${parseFloat(defaultVariation.price).toFixed(2)}` : 'N/A';
                    const stock = defaultVariation ? defaultVariation.stock_quantity : 0;
                    const imageUrl = product.primary_image?.url;
                    
                    return `
                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer" onclick="window.location='/admin/products/${product.slug}'">
                            <div class="flex-shrink-0">
                                ${imageUrl 
                                    ? `<img src="${imageUrl}" class="w-16 h-16 object-cover rounded" alt="${product.name}">`
                                    : '<div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'
                                }
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">${product.name}</p>
                                <p class="text-sm text-gray-500">Stock: ${stock}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <p class="font-semibold text-gray-900">${price}</p>
                                <span class="inline-block px-2 py-1 text-xs rounded ${product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${product.is_active ? 'Active' : 'Inactive'}
                                </span>
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    } else {
        productsHtml += `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-box-open text-4xl mb-2"></i>
                <p>No products in this category yet</p>
            </div>
        `;
    }
    
    document.getElementById('products-section').innerHTML = productsHtml;

    // Actions
    const actionsHtml = [];
    
    if (!isArchived) {
        actionsHtml.push(`
            <a href="/admin/categories/${category.slug}/edit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Edit Category
            </a>
            <button id="archive-btn" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                Archive Category
            </button>
        `);
    } else {
        actionsHtml.push(`
            <button id="restore-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Restore Category
            </button>
            <button id="permanent-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Permanently Delete
            </button>
        `);
    }

    document.getElementById('category-actions').innerHTML = actionsHtml.join('');

    // Attach event listeners
    attachActionListeners(isArchived);
}

// Attach event listeners to action buttons
function attachActionListeners(isArchived) {
    if (!isArchived) {
        const archiveBtn = document.getElementById('archive-btn');
        if (archiveBtn) {
            archiveBtn.addEventListener('click', confirmArchiveCategory);
        }
    } else {
        const restoreBtn = document.getElementById('restore-btn');
        const permanentDeleteBtn = document.getElementById('permanent-delete-btn');
        
        if (restoreBtn) {
            restoreBtn.addEventListener('click', restoreCategory);
        }
        
        if (permanentDeleteBtn) {
            permanentDeleteBtn.addEventListener('click', confirmPermanentDeleteCategory);
        }
    }
}

// Confirm archive category (requires second click)
function confirmArchiveCategory(event) {
    window.toast.warning('Click Archive again to confirm', 3000);
    const btn = event.target;
    btn.textContent = 'Confirm Archive';
    
    btn.removeEventListener('click', confirmArchiveCategory);
    btn.addEventListener('click', archiveCategory, { once: true });
    
    setTimeout(() => {
        btn.textContent = 'Archive';
        btn.removeEventListener('click', archiveCategory);
        btn.addEventListener('click', confirmArchiveCategory);
    }, 3000);
}

// Archive the category
async function archiveCategory() {
    const categorySlug = getCategorySlug();
    if (!categorySlug) return;

    try {
        await axios.delete(`/api/admin/categories/${categorySlug}`);
        window.toast.success('Category archived successfully!');
        setTimeout(() => window.location.reload(), 1000);
    } catch (error) {
        console.error('Error archiving category:', error);
        window.toast.error(error.response?.data?.message || 'Failed to archive category');
    }
}

// Restore the category
async function restoreCategory() {
    const categorySlug = getCategorySlug();
    if (!categorySlug) return;

    try {
        await axios.post(`/api/admin/categories/${categorySlug}/restore`);
        window.toast.success('Category restored successfully!');
        setTimeout(() => window.location.reload(), 1000);
    } catch (error) {
        console.error('Error restoring category:', error);
        window.toast.error(error.response?.data?.message || 'Failed to restore category');
    }
}

// Confirm permanent delete (requires second click)
function confirmPermanentDeleteCategory(event) {
    window.toast.warning('Click Delete again to PERMANENTLY delete', 3000);
    const btn = event.target;
    btn.textContent = 'Confirm Delete';
    
    btn.removeEventListener('click', confirmPermanentDeleteCategory);
    btn.addEventListener('click', permanentlyDeleteCategory, { once: true });
    
    setTimeout(() => {
        btn.textContent = 'Permanent Delete';
        btn.removeEventListener('click', permanentlyDeleteCategory);
        btn.addEventListener('click', confirmPermanentDeleteCategory);
    }, 3000);
}

// Permanently delete the category
async function permanentlyDeleteCategory() {
    const categorySlug = getCategorySlug();
    if (!categorySlug) return;

    try {
        await axios.delete(`/api/admin/categories/${categorySlug}/force`);
        window.toast.success('Category permanently deleted!');
        setTimeout(() => window.location.href = '/admin/categories', 1000);
    } catch (error) {
        console.error('Error deleting category:', error);
        window.toast.error(error.response?.data?.message || 'Failed to delete category');
    }
}

// Handle image upload
async function handleImageUpload() {
    const fileInput = document.getElementById('image-upload');
    const file = fileInput.files[0];
    
    if (!file) {
        window.toast.error('Please select an image');
        return;
    }
    
    // Validate file
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const errorsDiv = document.getElementById('validation-errors');
    
    if (!allowedTypes.includes(file.type)) {
        errorsDiv.innerHTML = '<p>Invalid file type. Only JPEG, PNG, and WebP are allowed.</p>';
        errorsDiv.classList.remove('hidden');
        return;
    }
    
    if (file.size > maxSize) {
        errorsDiv.innerHTML = '<p>File too large. Maximum size is 5MB.</p>';
        errorsDiv.classList.remove('hidden');
        return;
    }
    
    errorsDiv.classList.add('hidden');
    
    // Prepare form data
    const formData = new FormData();
    formData.append('image', file);
    
    // Show progress
    const progressDiv = document.getElementById('upload-progress');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    progressDiv.classList.remove('hidden');
    
    try {
        const response = await new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressBar.style.width = percentComplete + '%';
                    progressText.textContent = `Uploading... ${Math.round(percentComplete)}%`;
                }
            });
            
            xhr.addEventListener('load', () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve(JSON.parse(xhr.responseText));
                } else {
                    reject(JSON.parse(xhr.responseText));
                }
            });
            
            xhr.addEventListener('error', () => reject({ message: 'Upload failed' }));
            
            xhr.open('POST', `/api/admin/categories/${currentCategory.slug}/image`);
            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            if (token) {
                xhr.setRequestHeader('Authorization', `Bearer ${token}`);
            }
            xhr.send(formData);
        });
        
        progressDiv.classList.add('hidden');
        progressBar.style.width = '0%';
        fileInput.value = '';
        
        window.toast.success('Image uploaded successfully!');
        await loadCategory();
        
    } catch (error) {
        console.error('Upload error:', error);
        progressDiv.classList.add('hidden');
        window.toast.error(error.message || 'Failed to upload image');
        
        if (error.errors) {
            const errorMessages = Object.values(error.errors).flat();
            errorsDiv.innerHTML = errorMessages.map(err => `<p>• ${err}</p>`).join('');
            errorsDiv.classList.remove('hidden');
        }
    }
}

// Delete image
async function deleteImage() {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }
    
    try {
        await axios.delete(`/api/admin/categories/${currentCategory.slug}/image`);
        window.toast.success('Image deleted successfully!');
        await loadCategory();
    } catch (error) {
        console.error('Delete error:', error);
        window.toast.error(error.response?.data?.message || 'Failed to delete image');
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    loadCategory();
});
