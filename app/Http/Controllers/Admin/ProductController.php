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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $product = Product::create(array_merge(
                $request->except(['categories']),
                ['shop_id' => \App\Services\ShopContext::getShopId()]
            ));

            // Attach categories
            $product->categories()->attach($request->categories);

            DB::commit();

            return response()->json($product->load(['categories', 'variations']), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create product', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $product = Product::withTrashed()->with(['variations', 'images', 'categories'])->findOrFail($id);

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:standard,meat,frozen,fresh,perishable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $product->update($request->except(['categories']));

            if ($request->has('categories')) {
                $product->categories()->sync($request->categories);
            }

            DB::commit();

            return response()->json($product->load(['categories', 'variations']));

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update product', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product archived successfully']);
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return response()->json(['message' => 'Product restored successfully']);
    }

    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        
        // Delete images from S3
        foreach ($product->images as $image) {
            Storage::disk('s3')->delete($image->path);
        }
        
        $product->forceDelete();

        return response()->json(['message' => 'Product permanently deleted']);
    }

    public function uploadImage(Request $request, $id)
    {
        $product = Product::findOrFail($id);

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
