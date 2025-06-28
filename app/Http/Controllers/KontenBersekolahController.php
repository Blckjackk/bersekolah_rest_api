<?php

namespace App\Http\Controllers;

use App\Models\KontenBersekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class KontenBersekolahController extends Controller
{
    /**
     * Display a listing of the resource for public display (only published).
     */
    public function index(Request $request)
    {
        $query = KontenBersekolah::query();
        
        // For public endpoint, only show published content
        if (!$request->is('api/admin*')) {
            $query->where('status', 'published');
        }

        // Add search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul_halaman', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Add category filter
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $konten = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Konten berhasil diambil',
            'data' => $konten
        ]);
    }

    /**
     * Display the specified resource for public display.
     */
    public function show($id)
    {
        // Try to find by ID
        $konten = KontenBersekolah::where(function($query) use ($id) {
                $query->where('id', $id)
                      ->orWhere('slug', $id);
            })
            ->where('status', 'published')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Konten berhasil diambil',
            'data' => $konten
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'judul_halaman' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:konten_bersekolah',
            'deskripsi' => 'required|string',
            'category' => 'required|string|max:100',
            'status' => 'required|in:draft,published,archived',
            'gambar' => 'nullable|image|max:2048', // 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            
            // Handle image upload if provided
            if ($request->hasFile('gambar')) {
                $image = $request->file('gambar');
                $filename = time() . '_' . Str::slug($request->judul_halaman) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('public/uploads/konten', $filename);
                $data['gambar'] = Storage::url($path);
            }
            
            // Set user_id to the authenticated user
            $data['user_id'] = Auth::id();
            
            $konten = KontenBersekolah::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Konten berhasil dibuat',
                'data' => $konten
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat konten',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the konten
        $konten = KontenBersekolah::findOrFail($id);
        
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'judul_halaman' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:konten_bersekolah,slug,' . $id,
            'deskripsi' => 'required|string',
            'category' => 'required|string|max:100',
            'status' => 'required|in:draft,published,archived',
            'gambar' => 'nullable|image|max:2048', // 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            
            // Handle image upload if provided
            if ($request->hasFile('gambar')) {
                // Delete old image if exists
                if ($konten->gambar && Storage::exists(str_replace('/storage', 'public', $konten->gambar))) {
                    Storage::delete(str_replace('/storage', 'public', $konten->gambar));
                }
                
                // Upload new image
                $image = $request->file('gambar');
                $filename = time() . '_' . Str::slug($request->judul_halaman) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('public/uploads/konten', $filename);
                $data['gambar'] = Storage::url($path);
            }
            
            $konten->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Konten berhasil diperbarui',
                'data' => $konten
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui konten',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $konten = KontenBersekolah::findOrFail($id);
            
            // Delete associated image if exists
            if ($konten->gambar && Storage::exists(str_replace('/storage', 'public', $konten->gambar))) {
                Storage::delete(str_replace('/storage', 'public', $konten->gambar));
            }
            
            $konten->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Konten berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus konten',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
