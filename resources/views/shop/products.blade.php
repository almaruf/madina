@extends('shop.layout')

@section('title', 'Shop - ' . app(\App\Services\ShopConfigService::class)->name())

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Shop All Products</h1>
        <p class="text-gray-600">Browse our full range of fresh products</p>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-wrap gap-4">
        <select id="category-filter" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
            <option value="">All Categories</option>
        </select>
        
        <select id="sort-filter" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
            <option value="featured">Featured</option>
            <option value="name_asc">Name (A-Z)</option>
            <option value="name_desc">Name (Z-A)</option>
            <option value="price_asc">Price (Low to High)</option>
            <option value="price_desc">Price (High to Low)</option>
        </select>

        <input type="text" id="search-input" placeholder="Search products..." class="flex-1 min-w-[200px] border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
    </div>

    <!-- Loading State -->
    <div id="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
        <p class="mt-4 text-gray-600">Loading products...</p>
    </div>

    <!-- Products Grid -->
    <div id="products-grid" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Products will be loaded here -->
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden text-center py-12">
        <i class="fas fa-shopping-basket text-6xl text-gray-400 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
        <p class="text-gray-600">Try adjusting your filters or search terms</p>
    </div>
</div>

<!-- Cart Message -->
<div id="cart-message" class="hidden fixed top-6 right-6 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg z-50">
    <span></span>
</div>

<!-- Variation Modal -->
<div id="variation-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold" id="variation-modal-title">Select Variation</h3>
            <button class="text-gray-500 hover:text-gray-700" onclick="closeVariationModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <div id="variation-modal-options" class="space-y-3"></div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Quantity</label>
                <input type="number" id="variation-qty" min="1" value="1" class="w-24 border border-gray-300 rounded-lg px-3 py-2">
            </div>
        </div>
        <div class="px-6 py-4 border-t flex items-center justify-end gap-3">
            <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50" onclick="closeVariationModal()">Cancel</button>
            <button id="variation-add-btn" class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700" onclick="confirmVariationAdd()">Add to Cart</button>
        </div>
    </div>
</div>

<script>
    let allProducts = [];
    let categories = [];
    let currentFilters = {
        category: '',
        sort: 'featured',
        search: ''
    };

    async function loadData() {
        try {
            // Load products and categories
            const [productsRes, categoriesRes] = await Promise.all([
                axios.get('/api/products'),
                axios.get('/api/categories')
            ]);
            
            allProducts = productsRes.data.data || productsRes.data;
            categories = categoriesRes.data.data || categoriesRes.data;
            
            renderCategories();
            renderProducts();
            
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('products-grid').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading data:', error);
            document.getElementById('loading').innerHTML = '<p class="text-red-600">Failed to load products</p>';
        }
    }

    function renderCategories() {
        const select = document.getElementById('category-filter');
        categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            select.appendChild(option);
        });
    }

    function renderProducts() {
        let filteredProducts = [...allProducts];

        // Apply category filter
        if (currentFilters.category) {
            filteredProducts = filteredProducts.filter(product => 
                product.categories?.some(cat => cat.id == currentFilters.category)
            );
        }

        // Apply search filter
        if (currentFilters.search) {
            const search = currentFilters.search.toLowerCase();
            filteredProducts = filteredProducts.filter(product =>
                product.name.toLowerCase().includes(search) ||
                product.description?.toLowerCase().includes(search)
            );
        }

        // Apply sorting
        switch (currentFilters.sort) {
            case 'name_asc':
                filteredProducts.sort((a, b) => a.name.localeCompare(b.name));
                break;
            case 'name_desc':
                filteredProducts.sort((a, b) => b.name.localeCompare(a.name));
                break;
            case 'price_asc':
                filteredProducts.sort((a, b) => getMinPrice(a) - getMinPrice(b));
                break;
            case 'price_desc':
                filteredProducts.sort((a, b) => getMinPrice(b) - getMinPrice(a));
                break;
        }

        const grid = document.getElementById('products-grid');
        const emptyState = document.getElementById('empty-state');

        if (filteredProducts.length === 0) {
            grid.classList.add('hidden');
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        grid.classList.remove('hidden');

        grid.innerHTML = filteredProducts.map(product => {
            const minPrice = getMinPrice(product);
            const imageUrl = product.primary_image?.url || product.images?.[0]?.url || '/images/placeholder.jpg';
            
            return `
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition cursor-pointer" onclick="window.location.href='/products/${product.slug}'">
                    <div class="aspect-square overflow-hidden rounded-t-lg bg-gray-100">
                        <img src="${imageUrl}" alt="${product.name}" class="w-full h-full object-cover">
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">${product.name}</h3>
                        ${product.description ? `<p class="text-sm text-gray-600 mb-2 line-clamp-2">${product.description}</p>` : ''}
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-green-600">£${minPrice.toFixed(2)}</span>
                            <div class="flex items-center gap-2">
                                <button onclick="event.stopPropagation(); addToCartFromCard(${product.id})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm">
                                    Add to Cart
                                </button>
                                <button onclick="event.stopPropagation(); viewProduct('${product.slug}')" class="border border-green-600 text-green-600 hover:bg-green-50 px-3 py-2 rounded-lg text-sm">
                                    View
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function getMinPrice(product) {
        if (!product.variations || product.variations.length === 0) return 0;
        return Math.min(...product.variations.map(v => parseFloat(v.price)));
    }

    function viewProduct(slug) {
        window.location.href = '/products/' + slug;
    }

    let currentProduct = null;

    function addToCartFromCard(productId) {
        const product = allProducts.find(item => item.id === productId);
        if (!product) return;

        const variations = product.variations || [];
        if (variations.length === 1) {
            addToCart(product.id, variations[0].id, 1);
            showCartMessage('Added to cart');
            return;
        }

        currentProduct = product;
        openVariationModal(product);
    }

    function openVariationModal(product) {
        document.getElementById('variation-modal-title').textContent = `Select Variation - ${product.name}`;
        const options = product.variations || [];
        const optionsHtml = options.map((variation, index) => {
            const price = parseFloat(variation.price).toFixed(2);
            const stock = variation.stock_quantity ?? variation.stock ?? null;
            const outOfStock = stock !== null && stock <= 0;
            return `
                <label class="flex items-start p-3 border border-gray-300 rounded-lg ${outOfStock ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-green-50'}">
                    <input type="radio" name="variation_id" value="${variation.id}" class="mt-1 w-4 h-4" ${index === 0 && !outOfStock ? 'checked' : ''} ${outOfStock ? 'disabled' : ''}>
                    <div class="ml-3 flex-1">
                        <div class="font-semibold">${variation.name || 'Standard'}</div>
                        <div class="text-sm text-gray-600">£${price}</div>
                        ${stock !== null ? `<div class="text-xs text-gray-500">${stock} in stock</div>` : ''}
                    </div>
                </label>
            `;
        }).join('');

        document.getElementById('variation-modal-options').innerHTML = optionsHtml || '<p class="text-gray-600">No variations available.</p>';
        document.getElementById('variation-qty').value = 1;
        document.getElementById('variation-modal').classList.remove('hidden');

        const hasAvailable = options.some(v => {
            const stock = v.stock_quantity ?? v.stock ?? null;
            return stock === null || stock > 0;
        });
        document.getElementById('variation-add-btn').disabled = !hasAvailable;
        document.getElementById('variation-add-btn').classList.toggle('opacity-50', !hasAvailable);
    }

    function closeVariationModal() {
        document.getElementById('variation-modal').classList.add('hidden');
        currentProduct = null;
    }

    function confirmVariationAdd() {
        if (!currentProduct) return;
        const selected = document.querySelector('input[name="variation_id"]:checked');
        if (!selected) {
            showCartMessage('Please select a variation', 'error');
            return;
        }
        const qty = Math.max(1, parseInt(document.getElementById('variation-qty').value || '1'));
        addToCart(currentProduct.id, parseInt(selected.value), qty);
        showCartMessage('Added to cart');
        closeVariationModal();
    }

    function addToCart(productId, variationId, quantity = 1) {
        const cart = JSON.parse(localStorage.getItem('shopping_cart') || '[]');
        const existing = cart.find(item => item.product_id === productId && item.variation_id === variationId);
        if (existing) {
            existing.quantity += quantity;
        } else {
            cart.push({ product_id: productId, variation_id: variationId, quantity });
        }
        localStorage.setItem('shopping_cart', JSON.stringify(cart));
        if (typeof updateCartCount === 'function') {
            updateCartCount();
        }
    }

    function showCartMessage(message, type = 'success') {
        const el = document.getElementById('cart-message');
        el.querySelector('span').textContent = message;
        el.className = 'fixed top-6 right-6 px-4 py-3 rounded-lg shadow-lg z-50 ' + (type === 'error' ? 'bg-red-600 text-white' : 'bg-green-600 text-white');
        el.classList.remove('hidden');
        setTimeout(() => el.classList.add('hidden'), 2000);
    }

    // Event listeners
    document.getElementById('category-filter').addEventListener('change', (e) => {
        currentFilters.category = e.target.value;
        renderProducts();
    });

    document.getElementById('sort-filter').addEventListener('change', (e) => {
        currentFilters.sort = e.target.value;
        renderProducts();
    });

    let searchTimeout;
    document.getElementById('search-input').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentFilters.search = e.target.value;
            renderProducts();
        }, 300);
    });

    // Load data on page load
    loadData();
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
