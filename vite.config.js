import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/admin/layout.js',
                'resources/js/admin/login.js',
                'resources/js/admin/dashboard.js',
                'resources/js/admin/users.js',
                'resources/js/admin/products.js',
                'resources/js/admin/categories.js',
                'resources/js/admin/orders.js',
                'resources/js/admin/offers.js',
                'resources/js/admin/delivery-slots.js',
                'resources/js/admin/shops.js',
                'resources/js/admin/admin-users.js',
                // Show pages
                'resources/js/admin/orders/show.js',
                'resources/js/admin/products/show.js',
                'resources/js/admin/users/show.js',
                'resources/js/admin/categories/show.js',
                'resources/js/admin/shops/show.js',
                'resources/js/admin/offers/show.js',
                // Edit pages
                'resources/js/admin/products/edit.js',
                'resources/js/admin/users/edit.js',
                'resources/js/admin/categories/edit.js',
                'resources/js/admin/shops/edit.js',
                // Create pages
                'resources/js/admin/shops/create.js',
                'resources/js/admin/products/create.js',
                'resources/js/admin/categories/create.js',
                'resources/js/admin/offers/create.js',
                'resources/js/admin/offers/create-percentage-discount.js',
                'resources/js/admin/offers/create-bxgy.js',
                // Edit offers pages
                'resources/js/admin/offers/edit.js',
                'resources/js/admin/offers/edit-percentage-discount.js',
                'resources/js/admin/offers/edit-bxgy.js',
                // Queue page
                'resources/js/admin/queue/index.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'probable-goggles-v676qw94rjcwgxr-5173.app.github.dev',
            protocol: 'wss',
            clientPort: 443,
        },
    },
});
