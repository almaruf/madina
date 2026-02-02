<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $shopId = \App\Services\ShopContext::getShopId();
        $query = Product::where('shop_id', $shopId)
            ->with(['variations', 'primaryImage', 'categories']);

        // Handle archived filter
        if ($request->has('archived') && $request->archived == '1') {
            $query->onlyTrashed();
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $products = $query->latest()->paginate(50);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:standard,meat,frozen,fresh,perishable',
            'description' => 'nullable|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'variations' => 'required|array|min:1',
            'variations.*.size' => 'required|string',
            'variations.*.unit' => 'required|string',
            'variations.*.price' => 'required|numeric|min:0',
            'variations.*.stock_quantity' => 'required|integer|min:0',
            'variations.*.is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $product = Product::create(array_merge(
                $request->except(['categories', 'variations']),
                ['shop_id' => \App\Services\ShopContext::getShopId()]
            ));

            // Attach categories
            $product->categories()->attach($request->categories);

            // Create variations
            foreach ($request->variations as $variation) {
                // Auto-generate name from size + unit if not provided
                if (empty($variation['name'])) {
                    $variation['name'] = $variation['size'] . ' ' . $variation['unit'];
                }
                // Map 'unit' to 'size_unit' for database
                if (isset($variation['unit'])) {
                    $variation['size_unit'] = $variation['unit'];
                    unset($variation['unit']);
                }
                $product->variations()->create($variation);
            }

            DB::commit();

            return response()->json($product->load(['categories', 'variations']), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create product', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($slug)
    {
        $shopId = \App\Services\ShopContext::getShopId();
        $product = Product::withTrashed()
            ->where('slug', $slug)
            ->where('shop_id', $shopId)
            ->with(['variations', 'images', 'categories'])
            ->firstOrFail();

        return response()->json($product);
    }

    public function update(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:standard,meat,frozen,fresh,perishable',
            'variations' => 'nullable|array',
            'variations.*.size' => 'required_with:variations|string',
            'variations.*.size_unit' => 'required_with:variations|string',
            'variations.*.price' => 'required_with:variations|numeric|min:0',
            'variations.*.stock_quantity' => 'required_with:variations|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Update product basic info
            $product->update($request->except(['categories', 'variations']));

            // Sync categories
            if ($request->has('categories')) {
                $product->categories()->sync($request->categories);
            }

            // Handle variations
            if ($request->has('variations')) {
                $variationsData = $request->variations;
                
                foreach ($variationsData as $variationData) {
                    // Auto-generate name if not provided
                    if (empty($variationData['name']) && isset($variationData['size']) && isset($variationData['size_unit'])) {
                        $variationData['name'] = $variationData['size'] . ' ' . $variationData['size_unit'];
                    }
                    
                    // Check if this is a delete operation
                    if (isset($variationData['_delete']) && $variationData['_delete']) {
                        if (isset($variationData['id'])) {
                            $product->variations()->where('id', $variationData['id'])->delete();
                        }
                        continue;
                    }
                    
                    // Update existing or create new
                    if (isset($variationData['id'])) {
                        // Update existing variation
                        $variation = $product->variations()->find($variationData['id']);
                        if ($variation) {
                            $variation->update($variationData);
                        }
                    } else {
                        // Create new variation
                        $product->variations()->create($variationData);
                    }
                }
            }

            DB::commit();

            return response()->json($product->load(['categories', 'variations']), 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update product', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $product->delete();

        return response()->json(['message' => 'Product archived successfully']);
    }

    public function restore($slug)
    {
        $product = Product::onlyTrashed()->where('slug', $slug)->firstOrFail();
        $product->restore();

        return response()->json(['message' => 'Product restored successfully']);
    }

    public function forceDelete($slug)
    {
        $product = Product::withTrashed()->where('slug', $slug)->firstOrFail();
        
        // Delete images from S3
        foreach ($product->images as $image) {
            Storage::disk('s3')->delete($image->path);
        }
        
        $product->forceDelete();

        return response()->json(['message' => 'Product permanently deleted']);
    }

    public function uploadImage(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:5120', // 5MB
            'alt_text' => 'nullable|string|max:255',
            'is_primary' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $file = $request->file('image');
            $path = Storage::disk('s3')->put('products/' . $product->id, $file, 'public');
            $url = Storage::disk('s3')->url($path);

            // If this is primary, unset other primary images
            if ($request->is_primary) {
                ProductImage::where('product_id', $product->id)
                    ->update(['is_primary' => false]);
            }

            $image = ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'url' => $url,
                'alt_text' => $request->alt_text,
                'is_primary' => $request->is_primary ?? false,
            ]);

            return response()->json($image, 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to upload image', 'error' => $e->getMessage()], 500);
        }
    }
}
