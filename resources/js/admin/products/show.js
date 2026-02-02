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
    
    document.getElementById('product-container').innerHTML = `
        <!-- Action Buttons -->
        <div class="flex gap-3">
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
        
        ${isArchived ? '<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg"><i class="fas fa-exclamation-triangle mr-2"></i>This product is archived</div>' : ''}
        
        <!-- Product Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Images -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Product Image</h3>
                <img src="${productData.primary_image?.url || '/placeholder.png'}" alt="${productData.name}" class="w-full h-64 object-cover rounded-lg">
            </div>
            
            <!-- Basic Info -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
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
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Description</p>
                        <p class="text-base">${productData.description}</p>
                    </div>
                ` : ''}
            </div>
        </div>
        
        <!-- Variations -->
        ${productData.variations && productData.variations.length > 0 ? `
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Variations</h3>
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
    `;

    // Attach event listeners after rendering
    attachEventListeners();
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
