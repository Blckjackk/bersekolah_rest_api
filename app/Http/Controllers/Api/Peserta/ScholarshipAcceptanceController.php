<?php

namespace App\Http\Controllers\Api\Peserta;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipAcceptance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScholarshipAcceptanceController extends Controller
{
    /**
     * Get scholarship acceptance status for the authenticated user
     */
    public function getStatus(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 401);
            }

            $acceptance = ScholarshipAcceptance::where('user_id', $user->id)->first();

            // If no record exists, create default one
            if (!$acceptance) {
                $acceptance = ScholarshipAcceptance::create([
                    'user_id' => $user->id,
                    'has_accepted_scholarship' => false,
                    'has_joined_whatsapp_group' => false,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'has_accepted_scholarship' => $acceptance->has_accepted_scholarship,
                    'has_joined_whatsapp_group' => $acceptance->has_joined_whatsapp_group,
                    'accepted_at' => $acceptance->accepted_at?->toISOString(),
                    'joined_group_at' => $acceptance->joined_group_at?->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get acceptance status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accept scholarship for the authenticated user
     */
    public function acceptScholarship(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 401);
            }

            $acceptance = ScholarshipAcceptance::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'has_accepted_scholarship' => true,
                    'accepted_at' => Carbon::now(),
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Scholarship accepted successfully',
                'data' => [
                    'has_accepted_scholarship' => $acceptance->has_accepted_scholarship,
                    'has_joined_whatsapp_group' => $acceptance->has_joined_whatsapp_group,
                    'accepted_at' => $acceptance->accepted_at?->toISOString(),
                    'joined_group_at' => $acceptance->joined_group_at?->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to accept scholarship',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark as joined WhatsApp group for the authenticated user
     */
    public function joinWhatsAppGroup(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 401);
            }

            $acceptance = ScholarshipAcceptance::where('user_id', $user->id)->first();

            // Must accept scholarship first
            if (!$acceptance || !$acceptance->has_accepted_scholarship) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Must accept scholarship first before joining WhatsApp group'
                ], 400);
            }

            $acceptance->update([
                'has_joined_whatsapp_group' => true,
                'joined_group_at' => Carbon::now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Joined WhatsApp group successfully',
                'data' => [
                    'has_accepted_scholarship' => $acceptance->has_accepted_scholarship,
                    'has_joined_whatsapp_group' => $acceptance->has_joined_whatsapp_group,
                    'accepted_at' => $acceptance->accepted_at?->toISOString(),
                    'joined_group_at' => $acceptance->joined_group_at?->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to join WhatsApp group',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
