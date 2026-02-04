<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopBanner;
use App\Services\ShopContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function __construct()
    {
        // Only super admins can create, update, delete shops
        // Admins can view shops
        // Shop admins (including owner/staff) can manage banners
        $this->middleware('super_admin')->except([
            'current', 
            'updateCurrent', 
            'index',
            'show',
            'uploadBanner',
            'deleteBanner',
            'setPrimaryBanner',
            'reorderBanners',
            'selectedShop',
            'setSelectedShop'
        ]);
    }

    public function index(Request $request)
    {
        $query = Shop::query();

        // Handle archived filter
        if ($request->has('archived') && $request->archived == '1') {
            $query->onlyTrashed();
        }

        $shops = $query->paginate(50);
        return response()->json($shops);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shops,slug',
            'domain' => 'nullable|string|unique:shops,domain',
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postcode' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'description' => 'nullable|string',
            'specialization' => 'nullable|string',
            'has_halal_products' => 'nullable|boolean',
            'delivery_fee' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $shop = Shop::create($request->all());

        ShopContext::clearCache();

        return response()->json($shop, 201);
    }

    public function show(Shop $shop)
    {
        $shop->load('banners');
        return response()->json($shop);
    }

    public function update(Request $request, Shop $shop)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'legal_company_name' => 'nullable|string|max:255',
            'company_registration_number' => 'nullable|string|max:100',
            'slug' => 'sometimes|required|string|max:255|unique:shops,slug,' . $shop->id,
            'domain' => 'nullable|string|unique:shops,domain,' . $shop->id,
            'phone' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email',
            'is_active' => 'sometimes|boolean',
            'vat_registered' => 'sometimes|boolean',
            'vat_number' => 'nullable|string|max:50',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'prices_include_vat' => 'sometimes|boolean',
            'delivery_enabled' => 'sometimes|boolean',
            'collection_enabled' => 'sometimes|boolean',
            'online_payment' => 'sometimes|boolean',
            'has_halal_products' => 'sometimes|boolean',
            'has_organic_products' => 'sometimes|boolean',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_sort_code' => 'nullable|string|max:20',
            'bank_iban' => 'nullable|string|max:50',
            'bank_swift_code' => 'nullable|string|max:20',
            // Operating hours validation
            'monday_open' => 'nullable|date_format:H:i',
            'monday_close' => 'nullable|date_format:H:i',
            'monday_closed' => 'nullable|boolean',
            'tuesday_open' => 'nullable|date_format:H:i',
            'tuesday_close' => 'nullable|date_format:H:i',
            'tuesday_closed' => 'nullable|boolean',
            'wednesday_open' => 'nullable|date_format:H:i',
            'wednesday_close' => 'nullable|date_format:H:i',
            'wednesday_closed' => 'nullable|boolean',
            'thursday_open' => 'nullable|date_format:H:i',
            'thursday_close' => 'nullable|date_format:H:i',
            'thursday_closed' => 'nullable|boolean',
            'friday_open' => 'nullable|date_format:H:i',
            'friday_close' => 'nullable|date_format:H:i',
            'friday_closed' => 'nullable|boolean',
            'saturday_open' => 'nullable|date_format:H:i',
            'saturday_close' => 'nullable|date_format:H:i',
            'saturday_closed' => 'nullable|boolean',
            'sunday_open' => 'nullable|date_format:H:i',
            'sunday_close' => 'nullable|date_format:H:i',
            'sunday_closed' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get validated data
        $data = $request->all();
        
        // Explicitly handle boolean fields to ensure they update even when false
        $booleanFields = ['vat_registered', 'prices_include_vat', 'is_active', 'delivery_enabled', 
                          'collection_enabled', 'online_payment', 'has_halal_products', 'has_organic_products',
                          'monday_closed', 'tuesday_closed', 'wednesday_closed', 'thursday_closed',
                          'friday_closed', 'saturday_closed', 'sunday_closed'];
        
        foreach ($booleanFields as $field) {
            if ($request->has($field)) {
                $value = $request->input($field);
                // Convert to boolean: true/1/"1"/"true" -> true, false/0/"0"/"false"/null -> false
                $data[$field] = ($value === true || $value === 1 || $value === '1' || $value === 'true');
            } else {
                // If not present in request (checkbox unchecked), set to false
                if (str_ends_with($field, '_closed')) {
                    $data[$field] = false;
                }
            }
        }
        
        // If a day is marked as closed, clear the open/close times
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days as $day) {
            if (isset($data["{$day}_closed"]) && $data["{$day}_closed"] === true) {
                $data["{$day}_open"] = null;
                $data["{$day}_close"] = null;
            }
        }

        $shop->update($data);

        ShopContext::clearCache();

        return response()->json($shop);
    }

    public function current()
    {
        $shop = ShopContext::getShop();

        if (!$shop) {
            return response()->json(['message' => 'No shop context'], 404);
        }

        return response()->json($shop);
    }

    public function updateCurrent(Request $request)
    {
        $shop = ShopContext::getShop();

        if (!$shop) {
            return response()->json(['message' => 'No shop context'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'phone' => 'string|max:20',
            'email' => 'email',
            'delivery_fee' => 'nullable|numeric',
            'min_order_amount' => 'nullable|numeric',
            'primary_color' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $shop->update($request->all());

        ShopContext::clearCache();

        return response()->json($shop);
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();

        ShopContext::clearCache();

        return response()->json(['message' => 'Shop archived successfully']);
    }

    public function restore($slug)
    {
        $shop = Shop::withTrashed()->where('slug', $slug)->firstOrFail();
        $shop->restore();

        ShopContext::clearCache();

        return response()->json(['message' => 'Shop restored successfully']);
    }

    public function forceDelete($slug)
    {
        $shop = Shop::withTrashed()->where('slug', $slug)->firstOrFail();
        $shop->forceDelete();

        ShopContext::clearCache();

        return response()->json(['message' => 'Shop permanently deleted']);
    }

    /**
     * Get selected shop from session
     */
    public function selectedShop(Request $request)
    {
        $selectedShopId = session('admin_selected_shop_id');
        
        if (!$selectedShopId) {
            return response()->json(['shop_id' => null, 'shop' => null]);
        }

        $shop = Shop::find($selectedShopId);
        
        return response()->json([
            'shop_id' => $selectedShopId,
            'shop' => $shop
        ]);
    }

    /**
     * Set selected shop in session
     */
    public function setSelectedShop(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_id' => 'nullable|integer|exists:shops,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->shop_id) {
            $request->session()->put('admin_selected_shop_id', $request->shop_id);
        } else {
            $request->session()->forget('admin_selected_shop_id');
        }

        return response()->json(['message' => 'Shop selection updated']);
    }

    /**
     * Upload banner images for shop
     */
    public function uploadBanner(Request $request, Shop $shop)
    {
        // Owner/staff can only manage their own shop
        $user = $request->user();
        if (in_array($user->role, ['owner', 'staff']) && $user->shop_id !== $shop->id) {
            return response()->json(['message' => 'Unauthorized. You can only manage your own shop.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'image.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check current banner count
        $currentBannerCount = $shop->banners()->count();
        
        // Get files (support both single and multiple uploads)
        $files = is_array($request->file('image')) ? $request->file('image') : [$request->file('image')];
        
        // Check if adding these files would exceed limit
        if ($currentBannerCount + count($files) > 5) {
            return response()->json([
                'message' => 'Cannot upload images. Maximum 5 banners allowed per shop.',
                'current_count' => $currentBannerCount,
                'attempted_upload' => count($files)
            ], 422);
        }

        DB::beginTransaction();

        try {
            $uploadedBanners = [];
            
            // Determine if this is the first banner (should be primary)
            $isFirstBanner = $currentBannerCount === 0;
            
            foreach ($files as $index => $file) {
                // Generate unique filename
                $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                
                // Upload original image to S3
                $path = Storage::disk('s3')->putFileAs(
                    'banners/' . $shop->id,
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

                // Set first uploaded banner as primary if no primary exists
                $isPrimary = $isFirstBanner && $index === 0;

                // Get next order number
                $maxOrder = $shop->banners()->max('order');
                $nextOrder = $maxOrder !== null ? $maxOrder + 1 : 0;

                $banner = ShopBanner::create([
                    'shop_id' => $shop->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'path' => $path,
                    'url' => $url,
                    'thumbnail_path' => $thumbnailPath,
                    'thumbnail_url' => $thumbnailUrl,
                    'link' => $request->link,
                    'is_primary' => $isPrimary,
                    'order' => $nextOrder,
                    'is_active' => true,
                ]);

                $uploadedBanners[] = $banner;
            }

            DB::commit();

            return response()->json([
                'message' => 'Banners uploaded successfully',
                'banners' => $uploadedBanners,
                'count' => count($uploadedBanners)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Banner upload failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'message' => 'Failed to upload banners',
                'error' => $e->getMessage(),
                'details' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Delete a banner
     */
    public function deleteBanner(Request $request, Shop $shop, $bannerId)
    {
        // Owner/staff can only manage their own shop
        $user = $request->user();
        if (in_array($user->role, ['owner', 'staff']) && $user->shop_id !== $shop->id) {
            return response()->json(['message' => 'Unauthorized. You can only manage your own shop.'], 403);
        }

        $banner = ShopBanner::where('shop_id', $shop->id)
                           ->where('id', $bannerId)
                           ->firstOrFail();

        DB::beginTransaction();

        try {
            $wasPrimary = $banner->is_primary;

            // Delete from S3
            if ($banner->path) {
                Storage::disk('s3')->delete($banner->path);
            }
            
            // Delete banner record
            $banner->delete();

            // If deleted banner was primary, promote the next one
            if ($wasPrimary) {
                $nextBanner = ShopBanner::where('shop_id', $shop->id)
                                       ->orderBy('order')
                                       ->first();
                
                if ($nextBanner) {
                    $nextBanner->update(['is_primary' => true]);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Banner deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Banner deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to delete banner',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set a banner as primary
     */
    public function setPrimaryBanner(Request $request, Shop $shop, $bannerId)
    {
        // Owner/staff can only manage their own shop
        $user = $request->user();
        if (in_array($user->role, ['owner', 'staff']) && $user->shop_id !== $shop->id) {
            return response()->json(['message' => 'Unauthorized. You can only manage your own shop.'], 403);
        }

        $banner = ShopBanner::where('shop_id', $shop->id)
                           ->where('id', $bannerId)
                           ->firstOrFail();

        DB::beginTransaction();

        try {
            // Remove primary flag from all banners
            ShopBanner::where('shop_id', $shop->id)
                     ->update(['is_primary' => false]);
            
            // Set this banner as primary
            $banner->update(['is_primary' => true]);

            DB::commit();

            return response()->json([
                'message' => 'Primary banner updated successfully',
                'banner' => $banner
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Set primary banner failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to set primary banner',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder banners
     */
    public function reorderBanners(Request $request, Shop $shop)
    {
        // Owner/staff can only manage their own shop
        $user = $request->user();
        if (in_array($user->role, ['owner', 'staff']) && $user->shop_id !== $shop->id) {
            return response()->json(['message' => 'Unauthorized. You can only manage your own shop.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'banners' => 'required|array',
            'banners.*.id' => 'required|integer|exists:shop_banners,id',
            'banners.*.order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            foreach ($request->banners as $bannerData) {
                ShopBanner::where('id', $bannerData['id'])
                         ->where('shop_id', $shop->id)
                         ->update(['order' => $bannerData['order']]);
            }

            DB::commit();

            return response()->json(['message' => 'Banners reordered successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Banner reordering failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to reorder banners',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
