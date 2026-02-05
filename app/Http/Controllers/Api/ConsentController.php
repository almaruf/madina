<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserConsent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsentController extends Controller
{
    /**
     * Get user's current consents
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get all consent types
        $validTypes = UserConsent::getValidTypes();
        
        // Get existing consents
        $existingConsents = $user->consents()->get()->keyBy('consent_type');
        
        // Build response with all consent types
        $consents = [];
        foreach ($validTypes as $type) {
            if (isset($existingConsents[$type])) {
                $consent = $existingConsents[$type];
                $consents[] = [
                    'type' => $type,
                    'is_granted' => $consent->is_granted,
                    'granted_at' => $consent->granted_at,
                    'revoked_at' => $consent->revoked_at,
                    'updated_at' => $consent->updated_at,
                ];
            } else {
                // Default: not granted
                $consents[] = [
                    'type' => $type,
                    'is_granted' => false,
                    'granted_at' => null,
                    'revoked_at' => null,
                    'updated_at' => null,
                ];
            }
        }
        
        return response()->json([
            'consents' => $consents,
        ]);
    }

    /**
     * Update user's consent preferences
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'consents' => 'required|array',
            'consents.*.type' => 'required|string|in:' . implode(',', UserConsent::getValidTypes()),
            'consents.*.is_granted' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updatedConsents = [];
        
        foreach ($request->consents as $consentData) {
            $type = $consentData['type'];
            $isGranted = $consentData['is_granted'];
            
            // Update or create consent
            $consent = UserConsent::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'consent_type' => $type,
                ],
                [
                    'is_granted' => $isGranted,
                    'granted_at' => $isGranted ? now() : null,
                    'revoked_at' => $isGranted ? null : now(),
                ]
            );
            
            $updatedConsents[] = [
                'type' => $consent->consent_type,
                'is_granted' => $consent->is_granted,
                'granted_at' => $consent->granted_at,
                'revoked_at' => $consent->revoked_at,
                'updated_at' => $consent->updated_at,
            ];
        }

        return response()->json([
            'message' => 'Consent preferences updated successfully',
            'consents' => $updatedConsents,
        ]);
    }

    /**
     * Update a single consent
     */
    public function updateSingle(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:' . implode(',', UserConsent::getValidTypes()),
            'is_granted' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $type = $request->type;
        $isGranted = $request->is_granted;
        
        // Update or create consent
        $consent = UserConsent::updateOrCreate(
            [
                'user_id' => $user->id,
                'consent_type' => $type,
            ],
            [
                'is_granted' => $isGranted,
                'granted_at' => $isGranted ? now() : null,
                'revoked_at' => $isGranted ? null : now(),
            ]
        );
        
        return response()->json([
            'message' => 'Consent updated successfully',
            'consent' => [
                'type' => $consent->consent_type,
                'is_granted' => $consent->is_granted,
                'granted_at' => $consent->granted_at,
                'revoked_at' => $consent->revoked_at,
                'updated_at' => $consent->updated_at,
            ],
        ]);
    }
}
