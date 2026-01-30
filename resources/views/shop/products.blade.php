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
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-green-600">£${minPrice.toFixed(2)}</span>
                            <button onclick="event.stopPropagation(); viewProduct('${product.slug}')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                View
                            </button>
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
