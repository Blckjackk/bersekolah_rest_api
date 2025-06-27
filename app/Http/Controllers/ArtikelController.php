<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use Illuminate\Http\Request;

class ArtikelController extends Controller
{
    // Get all articles
    public function index()
    {
        $articles = Artikel::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $articles
        ]);
    }

    // Get total articles count
    public function total()
    {
        return response()->json([
            'total' => Artikel::count()
        ]);
    }

    // Create new article
    public function store(Request $request)
    {
        $data = $request->validate([
            'judul_halaman' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:konten_bersekolah,slug',
            'deskripsi' => 'required|string',
            'category' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:10240', // max 10MB
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $originalName = $file->getClientOriginalName();
            
            // Use absolute path to artikel directory
            $destinationPath = 'C:\Users\mp2k5\Documents\GitHub\Project_Prokon\bersekolah_website\public\assets\image\artikel';
            
            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $originalName);
            
            // Store only filename in database
            $data['gambar'] = $originalName;
        }

        // Add user_id (you can get this from auth user or set a default)
        $data['user_id'] = $request->user_id ?? 1; // Default to user 1 for now

        $artikel = Artikel::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Artikel created successfully',
            'data' => $artikel
        ], 201);
    }

    // Show article by id
    public function show($id)
    {
        $artikel = Artikel::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $artikel
        ]);
    }

    // Update article
    public function update(Request $request, $id)
    {
        $artikel = Artikel::findOrFail($id);

        $data = $request->validate([
            'judul_halaman' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:konten_bersekolah,slug,' . $id,
            'deskripsi' => 'sometimes|string',
            'category' => 'sometimes|string',
            'status' => 'sometimes|in:draft,published,archived',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:10240', // 10MB
        ]);

        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($artikel->gambar) {
                $oldImagePath = 'C:\Users\mp2k5\Documents\GitHub\Project_Prokon\bersekolah_website\public\assets\image\artikel\\' . basename($artikel->gambar);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            $file = $request->file('gambar');
            $originalName = $file->getClientOriginalName();
            
            // Use absolute path to artikel directory
            $destinationPath = 'C:\Users\mp2k5\Documents\GitHub\Project_Prokon\bersekolah_website\public\assets\image\artikel';
            
            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $originalName);
            
            // Store only filename in database
            $data['gambar'] = $originalName;
        }

        $artikel->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Artikel updated successfully',
            'data' => $artikel
        ]);
    }

    // Delete article
    public function destroy($id)
    {
        $artikel = Artikel::findOrFail($id);
        
        // Delete image if exists
        if ($artikel->gambar) {
            $imagePath = 'C:\Users\mp2k5\Documents\GitHub\Project_Prokon\bersekolah_website\public\assets\image\artikel\\' . basename($artikel->gambar);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $artikel->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Artikel deleted successfully'
        ]);
    }

    // Update article status
    public function updateStatus(Request $request, $id)
    {
        $artikel = Artikel::findOrFail($id);
        
        $data = $request->validate([
            'status' => 'required|in:draft,published,archived'
        ]);
        
        $artikel->update($data);
        
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data' => $artikel
        ]);
    }
}
