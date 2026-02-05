// Admin Product Show Page
// Handles product details display and management

let productData = null;

// Get product slug from the page
function getProductSlug() {
    const slugElement = document.querySelector('[data-product-slug]');
    return slugElement ? slugElement.dataset.productSlug : null;
}

// Load product data from API
async function loadProduct() {
    const productSlug = getProductSlug();
    if (!productSlug) {
        console.error('Product slug not found');
        return;
    }

    try {
        const response = await axios.get(`/api/admin/products/${productSlug}`);
        productData = response.data.data || response.data;
        renderProduct();
    } catch (error) {
        console.error('Error loading product:', error);
        const message = error.response?.data?.message || 'Failed to load product details';
        document.getElementById('product-container').innerHTML = `
            <div class="bg-red-50 border border-red-200 text-red-700 p-6 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>
                ${message}
            </div>
        `;
    }
}

// Render product details to the page
function renderProduct() {
    const price = productData.variations?.[0]?.price ? parseFloat(productData.variations[0].price).toFixed(2) : '0.00';
    const stock = productData.variations?.[0]?.stock_quantity || 0;
    const categories = productData.categories?.map(c => c.name).join(', ') || 'None';
    const isArchived = productData.deleted_at !== null;
    const productSlug = getProductSlug();
    const imageCount = productData.images?.length || 0;
    const remainingSlots = 5 - imageCount;
    
    document.getElementById('product-container').innerHTML = `
        ${isArchived ? '<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg mb-6"><i class="fas fa-exclamation-triangle mr-2"></i>This product is archived</div>' : ''}
        
        <!-- Product Details and Images -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left: Product Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-2xl font-bold mb-4">${productData.name}</h3>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-600">Price</p>
                        <p class="text-lg font-semibold text-green-600">£${price}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Stock</p>
                        <p class="text-lg font-semibold">${stock} units</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Type</p>
                        <p class="text-lg font-semibold capitalize">${productData.type}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="px-3 py-1 text-sm rounded ${productData.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${productData.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-1">Categories</p>
                    <p class="text-base">${categories}</p>
                </div>
                
                ${productData.description ? `
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-1">Description</p>
                        <p class="text-base">${productData.description}</p>
                    </div>
                ` : ''}
                
                <!-- Variations -->
                ${productData.variations && productData.variations.length > 0 ? `
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-lg font-bold mb-4">Variations</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    ${productData.variations.map(v => `
                                        <tr>
                                            <td class="px-4 py-3 text-sm">${v.name}</td>
                                            <td class="px-4 py-3 text-sm font-semibold">£${parseFloat(v.price).toFixed(2)}</td>
                                            <td class="px-4 py-3 text-sm">${v.stock_quantity || 0}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500">${v.sku || 'N/A'}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                ` : ''}
            </div>

            <!-- Right: Product Images Section -->
            ${!isArchived ? `
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Product Images (${imageCount}/5)</h3>
                    
                    <!-- Upload Section -->
                    <div class="mb-6">
                        <div class="flex flex-col gap-3">
                            <input type="file" id="image-upload" accept="image/jpeg,image/png,image/webp" multiple class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" ${imageCount >= 5 ? 'disabled' : ''}>
                            <button id="upload-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold ${imageCount >= 5 ? 'opacity-50 cursor-not-allowed' : ''}" ${imageCount >= 5 ? 'disabled' : ''}>
                                <i class="fas fa-upload mr-2"></i>Upload Images ${remainingSlots > 0 ? `(${remainingSlots} remaining)` : '(Maximum reached)'}
                            </button>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div id="upload-progress" class="hidden mt-3">
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div id="progress-bar" class="bg-blue-600 h-2 transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p id="progress-text" class="text-sm text-gray-600 mt-1">Uploading...</p>
                        </div>
                        
                        <!-- Validation Errors -->
                        <div id="validation-errors" class="hidden mt-3 bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-sm"></div>
                    </div>
                    
                    <!-- Image Gallery -->
                    <div id="image-gallery" class="grid grid-cols-3 gap-3">
                        ${productData.images && productData.images.length > 0 ? productData.images.map(img => `
                            <div class="relative group cursor-move" draggable="true" data-image-id="${img.id}" data-image-order="${img.order}">
                                <img src="${img.signed_thumbnail_url || img.signed_url || img.thumbnail_url || img.url}" alt="${img.alt_text || productData.name}" class="w-full h-[100px] object-cover rounded-lg border-2 ${img.is_primary ? 'border-blue-500' : 'border-gray-200'}">
                                
                                <!-- Primary Badge -->
                                ${img.is_primary ? '<div class="absolute top-1 left-1 bg-blue-600 text-white text-xs px-2 py-0.5 rounded">Primary</div>' : ''}
                                
                                <!-- Hover Controls -->
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center gap-2">
                                    ${!img.is_primary ? `<button class="set-primary-btn bg-blue-600 hover:bg-blue-700 text-white p-2 rounded" data-image-id="${img.id}" title="Set as Primary">
                                        <i class="fas fa-star"></i>
                                    </button>` : ''}
                                    <button class="delete-image-btn bg-red-600 hover:bg-red-700 text-white p-2 rounded" data-image-id="${img.id}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `).join('') : '<p class="text-gray-500 text-center col-span-full py-8">No images uploaded yet</p>'}
                    </div>
                </div>
            ` : ''}
        </div>
        
        <!-- Additional Product Information -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-xl font-bold mb-6">Additional Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                ${productData.short_description ? `
                    <div class="md:col-span-2">
                        <p class="text-sm font-semibold text-gray-600 mb-1">Short Description</p>
                        <p class="text-base text-gray-900">${productData.short_description}</p>
                    </div>
                ` : ''}
                
                ${productData.brand ? `
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Brand</p>
                        <p class="text-base text-gray-900">${productData.brand}</p>
                    </div>
                ` : ''}
                
                ${productData.country_of_origin ? `
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Country of Origin</p>
                        <p class="text-base text-gray-900">${productData.country_of_origin}</p>
                    </div>
                ` : ''}
                
                ${productData.type === 'meat' ? `
                    ${productData.meat_type ? `
                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-1">Meat Type</p>
                            <p class="text-base text-gray-900 capitalize">${productData.meat_type}</p>
                        </div>
                    ` : ''}
                    
                    ${productData.cut_type ? `
                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-1">Cut Type</p>
                            <p class="text-base text-gray-900">${productData.cut_type}</p>
                        </div>
                    ` : ''}
                    
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Halal Certified</p>
                        <span class="inline-block px-3 py-1 text-sm rounded ${productData.is_halal ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                            ${productData.is_halal ? 'Yes' : 'No'}
                        </span>
                    </div>
                ` : ''}
                
                <div class="md:col-span-2">
                    <p class="text-sm font-semibold text-gray-600 mb-1">Ingredients</p>
                    <p class="text-base text-gray-900">${productData.ingredients || '<span class="text-gray-400">Not specified</span>'}</p>
                </div>
                
                <div class="md:col-span-2">
                    <p class="text-sm font-semibold text-gray-600 mb-1">Allergen Information</p>
                    <p class="text-base text-gray-900 bg-yellow-50 border border-yellow-200 p-3 rounded">${productData.allergen_info || '<span class="text-gray-400">Not specified</span>'}</p>
                </div>
                
                <div class="md:col-span-2">
                    <p class="text-sm font-semibold text-gray-600 mb-1">Storage Instructions</p>
                    <p class="text-base text-gray-900">${productData.storage_instructions || '<span class="text-gray-400">Not specified</span>'}</p>
                </div>
                
                <!-- Status Badges -->
                <div class="md:col-span-2">
                    <p class="text-sm font-semibold text-gray-600 mb-2">Product Flags</p>
                    <div class="flex flex-wrap gap-2">
                        ${productData.is_featured ? '<span class="px-3 py-1 text-sm rounded bg-yellow-100 text-yellow-800"><i class="fas fa-star mr-1"></i>Featured</span>' : ''}
                        ${productData.is_on_sale ? '<span class="px-3 py-1 text-sm rounded bg-red-100 text-red-800"><i class="fas fa-tag mr-1"></i>On Sale</span>' : ''}
                        ${!productData.is_featured && !productData.is_on_sale ? '<span class="text-gray-500 text-sm">No special flags</span>' : ''}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons (Bottom) -->
        <div class="flex gap-3 mt-6">
            ${!isArchived ? `
                <a href="/admin/products/${productSlug}/edit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i>Edit Product
                </a>
                <button id="archive-btn" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold">
                    <i class="fas fa-archive mr-2"></i>Archive
                </button>
            ` : `
                <button id="restore-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
                    <i class="fas fa-undo mr-2"></i>Restore
                </button>
                <button id="permanent-delete-btn" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold">
                    <i class="fas fa-trash mr-2"></i>Permanent Delete
                </button>
            `}
        </div>
    `;

    // Attach event listeners after rendering
    attachEventListeners();
    
    // Attach drag and drop listeners for image reordering
    if (!isArchived) {
        attachDragAndDropListeners();
        attachImageControlListeners();
    }
}

// Attach event listeners to dynamically created elements
function attachEventListeners() {
    const isArchived = productData.deleted_at !== null;

    if (!isArchived) {
        const archiveBtn = document.getElementById('archive-btn');
        if (archiveBtn) {
            archiveBtn.addEventListener('click', confirmArchive);
        }
    } else {
        const restoreBtn = document.getElementById('restore-btn');
        const permanentDeleteBtn = document.getElementById('permanent-delete-btn');
        
        if (restoreBtn) {
            restoreBtn.addEventListener('click', restoreProduct);
        }
        
        if (permanentDeleteBtn) {
            permanentDeleteBtn.addEventListener('click', confirmPermanentDelete);
        }
    }
}

// Confirm archive (requires second click)
function confirmArchive(event) {
    window.toast.warning('Click Archive again to confirm', 3000);
    const btn = event.target.closest('button');
    btn.innerHTML = '<i class="fas fa-check mr-2"></i>Confirm Archive';
    
    btn.removeEventListener('click', confirmArchive);
    btn.addEventListener('click', archiveProduct, { once: true });
    
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-archive mr-2"></i>Archive';
        btn.removeEventListener('click', archiveProduct);
        btn.addEventListener('click', confirmArchive);
    }, 3000);
}

// Archive the product
async function archiveProduct() {
    const productSlug = getProductSlug();
    if (!productSlug) return;

    try {
        await axios.delete(`/api/admin/products/${productSlug}`);
        window.toast.success('Product archived successfully');
        setTimeout(() => window.location.href = '/admin/products', 1500);
    } catch (error) {
        console.error('Error archiving product:', error);
        const message = error.response?.data?.message || 'Failed to archive product';
        window.toast.error(message);
    }
}

// Restore the product
async function restoreProduct() {
    const productSlug = getProductSlug();
    if (!productSlug) return;

    try {
        await axios.post(`/api/admin/products/${productSlug}/restore`);
        window.toast.success('Product restored successfully');
        loadProduct();
    } catch (error) {
        console.error('Error restoring product:', error);
        const message = error.response?.data?.message || 'Failed to restore product';
        window.toast.error(message);
    }
}

// Confirm permanent delete (requires second click)
function confirmPermanentDelete(event) {
    window.toast.warning('Click Delete again to permanently delete', 3000);
    const btn = event.target.closest('button');
    btn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete';
    
    btn.removeEventListener('click', confirmPermanentDelete);
    btn.addEventListener('click', permanentDelete, { once: true });
    
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-trash mr-2"></i>Permanent Delete';
        btn.removeEventListener('click', permanentDelete);
        btn.addEventListener('click', confirmPermanentDelete);
    }, 3000);
}

// Permanently delete the product
async function permanentDelete() {
    const productSlug = getProductSlug();
    if (!productSlug) return;

    try {
        await axios.delete(`/api/admin/products/${productSlug}/force`);
        window.toast.success('Product permanently deleted');
        setTimeout(() => window.location.href = '/admin/products', 1500);
    } catch (error) {
        console.error('Error deleting product:', error);
        const message = error.response?.data?.message || 'Failed to delete product';
        window.toast.error(message);
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    loadProduct();
});

// ============ IMAGE UPLOAD FUNCTIONALITY ============

// Attach image control listeners (upload, delete, set primary)
function attachImageControlListeners() {
    const uploadBtn = document.getElementById('upload-btn');
    const imageUpload = document.getElementById('image-upload');
    
    if (uploadBtn) {
        uploadBtn.addEventListener('click', handleImageUpload);
    }
    
    // Delete image buttons
    document.querySelectorAll('.delete-image-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const imageId = btn.dataset.imageId;
            showDeleteConfirmModal(imageId);
        });
    });
    
    // Set primary buttons
    document.querySelectorAll('.set-primary-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const imageId = btn.dataset.imageId;
            setPrimaryImage(imageId);
        });
    });
}

// Validate image files before upload
function validateImageFiles(files) {
    const errors = [];
    const validFiles = [];
    const maxSize = 5 * 1024 * 1024; // 5MB
    const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const currentCount = productData.images?.length || 0;
    
    // Check total count
    if (currentCount >= 5) {
        errors.push('Maximum 5 images per product. Delete existing images first.');
        return { validFiles: [], errors };
    }
    
    // Check if adding these would exceed limit
    if (currentCount + files.length > 5) {
        errors.push(`Can only upload ${5 - currentCount} more image(s). You selected ${files.length}.`);
        return { validFiles: [], errors };
    }
    
    // Check batch limit
    if (files.length > 5) {
        errors.push('Maximum 5 images per upload batch.');
        return { validFiles: [], errors };
    }
    
    // Validate each file
    for (let file of files) {
        if (!validTypes.includes(file.type)) {
            errors.push(`${file.name}: Invalid format. Only JPEG, PNG, and WebP allowed.`);
        } else if (file.size > maxSize) {
            errors.push(`${file.name}: File too large. Maximum 5MB per image.`);
        } else {
            validFiles.push(file);
        }
    }
    
    return { validFiles, errors };
}

// Handle image upload
async function handleImageUpload() {
    const fileInput = document.getElementById('image-upload');
    const files = Array.from(fileInput.files);
    
    console.log('Upload initiated. Files selected:', files.length);
    
    if (files.length === 0) {
        window.toast.warning('Please select at least one image');
        return;
    }
    
    // Validate files
    const { validFiles, errors } = validateImageFiles(files);
    
    console.log('Validation complete. Valid files:', validFiles.length, 'Errors:', errors.length);
    
    // Display validation errors
    const errorContainer = document.getElementById('validation-errors');
    if (errors.length > 0) {
        errorContainer.innerHTML = errors.map(err => `<div>• ${err}</div>`).join('');
        errorContainer.classList.remove('hidden');
        
        if (validFiles.length === 0) {
            console.error('No valid files to upload');
            return; // Stop if no valid files
        }
    } else {
        errorContainer.classList.add('hidden');
    }
    
    // Prepare FormData
    const formData = new FormData();
    validFiles.forEach(file => {
        formData.append('image[]', file);
        console.log('Added file to FormData:', file.name, file.type, file.size);
    });
    
    // Show progress bar
    const progressContainer = document.getElementById('upload-progress');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    progressContainer.classList.remove('hidden');
    progressBar.style.width = '0%';
    progressText.textContent = 'Uploading...';
    
    try {
        const productSlug = getProductSlug();
        const apiUrl = `/api/admin/products/${productSlug}/images`;
        
        console.log('Uploading to:', apiUrl);
        
        // Use XMLHttpRequest for progress tracking
        await new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            
            // Progress tracking
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentage = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = `${percentage}%`;
                    progressText.textContent = `Uploading... ${percentage}%`;
                    console.log('Upload progress:', percentage + '%');
                }
            });
            
            // Completion
            xhr.addEventListener('load', () => {
                console.log('Upload complete. Status:', xhr.status);
                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve(JSON.parse(xhr.responseText));
                } else {
                    console.error('Upload failed. Response:', xhr.responseText);
                    reject(new Error(xhr.statusText || 'Upload failed'));
                }
            });
            
            xhr.addEventListener('error', () => {
                console.error('XHR error occurred');
                reject(new Error('Upload failed'));
            });
            
            // Get token from localStorage or sessionStorage
            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            
            if (!token) {
                console.error('No auth token found!');
                reject(new Error('Authentication required'));
                return;
            }
            
            console.log('Auth token found, length:', token.length);
            
            xhr.open('POST', apiUrl);
            xhr.setRequestHeader('Authorization', `Bearer ${token}`);
            xhr.send(formData);
        });
        
        progressText.textContent = 'Upload complete!';
        window.toast.success(`${validFiles.length} image(s) uploaded successfully`);
        
        console.log('Upload successful, refreshing product data...');
        
        // Reset form
        fileInput.value = '';
        errorContainer.classList.add('hidden');
        
        // Hide progress bar after 2 seconds and reload product
        setTimeout(() => {
            progressContainer.classList.add('hidden');
            loadProduct();
        }, 2000);
        
    } catch (error) {
        console.error('Upload error:', error);
        progressContainer.classList.add('hidden');
        const message = error.message || 'Failed to upload images';
        window.toast.error(message);
    }
}

// Show delete confirmation modal
function showDeleteConfirmModal(imageId) {
    const image = productData.images.find(img => img.id == imageId);
    if (!image) return;
    
    // Create modal
    const modal = document.createElement('div');
    modal.id = 'delete-image-modal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Delete Image</h3>
                <button onclick="closeDeleteModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mb-4">
                <img src="${image.signed_thumbnail_url || image.signed_url || image.thumbnail_url || image.url}" alt="${image.alt_text || 'Product image'}" class="w-full h-48 object-cover rounded-lg mb-4">
                <p class="text-gray-700">Are you sure you want to delete this image?</p>
                ${image.is_primary ? '<p class="text-sm text-yellow-600 mt-2"><i class="fas fa-info-circle mr-1"></i>This is the primary image. The next image will become primary.</p>' : ''}
            </div>
            
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button onclick="confirmDeleteImage(${imageId})" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Delete
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Close delete modal
window.closeDeleteModal = function() {
    const modal = document.getElementById('delete-image-modal');
    if (modal) {
        modal.remove();
    }
};

// Confirm delete image
window.confirmDeleteImage = async function(imageId) {
    try {
        const productSlug = getProductSlug();
        await axios.delete(`/api/admin/products/${productSlug}/images/${imageId}`);
        
        window.toast.success('Image deleted successfully');
        closeDeleteModal();
        loadProduct();
        
    } catch (error) {
        console.error('Delete error:', error);
        const message = error.response?.data?.message || 'Failed to delete image';
        window.toast.error(message);
    }
};

// Set image as primary
async function setPrimaryImage(imageId) {
    try {
        const productSlug = getProductSlug();
        await axios.patch(`/api/admin/products/${productSlug}/images/${imageId}/set-primary`);
        
        window.toast.success('Primary image updated');
        loadProduct();
        
    } catch (error) {
        console.error('Set primary error:', error);
        const message = error.response?.data?.message || 'Failed to update primary image';
        window.toast.error(message);
    }
}

// ============ DRAG AND DROP REORDERING ============

let draggedElement = null;
let draggedImageId = null;

function attachDragAndDropListeners() {
    const gallery = document.getElementById('image-gallery');
    if (!gallery) return;
    
    const imageCards = gallery.querySelectorAll('[draggable="true"]');
    
    imageCards.forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragover', handleDragOver);
        card.addEventListener('drop', handleDrop);
        card.addEventListener('dragend', handleDragEnd);
        card.addEventListener('dragenter', handleDragEnter);
        card.addEventListener('dragleave', handleDragLeave);
    });
}

function handleDragStart(e) {
    draggedElement = e.currentTarget;
    draggedImageId = draggedElement.dataset.imageId;
    e.currentTarget.classList.add('opacity-50', 'cursor-grabbing');
    e.dataTransfer.effectAllowed = 'move';
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    e.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDragEnter(e) {
    if (e.currentTarget !== draggedElement) {
        e.currentTarget.classList.add('ring-2', 'ring-blue-500', 'scale-105', 'transition-transform');
    }
}

function handleDragLeave(e) {
    e.currentTarget.classList.remove('ring-2', 'ring-blue-500', 'scale-105', 'transition-transform');
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    e.preventDefault();
    
    const dropTarget = e.currentTarget;
    dropTarget.classList.remove('ring-2', 'ring-blue-500', 'scale-105', 'transition-transform');
    
    if (draggedElement !== dropTarget) {
        // Swap the elements in DOM
        const gallery = document.getElementById('image-gallery');
        const allCards = Array.from(gallery.querySelectorAll('[draggable="true"]'));
        
        const draggedIndex = allCards.indexOf(draggedElement);
        const dropIndex = allCards.indexOf(dropTarget);
        
        if (draggedIndex < dropIndex) {
            dropTarget.parentNode.insertBefore(draggedElement, dropTarget.nextSibling);
        } else {
            dropTarget.parentNode.insertBefore(draggedElement, dropTarget);
        }
        
        // Update order in backend
        reorderImages();
    }
    
    return false;
}

function handleDragEnd(e) {
    e.currentTarget.classList.remove('opacity-50', 'cursor-grabbing');
    
    // Remove all drag styling
    const gallery = document.getElementById('image-gallery');
    const allCards = gallery.querySelectorAll('[draggable="true"]');
    allCards.forEach(card => {
        card.classList.remove('ring-2', 'ring-blue-500', 'scale-105', 'transition-transform');
    });
}

// Reorder images after drag and drop
async function reorderImages() {
    try {
        const gallery = document.getElementById('image-gallery');
        const imageCards = Array.from(gallery.querySelectorAll('[draggable="true"]'));
        
        const orderedImages = imageCards.map((card, index) => ({
            id: parseInt(card.dataset.imageId),
            order: index
        }));
        
        const productSlug = getProductSlug();
        await axios.post(`/api/admin/products/${productSlug}/images/reorder`, {
            images: orderedImages
        });
        
        window.toast.success('Images reordered successfully');
        
        // Refresh to get updated data
        loadProduct();
        
    } catch (error) {
        console.error('Reorder error:', error);
        const message = error.response?.data?.message || 'Failed to reorder images';
        window.toast.error(message);
        
        // Reload to reset order
        loadProduct();
    }
}
