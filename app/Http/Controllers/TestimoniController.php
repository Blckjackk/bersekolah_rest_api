<?php

namespace App\Http\Controllers;

use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TestimoniController extends Controller
{
    /**
     * Display a listing of the resource.
     * For public access: only active testimonials
     * For admin access: all testimonials
     */
    public function index(Request $request)
    {
        try {
            // Check if this is an admin request (has auth token)
            $user = Auth::user();
            
            if ($user && in_array($user->role, ['admin', 'superadmin'])) {
                // Admin can see all testimonials
                $testimoni = Testimoni::orderBy('tanggal_input', 'desc')->get();
            } else {
                // Public can only see active testimonials
                $testimoni = Testimoni::where('status', 'active')
                    ->orderBy('tanggal_input', 'desc')
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $testimoni,
                'total' => $testimoni->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching testimonials: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch testimonials'
            ], 500);
        }
    }

    /**
     * Get total count of testimonials
     */
    public function total(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user && in_array($user->role, ['admin', 'superadmin'])) {
                $total = Testimoni::count();
                $active = Testimoni::where('status', 'active')->count();
                $inactive = Testimoni::where('status', 'inactive')->count();
            } else {
                $total = Testimoni::where('status', 'active')->count();
                $active = $total;
                $inactive = 0;
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'active' => $active,
                    'inactive' => $inactive
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting testimoni count: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get testimoni count'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama' => 'required|string|max:100',
                'angkatan_beswan' => 'required|string|max:20',
                'sekarang_dimana' => 'nullable|string|max:255',
                'isi_testimoni' => 'required|string',
                'foto_testimoni' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'status' => 'in:active,inactive'
            ]);

            if ($request->hasFile('foto_testimoni')) {
                // Get the uploaded file
                $file = $request->file('foto_testimoni');
                
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Define the path to save in frontend assets
                $frontendPath = base_path('../bersekolah_website/public/assets/image/testimoni/');
                
                // Create directory if it doesn't exist
                if (!file_exists($frontendPath)) {
                    mkdir($frontendPath, 0755, true);
                }
                
                // Move file to frontend directory
                $file->move($frontendPath, $filename);
                
                // Only save filename to database
                $validatedData['foto_testimoni'] = $filename;
            }

            $validatedData['status'] = $validatedData['status'] ?? 'inactive';
            $validatedData['tanggal_input'] = now();

            $testimoni = Testimoni::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Testimoni created successfully.',
                'data' => $testimoni
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating testimoni: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create testimoni'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimoni $testimoni)
    {
        return response()->json([
            'success' => true,
            'data' => $testimoni
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimoni $testimoni)
    {
        try {
            $validatedData = $request->validate([
                'nama' => 'sometimes|required|string|max:100',
                'angkatan_beswan' => 'sometimes|required|string|max:20',
                'sekarang_dimana' => 'nullable|string|max:255',
                'isi_testimoni' => 'sometimes|required|string',
                'foto_testimoni' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'status' => 'in:active,inactive'
            ]);

            if ($request->hasFile('foto_testimoni')) {
                // Delete old image if exists
                if ($testimoni->foto_testimoni && $testimoni->foto_testimoni !== 'default.jpg') {
                    $oldImagePath = base_path('../bersekolah_website/public/assets/image/testimoni/' . $testimoni->foto_testimoni);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Get the uploaded file
                $file = $request->file('foto_testimoni');
                
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Define the path to save in frontend assets
                $frontendPath = base_path('../bersekolah_website/public/assets/image/testimoni/');
                
                // Create directory if it doesn't exist
                if (!file_exists($frontendPath)) {
                    mkdir($frontendPath, 0755, true);
                }
                
                // Move file to frontend directory
                $file->move($frontendPath, $filename);
                
                // Only save filename to database
                $validatedData['foto_testimoni'] = $filename;
            }

            $testimoni->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Testimoni updated successfully.',
                'data' => $testimoni->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating testimoni: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update testimoni'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimoni $testimoni)
    {
        try {
            // Delete image file if exists
            if ($testimoni->foto_testimoni && $testimoni->foto_testimoni !== 'default.jpg') {
                $imagePath = base_path('../bersekolah_website/public/assets/image/testimoni/' . $testimoni->foto_testimoni);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $testimoni->delete();

            return response()->json([
                'success' => true,
                'message' => 'Testimoni deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting testimoni: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete testimoni'
            ], 500);
        }
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, Testimoni $testimoni)
    {
        try {
            $validatedData = $request->validate([
                'status' => 'required|in:active,inactive'
            ]);

            $testimoni->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Testimoni status updated successfully.',
                'data' => $testimoni->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating testimoni status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update testimoni status'
            ], 500);
        }
    }
}
