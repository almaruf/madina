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
    const productId = {{ request()->route('id') }};
    let productData = null;
    
    async function loadProduct() {
        try {
            const response = await axios.get(`/api/admin/products/${productId}`);
            productData = response.data.data || response.data;
            renderProduct();
        } catch (error) {
            console.error('Error loading product:', error);
            document.getElementById('product-container').innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 p-6 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Failed to load product details
                </div>
            `;
        }
    }
    
    function renderProduct() {
        const price = productData.variations?.[0]?.price ? parseFloat(productData.variations[0].price).toFixed(2) : '0.00';
        const stock = productData.variations?.[0]?.stock || 0;
        const categories = productData.categories?.map(c => c.name).join(', ') || 'None';
        const isArchived = productData.deleted_at !== null;
        
        document.getElementById('product-container').innerHTML = `
            <!-- Action Buttons -->
            <div class="flex gap-3">
                ${!isArchived ? `
                    <button onclick="editProduct()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                        <i class="fas fa-edit mr-2"></i>Edit Product
                    </button>
                    <button onclick="archiveProduct()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold">
                        <i class="fas fa-archive mr-2"></i>Archive
                    </button>
                ` : `
                    <button onclick="restoreProduct()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
                        <i class="fas fa-undo mr-2"></i>Restore
                    </button>
                    <button onclick="permanentDelete()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold">
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
                                        <td class="px-4 py-3 text-sm">${v.stock}</td>
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
    
    function editProduct() {
        toast.info('Edit functionality coming soon!');
    }
    
    async function archiveProduct() {
        if (!confirm('Are you sure you want to archive this product?')) return;
        
        try {
            await axios.delete(`/api/admin/products/${productId}`);
            toast.success('Product archived successfully');
            setTimeout(() => window.location.href = '/admin/products', 1500);
        } catch (error) {
            console.error('Error archiving product:', error);
            toast.error('Failed to archive product');
        }
    }
    
    async function restoreProduct() {
        try {
            await axios.post(`/api/admin/products/${productId}/restore`);
            toast.success('Product restored successfully');
            loadProduct();
        } catch (error) {
            console.error('Error restoring product:', error);
            toast.error('Failed to restore product');
        }
    }
    
    async function permanentDelete() {
        if (!confirm('Are you sure you want to PERMANENTLY delete this product? This cannot be undone!')) return;
        
        try {
            await axios.delete(`/api/admin/products/${productId}/force`);
            toast.success('Product permanently deleted');
            setTimeout(() => window.location.href = '/admin/products', 1500);
        } catch (error) {
            console.error('Error deleting product:', error);
            toast.error('Failed to delete product');
        }
    }
    
    loadProduct();
</script>
@endsection
