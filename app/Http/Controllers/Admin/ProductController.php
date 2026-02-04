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

        // Check if any files were uploaded
        if (!$request->hasFile('image')) {
            return response()->json(['message' => 'No images provided'], 422);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'required|array|min:1|max:5', // Accept array of images
            'image.*' => 'required|file|image|mimes:jpeg,jpg,png,webp|max:5120', // 5MB per image
            'alt_text' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if adding these images would exceed the limit
        $currentImageCount = $product->images()->count();
        $files = $request->file('image');
        $newImagesCount = is_array($files) ? count($files) : 1;
        
        if ($currentImageCount + $newImagesCount > 5) {
            return response()->json([
                'message' => 'Cannot upload images. Maximum 5 images per product.',
                'current_count' => $currentImageCount,
                'limit' => 5
            ], 422);
        }

        try {
            $uploadedImages = [];
            $files = is_array($request->file('image')) ? $request->file('image') : [$request->file('image')];
            
            // Determine if this is the first image (should be primary)
            $isFirstImage = $currentImageCount === 0;
            
            foreach ($files as $index => $file) {
                // Generate unique filename
                $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                
                // Upload original image directly (no processing for now)
                // Don't specify 'public' visibility as bucket doesn't allow ACLs
                $path = Storage::disk('s3')->putFileAs(
                    'products/' . $product->id,
                    $file,
                    $filename
                );
                
                if (!$path) {
                    throw new \Exception('Failed to upload file to S3. Path returned empty.');
                }

                // For thumbnail, we'll use the same image for now
                // TODO: Add proper thumbnail generation when GD is available
                $thumbnailPath = $path;

                // Set first uploaded image as primary if no primary exists
                $isPrimary = $isFirstImage && $index === 0;

                // Get next order number (handle null case)
                $maxOrder = $product->images()->max('order');
                $nextOrder = $maxOrder !== null ? $maxOrder + 1 : 0;

                $image = ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'thumbnail_path' => $thumbnailPath,
                    'alt_text' => $request->alt_text,
                    'is_primary' => $isPrimary,
                    'order' => $nextOrder,
                ]);

                $uploadedImages[] = $image;
            }

            return response()->json([
                'message' => 'Images uploaded successfully',
                'images' => $uploadedImages,
                'count' => count($uploadedImages)
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Image upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'product_id' => $product->id ?? null,
                'file_count' => isset($files) ? count($files) : 0
            ]);
            
            return response()->json([
                'message' => 'Failed to upload images', 
                'error' => $e->getMessage(),
                'details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Process image: resize to max 1200x1200 and compress
     */
    private function processImage($file)
    {
        try {
            $image = imagecreatefromstring(file_get_contents($file));
            
            if (!$image) {
                throw new \Exception('Failed to process image');
            }

            $width = imagesx($image);
            $height = imagesy($image);
            
            // Calculate new dimensions (max 1200x1200 maintaining aspect ratio)
            $maxDimension = 1200;
            if ($width > $maxDimension || $height > $maxDimension) {
                if ($width > $height) {
                    $newWidth = $maxDimension;
                    $newHeight = intval($height * ($maxDimension / $width));
                } else {
                    $newHeight = $maxDimension;
                    $newWidth = intval($width * ($maxDimension / $height));
                }
            } else {
                $newWidth = $width;
                $newHeight = $height;
            }

            // Create resized image
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG and WebP
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
            imagefill($resizedImage, 0, 0, $transparent);
            
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            
            // Save to temporary file as JPEG
            $tempPath = tempnam(sys_get_temp_dir(), 'img_');
            imagejpeg($resizedImage, $tempPath, 85); // 85% quality
            
            imagedestroy($image);
            imagedestroy($resizedImage);
            
            $contents = file_get_contents($tempPath);
            unlink($tempPath); // Clean up temp file
            
            return $contents;
        } catch (\Exception $e) {
            \Log::error('Image processing failed: ' . $e->getMessage());
            throw new \Exception('Image processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Create thumbnail: 105x150 maintaining aspect ratio
     */
    private function createThumbnail($file)
    {
        try {
            $image = imagecreatefromstring(file_get_contents($file));
            
            if (!$image) {
                throw new \Exception('Failed to create thumbnail');
            }

            $width = imagesx($image);
            $height = imagesy($image);
            
            // Target dimensions
            $thumbWidth = 105;
            $thumbHeight = 150;
            
            // Calculate scaling to maintain aspect ratio
            $widthRatio = $thumbWidth / $width;
            $heightRatio = $thumbHeight / $height;
            $ratio = min($widthRatio, $heightRatio);
            
            $newWidth = intval($width * $ratio);
            $newHeight = intval($height * $ratio);
            
            // Create thumbnail
            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
            imagefill($thumbnail, 0, 0, $transparent);
            
            imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            
            // Save to temporary file as JPEG
            $tempPath = tempnam(sys_get_temp_dir(), 'thumb_');
            imagejpeg($thumbnail, $tempPath, 85);
            
            imagedestroy($image);
            imagedestroy($thumbnail);
            
            $contents = file_get_contents($tempPath);
            unlink($tempPath); // Clean up temp file
            
            return $contents;
        } catch (\Exception $e) {
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
            throw new \Exception('Thumbnail creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete a product image
     */
    public function deleteImage($slug, $imageId)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $image = ProductImage::where('id', $imageId)
            ->where('product_id', $product->id)
            ->firstOrFail();

        try {
            // Delete files from S3
            Storage::disk('s3')->delete($image->path);
            Storage::disk('s3')->delete($image->thumbnail_path);

            $wasPrimary = $image->is_primary;
            
            // Delete from database
            $image->delete();

            // If deleted image was primary, promote the next image
            if ($wasPrimary) {
                $nextImage = ProductImage::where('product_id', $product->id)
                    ->orderBy('order')
                    ->first();
                    
                if ($nextImage) {
                    $nextImage->update(['is_primary' => true]);
                }
            }

            return response()->json([
                'message' => 'Image deleted successfully',
                'promoted_to_primary' => $wasPrimary && isset($nextImage) ? $nextImage->id : null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set an image as primary
     */
    public function setPrimaryImage($slug, $imageId)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $image = ProductImage::where('id', $imageId)
            ->where('product_id', $product->id)
            ->firstOrFail();

        try {
            // Unset all primary flags for this product
            ProductImage::where('product_id', $product->id)
                ->update(['is_primary' => false]);

            // Set this image as primary
            $image->update(['is_primary' => true]);

            return response()->json([
                'message' => 'Primary image updated successfully',
                'image' => $image
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update primary image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder images
     */
    public function reorderImages(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*.id' => 'required|exists:product_images,id',
            'images.*.order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            foreach ($request->images as $imageData) {
                ProductImage::where('id', $imageData['id'])
                    ->where('product_id', $product->id)
                    ->update(['order' => $imageData['order']]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Images reordered successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to reorder images',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
