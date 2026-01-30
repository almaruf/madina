@extends('admin.layout')

@section('title', 'Product Details')

@section('page-title', 'Product Details')

@section('content')
<div class="mb-6">
    <a href="/admin/products" class="text-green-600 hover:text-green-700 inline-flex items-center gap-2">
        <i class="fas fa-arrow-left"></i>
        Back to Products
    </a>
</div>

<div id="product-container" class="space-y-6">
    <div class="text-center py-8">
        <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
        <p class="text-gray-600 mt-4">Loading product details...</p>
    </div>
</div>

<script>
    const productSlug = '{{ request()->route("slug") }}';
    let productData = null;
    
    async function loadProduct() {
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/admin/login';
                return;
            }

            const response = await axios.get(`/api/admin/products/${productSlug}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
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
    
    function renderProduct() {
        const price = productData.variations?.[0]?.price ? parseFloat(productData.variations[0].price).toFixed(2) : '0.00';
        const stock = productData.variations?.[0]?.stock_quantity || 0;
        const categories = productData.categories?.map(c => c.name).join(', ') || 'None';
        const isArchived = productData.deleted_at !== null;
        
        document.getElementById('product-container').innerHTML = `
            <!-- Action Buttons -->
            <div class="flex gap-3">
                ${!isArchived ? `
                    <a href="/admin/products/${productSlug}/edit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold inline-flex items-center">
                        <i class="fas fa-edit mr-2"></i>Edit Product
                    </a>
                    <button onclick="confirmArchive()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold">
                        <i class="fas fa-archive mr-2"></i>Archive
                    </button>
                ` : `
                    <button onclick="restoreProduct()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
                        <i class="fas fa-undo mr-2"></i>Restore
                    </button>
                    <button onclick="confirmPermanentDelete()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold">
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
    }
    
    function confirmArchive() {
        toast.warning('Click Archive again to confirm', 3000);
        const btn = event.target.closest('button');
        btn.innerHTML = '<i class="fas fa-check mr-2"></i>Confirm Archive';
        btn.onclick = archiveProduct;
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-archive mr-2"></i>Archive';
            btn.onclick = confirmArchive;
        }, 3000);
    }
    
    async function archiveProduct() {
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/admin/login';
                return;
            }

            await axios.delete(`/api/admin/products/${productSlug}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            toast.success('Product archived successfully');
            setTimeout(() => window.location.href = '/admin/products', 1500);
        } catch (error) {
            console.error('Error archiving product:', error);
            const message = error.response?.data?.message || 'Failed to archive product';
            toast.error(message);
        }
    }
    
    async function restoreProduct() {
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/admin/login';
                return;
            }

            await axios.post(`/api/admin/products/${productSlug}/restore`, {}, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            toast.success('Product restored successfully');
            loadProduct();
        } catch (error) {
            console.error('Error restoring product:', error);
            const message = error.response?.data?.message || 'Failed to restore product';
            toast.error(message);
        }
    }
    
    function confirmPermanentDelete() {
        toast.warning('Click Delete again to permanently delete', 3000);
        const btn = event.target.closest('button');
        btn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete';
        btn.onclick = permanentDelete;
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-trash mr-2"></i>Permanent Delete';
            btn.onclick = confirmPermanentDelete;
        }, 3000);
    }
    
    async function permanentDelete() {
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/admin/login';
                return;
            }

            await axios.delete(`/api/admin/products/${productSlug}/force`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            toast.success('Product permanently deleted');
            setTimeout(() => window.location.href = '/admin/products', 1500);
        } catch (error) {
            console.error('Error deleting product:', error);
            const message = error.response?.data?.message || 'Failed to delete product';
            toast.error(message);
        }
    }
    
    const waitForAuth = setInterval(() => {
        const token = localStorage.getItem('auth_token');
        if (token && axios.defaults.headers.common['Authorization']) {
            clearInterval(waitForAuth);
            loadProduct();
        }
    }, 100);

    setTimeout(() => {
        clearInterval(waitForAuth);
        if (!productData) {
            loadProduct();
        }
    }, 1000);
</script>
@endsection
