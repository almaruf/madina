const ADMIN_SHOP_SESSION_KEY = 'admin_selected_shop_id';

async function loadSessionShop() {
    try {
        const response = await axios.get('/api/admin/shop-selected');
        const { shop_id, shop } = response.data;
        
        if (shop_id && shop) {
            displayShopIndicator(shop);
            return shop_id;
        } else {
            hideShopIndicator();
            return null;
        }
    } catch (error) {
        console.error('Error loading session shop:', error);
        return null;
    }
}

function displayShopIndicator(shop) {
    const indicator = document.getElementById('shop-indicator');
    const nameEl = document.getElementById('shop-indicator-name');
    
    if (indicator && nameEl) {
        nameEl.textContent = shop.name + (shop.city ? ` • ${shop.city}` : '');
        indicator.classList.remove('hidden');
    }
}

function hideShopIndicator() {
    const indicator = document.getElementById('shop-indicator');
    if (indicator) {
        indicator.classList.add('hidden');
    }
}

async function setSessionShop(shopId) {
    try {
        await axios.post('/api/admin/shop-selected', { shop_id: shopId });
        await loadSessionShop();
    } catch (error) {
        console.error('Error setting shop:', error);
    }
}

async function clearSessionShop() {
    try {
        await axios.post('/api/admin/shop-selected', { shop_id: null });
        hideShopIndicator();
    } catch (error) {
        console.error('Error clearing shop:', error);
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    // Load current session shop
    await loadSessionShop();
    
    // Setup clear button
    const clearBtn = document.getElementById('clear-shop-selection');
    if (clearBtn) {
        clearBtn.addEventListener('click', clearSessionShop);
    }
});

window.setSessionShop = setSessionShop;
window.clearSessionShop = clearSessionShop;
window.loadSessionShop = loadSessionShop;
