// Admin Products JS
let allProducts = [];
let currentTab = 'active';

async function loadProducts() {
    try {
        const url = currentTab === 'archived' ? '/api/admin/products?archived=1' : '/api/admin/products';
        const response = await axios.get(url);
        allProducts = response.data.data || response.data;
        renderProducts();
    } catch (error) {
        console.error('Error loading products:', error);
        document.getElementById('products-table').innerHTML = '<tr><td colspan="6" class="text-center text-red-600 py-4">Failed to load products</td></tr>';
    }
}

function renderProducts() {
    const tbody = document.getElementById('products-table');
    
    if (allProducts.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-gray-600 py-4">No products found</td></tr>';
        return;
    }
    
    tbody.innerHTML = allProducts.map(product => {
        const defaultVariation = product.variations?.find(v => v.is_default) || product.variations?.[0];
        
        return `
            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/products/${product.slug}'">
                <td class="px-6 py-4">
                    ${product.primary_image 
                        ? `<img src="${product.primary_image.url}" class="w-16 h-16 object-cover rounded">`
                        : '<div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>'
                    }
                </td>
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">${product.name}</div>
                    <div class="text-sm text-gray-500">${product.categories?.map(c => c.name).join(', ') || 'No category'}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    ${defaultVariation ? `£${parseFloat(defaultVariation.price).toFixed(2)}` : 'N/A'}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    ${defaultVariation ? defaultVariation.stock_quantity : 'N/A'}
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded ${product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${product.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded ${product.is_featured ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'}">
                        ${product.is_featured ? 'Featured' : 'Regular'}
                    </span>
                </td>
            </tr>
        `;
    }).join('');
}

function switchTab(tab) {
    currentTab = tab;
    
    // Update tab styling
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-green-600', 'text-green-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    const activeBtn = document.getElementById(`tab-${tab}`);
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    activeBtn.classList.add('border-green-600', 'text-green-600');
    
    loadProducts();
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Set up tab buttons
    document.getElementById('tab-active').addEventListener('click', () => switchTab('active'));
    document.getElementById('tab-archived').addEventListener('click', () => switchTab('archived'));
    
    // Set up create button
    document.getElementById('create-product-btn').addEventListener('click', () => {
        window.location.href = '/admin/products/create';
    });
    
    // Load initial data
    loadProducts();
});
