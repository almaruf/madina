<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $shopId = \App\Services\ShopContext::getShopId();
        $query = Category::where('shop_id', $shopId)->with('children')->withCount('products');

        // Handle archived filter
        if ($request->has('archived') && $request->archived == '1') {
            $query->onlyTrashed();
        }

        $categories = $query->get();

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = Category::create(array_merge(
            $request->all(),
            ['shop_id' => \App\Services\ShopContext::getShopId()]
        ));

        return response()->json($category, 201);
    }

    public function show($slug)
    {
        $category = Category::withTrashed()
            ->where('slug', $slug)
            ->with(['children', 'parent', 'products.variations', 'products.primaryImage'])
            ->withCount('products')
            ->firstOrFail();

        return response()->json($category);
    }

    public function update(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category->update($request->all());

        return response()->json($category);
    }

    public function destroy($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $category->delete();

        return response()->json(['message' => 'Category archived successfully']);
    }

    public function restore($slug)
    {
        $category = Category::onlyTrashed()->where('slug', $slug)->firstOrFail();
        $category->restore();

        return response()->json(['message' => 'Category restored successfully']);
    }

    public function forceDelete($slug)
    {
        $category = Category::withTrashed()->where('slug', $slug)->firstOrFail();
        $category->forceDelete();

        return response()->json(['message' => 'Category permanently deleted']);
    }

    /**
     * Upload category image (only 1 allowed)
     */
    public function uploadImage(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max
            'alt_text' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if category already has an image
        if ($category->path) {
            return response()->json([
                'message' => 'Category already has an image. Please delete it first.',
            ], 422);
        }

        try {
            $file = $request->file('image');
            
            // Generate unique filename
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Upload to S3
            $path = Storage::disk('s3')->putFileAs(
                'categories/' . $category->id,
                $file,
                $filename
            );
            
            if (!$path) {
                throw new \Exception('Failed to upload file to S3. Path returned empty.');
            }

            // Generate full S3 URLs
            $url = Storage::disk('s3')->url($path);
            
            // For thumbnail, use the same image for now
            $thumbnailPath = $path;
            $thumbnailUrl = $url;

            // Update category with image info
            $category->update([
                'path' => $path,
                'url' => $url,
                'thumbnail_path' => $thumbnailPath,
                'thumbnail_url' => $thumbnailUrl,
            ]);

            return response()->json([
                'message' => 'Image uploaded successfully',
                'category' => $category
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Category image upload failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'message' => 'Failed to upload image',
                'error' => $e->getMessage(),
                'details' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Delete category image
     */
    public function deleteImage($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        if (!$category->path) {
            return response()->json(['message' => 'No image to delete'], 404);
        }

        try {
            // Delete from S3
            if ($category->path) {
                Storage::disk('s3')->delete($category->path);
            }
            
            // Clear image fields
            $category->update([
                'path' => null,
                'url' => null,
                'thumbnail_path' => null,
                'thumbnail_url' => null,
            ]);

            return response()->json(['message' => 'Image deleted successfully']);

        } catch (\Exception $e) {
            \Log::error('Category image deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to delete image',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
