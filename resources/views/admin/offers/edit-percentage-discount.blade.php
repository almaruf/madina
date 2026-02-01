@extends('admin.layout')

@section('title', 'Edit Percentage Discount Offer')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Loading State -->
    <div id="loading-state" class="bg-white rounded-lg shadow p-8 text-center">
        <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
        <p class="text-gray-600 mt-4">Loading offer...</p>
    </div>

    <!-- Edit Form -->
    <div id="form-container" class="hidden space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Edit Percentage Discount</h1>
                <p class="text-gray-600 mt-1">Update the sale details and product selection</p>
            </div>
            <a href="/admin/offers" class="text-green-600 hover:text-green-700 inline-flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Back to Offers
            </a>
        </div>

        <form id="offer-form" class="space-y-6" onsubmit="handleSubmit(event)">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-6">Basic Information</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Offer Name *</label>
                        <input type="text" id="name" required placeholder="e.g., Summer Sale 20%" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                        <textarea id="description" rows="3" placeholder="Tell customers about this sale" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"></textarea>
                    </div>
                </div>
            </div>

            <!-- Discount Configuration -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-6">Discount Configuration</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Percentage (%) *</label>
                        <input type="number" id="discount_value" min="0" max="100" step="0.01" required placeholder="e.g., 20" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                        <p class="text-sm text-gray-500 mt-1">Enter the percentage to discount (0-100)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Min Purchase Amount (£)</label>
                        <input type="number" id="min_purchase_amount" min="0" step="0.01" placeholder="e.g., 25" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                        <p class="text-sm text-gray-500 mt-1">Leave blank for no minimum</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Badge Text</label>
                        <input type="text" id="badge_text" placeholder="e.g., 20% OFF" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                        <p class="text-sm text-gray-500 mt-1">Text shown on product cards</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Badge Color</label>
                        <input type="color" id="badge_color" class="w-full border rounded-lg px-2 py-2 h-10 focus:ring-2 focus:ring-green-500" value="#DC2626">
                    </div>
                </div>
            </div>

            <!-- Validity Period -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-6">Validity Period</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date & Time</label>
                        <input type="datetime-local" id="starts_at" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                        <p class="text-sm text-gray-500 mt-1">When the sale begins</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">End Date & Time</label>
                        <input type="datetime-local" id="ends_at" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                        <p class="text-sm text-gray-500 mt-1">When the sale ends</p>
                    </div>
                </div>
            </div>

            <!-- Usage Limits -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-6">Usage Limits (Optional)</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Max Uses Per Customer</label>
                        <input type="number" id="max_uses_per_customer" min="1" placeholder="e.g., 3" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                        <p class="text-sm text-gray-500 mt-1">Leave blank for unlimited</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Total Usage Limit</label>
                        <input type="number" id="total_usage_limit" min="1" placeholder="e.g., 100" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                        <p class="text-sm text-gray-500 mt-1">Leave blank for unlimited</p>
                    </div>
                </div>
            </div>

            <!-- Product Selection -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-6">Apply to Products</h2>
                
                <div class="space-y-4">
                    <div class="flex gap-4 flex-wrap">
                        <label class="flex items-center">
                            <input type="radio" name="product-scope" value="all" checked onchange="handleProductScopeChange()">
                            <span class="ml-2 text-sm font-semibold">All Products</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="product-scope" value="category" onchange="handleProductScopeChange()">
                            <span class="ml-2 text-sm font-semibold">Specific Category</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="product-scope" value="selected" onchange="handleProductScopeChange()">
                            <span class="ml-2 text-sm font-semibold">Specific Products</span>
                        </label>
                    </div>

                    <!-- Category Selection -->
                    <div id="category-selection" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Choose Category</label>
                        <select id="selected-category" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                            <option value="">Select a category...</option>
                        </select>
                    </div>

                    <!-- Product Selection -->
                    <div id="product-selection" class="hidden">
                        <button type="button" onclick="openProductModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Add Products
                        </button>
                        
                        <div id="selected-products-display" class="mt-4 space-y-2">
                            <p class="text-gray-500">No products selected</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-6">Status</h2>
                
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" id="is_active" class="w-5 h-5">
                    <span class="text-sm font-semibold text-gray-700">Active (customers can use this offer)</span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold inline-flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Save Offer
                </button>
                <button type="button" onclick="deleteOffer()" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold inline-flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    Delete
                </button>
                <a href="/admin/offers" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Product Selection Modal -->
<div id="product-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="border-b p-4 flex justify-between items-center">
            <h3 class="text-xl font-bold">Select Products</h3>
            <button type="button" onclick="closeProductModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <!-- Search & Filter -->
        <div class="border-b p-4 space-y-3">
            <input type="text" id="product-search" placeholder="Search products..." 
                class="w-full border rounded-lg px-4 py-2" onkeyup="searchProducts()">
            
            <div class="flex gap-2">
                <select id="product-category-filter" class="flex-1 border rounded-lg px-4 py-2" onchange="searchProducts()">
                    <option value="">All Categories</option>
                </select>
                
                <button type="button" onclick="clearProductSelection()" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                    Clear All
                </button>
            </div>
        </div>

        <!-- Products List -->
        <div id="products-list" class="overflow-y-auto flex-1 p-4">
            <!-- Products loaded here -->
        </div>

        <!-- Footer -->
        <div class="border-t p-4 flex justify-end gap-2">
            <button type="button" onclick="closeProductModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                Cancel
            </button>
            <button type="button" onclick="confirmProductSelection()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Done
            </button>
        </div>
    </div>
</div>

<script>
    // CRITICAL: Ensure axios is configured with auth token
    (() => {
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        if (!token) {
            console.error('No auth token found');
            window.location.href = '/admin/login';
        } else {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            axios.defaults.headers.common['Accept'] = 'application/json';
            axios.defaults.headers.common['Content-Type'] = 'application/json';
            console.log('Auth token configured for axios:', token.substring(0, 20) + '...');
        }
    })();

    const getAuthHeaders = () => {
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        return token ? { Authorization: `Bearer ${token}` } : {};
    };

    const offerId = new URLSearchParams(window.location.search).get('id');
    let offerData = null;
    let allProducts = [];
    let allCategories = [];
    let selectedProducts = [];

    async function loadOffer() {
        try {
            const [offerRes, categoriesRes] = await Promise.all([
                axios.get(`/api/admin/offers/${offerId}`, { headers: getAuthHeaders() }),
                axios.get('/api/admin/categories', { headers: getAuthHeaders() })
            ]);
            
            offerData = offerRes.data;
            allCategories = categoriesRes.data.data || categoriesRes.data;
            selectedProducts = (offerData.products || []).map(p => p.id);
            
            populateForm();
            loadCategorySelect();
            document.getElementById('loading-state').classList.add('hidden');
            document.getElementById('form-container').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading offer:', error);
            toast.error('Failed to load offer: ' + (error.response?.data?.message || error.message));
        }
    }

    function populateForm() {
        const setIfExists = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.value = value || '';
        };

        const setCheckedIfExists = (id, checked) => {
            const el = document.getElementById(id);
            if (el) el.checked = checked;
        };

        setIfExists('name', offerData.name);
        setIfExists('description', offerData.description);
        setIfExists('badge_text', offerData.badge_text);
        setIfExists('badge_color', offerData.badge_color || '#DC2626');
        setIfExists('discount_value', offerData.discount_value);
        setIfExists('min_purchase_amount', offerData.min_purchase_amount);
        setIfExists('max_uses_per_customer', offerData.max_uses_per_customer);
        setIfExists('total_usage_limit', offerData.total_usage_limit);
        setCheckedIfExists('is_active', offerData.is_active);
        
        if (offerData.starts_at) {
            setIfExists('starts_at', new Date(offerData.starts_at).toISOString().slice(0, 16));
        }
        if (offerData.ends_at) {
            setIfExists('ends_at', new Date(offerData.ends_at).toISOString().slice(0, 16));
        }

        // Determine scope from offer
        if (offerData.scope) {
            const scopeRadio = document.querySelector(`input[name="product-scope"][value="${offerData.scope}"]`);
            if (scopeRadio) {
                scopeRadio.checked = true;
                
                if (offerData.scope === 'category' && offerData.category_id) {
                    const categorySelect = document.getElementById('selected-category');
                    if (categorySelect) categorySelect.value = offerData.category_id;
                }
            }
        } else {
            // Default to 'all' if no scope specified
            const allRadio = document.querySelector('input[name="product-scope"][value="all"]');
            if (allRadio) allRadio.checked = true;
        }

        // Call after scope is set
        handleProductScopeChange();
        renderSelectedProducts();
    }

    function loadCategorySelect() {
        const select = document.getElementById('selected-category');
        const filterSelect = document.getElementById('product-category-filter');
        
        allCategories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.name;
            select.appendChild(option);
            
            const filterOption = document.createElement('option');
            filterOption.value = cat.id;
            filterOption.textContent = cat.name;
            filterSelect.appendChild(filterOption);
        });
    }

    function handleProductScopeChange() {
        const scopeRadio = document.querySelector('input[name="product-scope"]:checked');
        if (!scopeRadio) return; // Exit if no radio button is checked yet
        
        const scope = scopeRadio.value;
        const categorySelection = document.getElementById('category-selection');
        const productSelection = document.getElementById('product-selection');
        
        if (categorySelection) categorySelection.classList.add('hidden');
        if (productSelection) productSelection.classList.add('hidden');
        
        if (scope === 'category' && categorySelection) {
            categorySelection.classList.remove('hidden');
        } else if (scope === 'selected' && productSelection) {
            productSelection.classList.remove('hidden');
        }
    }

    async function openProductModal() {
        try {
            const res = await axios.get('/api/admin/products?per_page=1000', {
                headers: getAuthHeaders()
            });
            allProducts = res.data.data || res.data;
            renderProductsList(allProducts);
            document.getElementById('product-modal').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading products:', error);
            toast.error('Failed to load products');
        }
    }

    function closeProductModal() {
        document.getElementById('product-modal').classList.add('hidden');
    }

    function renderProductsList(products) {
        const list = document.getElementById('products-list');
        list.innerHTML = products.map(product => {
            const imageUrl = product.primary_image?.url || null;
            const defaultVariation = product.variations?.find(v => v.is_default) || product.variations?.[0];
            const priceText = defaultVariation?.price ? `£${parseFloat(defaultVariation.price).toFixed(2)}` : 'No price';
            const categories = (product.categories || [])
                .map(c => `<span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs">${c.name}</span>`)
                .join('');

            return `
            <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 mb-2">
                <input type="checkbox" value="${product.id}" class="product-checkbox mt-1"
                    ${selectedProducts.includes(product.id) ? 'checked' : ''}>
                <div class="w-14 h-14 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                    ${imageUrl ? `<img src="${imageUrl}" alt="${product.name}" class="w-full h-full object-cover">` : '<div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image"></i></div>'}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <div class="font-semibold truncate">${product.name}</div>
                        <div class="text-sm font-semibold text-green-600">${priceText}</div>
                    </div>
                    <div class="text-xs text-gray-500">SKU: ${product.sku || 'N/A'}</div>
                    ${categories ? `<div class="flex flex-wrap gap-1 mt-2">${categories}</div>` : ''}
                </div>
            </label>
            `;
        }).join('');
    }

    function searchProducts() {
        const query = document.getElementById('product-search').value.toLowerCase();
        const categoryId = document.getElementById('product-category-filter').value;
        
        const filtered = allProducts.filter(p => {
            const matchesSearch = p.name.toLowerCase().includes(query) || 
                                (p.sku && p.sku.toLowerCase().includes(query));
            const matchesCategory = !categoryId || p.categories?.some(c => c.id == categoryId);
            return matchesSearch && matchesCategory;
        });
        
        renderProductsList(filtered);
    }

    function confirmProductSelection() {
        selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked'))
            .map(checkbox => parseInt(checkbox.value));
        
        renderSelectedProducts();
        closeProductModal();
    }

    function renderSelectedProducts() {
        const container = document.getElementById('selected-products-display');
        const sourceProducts = allProducts.length > 0 ? allProducts : (offerData?.products || []);
        const selected = sourceProducts.filter(p => selectedProducts.includes(p.id));
        
        container.innerHTML = selected.length > 0 
            ? selected.map(p => `
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                    <span class="text-sm">${p.name}</span>
                    <button type="button" onclick="removeProduct(${p.id})" class="text-red-600 hover:text-red-700">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `).join('')
            : '<p class="text-gray-500 text-sm">No products selected</p>';
    }

    function removeProduct(productId) {
        selectedProducts = selectedProducts.filter(id => id !== productId);
        renderSelectedProducts();
    }

    function clearProductSelection() {
        selectedProducts = [];
        document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = false);
    }

    async function handleSubmit(e) {
        e.preventDefault();
        
        const scope = document.querySelector('input[name="product-scope"]:checked').value;

        if (scope === 'selected' && selectedProducts.length === 0) {
            toast.error('Please select at least one product');
            return;
        }

        if (scope === 'category' && !document.getElementById('selected-category').value) {
            toast.error('Please select a category');
            return;
        }

        const formData = {
            name: document.getElementById('name').value,
            description: document.getElementById('description').value || null,
            badge_text: document.getElementById('badge_text').value || null,
            badge_color: document.getElementById('badge_color').value,
            discount_value: document.getElementById('discount_value').value,
            min_purchase_amount: document.getElementById('min_purchase_amount').value || null,
            max_uses_per_customer: document.getElementById('max_uses_per_customer').value || null,
            total_usage_limit: document.getElementById('total_usage_limit').value || null,
            starts_at: document.getElementById('starts_at').value || null,
            ends_at: document.getElementById('ends_at').value || null,
            is_active: document.getElementById('is_active').checked,
            scope: scope,
            category_id: scope === 'category' ? document.getElementById('selected-category').value : null,
            product_ids: scope === 'selected' ? selectedProducts : []
        };

        try {
            await axios.put(`/api/admin/offers/${offerId}`, formData, {
                headers: getAuthHeaders()
            });
            toast.success('Offer updated successfully');
            setTimeout(() => window.location.href = '/admin/offers', 1500);
        } catch (error) {
            console.error('Error updating offer:', error);
            const message = error.response?.data?.message || 'Failed to update offer';
            toast.error(message);
        }
    }

    async function deleteOffer() {
        if (!confirm('Are you sure you want to delete this offer? This action cannot be undone.')) return;
        
        try {
            await axios.delete(`/api/admin/offers/${offerId}`, {
                headers: getAuthHeaders()
            });
            toast.success('Offer deleted successfully');
            setTimeout(() => window.location.href = '/admin/offers', 1500);
        } catch (error) {
            console.error('Error deleting offer:', error);
            toast.error('Failed to delete offer');
        }
    }

    loadOffer();
</script>
@endsection
