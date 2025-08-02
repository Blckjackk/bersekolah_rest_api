<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use Illuminate\Http\Request;

class MentorController extends Controller
{
    // Get all mentors
    public function index()
    {
        $mentors = Mentor::all();
        $mentors->transform(function($mentor) {
            $mentor->photo_url = $mentor->photo_url;
            return $mentor;
        });
        return response()->json([
            'data' => $mentors,
            'total' => Mentor::count()
        ]);
    }

    // Get total mentors only
    public function total()
    {
        return response()->json([
            'total' => Mentor::count()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:mentors,email',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:10240', // max 10MB
        ]);
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('admin/mentor', 'public');
            $data['photo'] = basename($photoPath);
        }
        $mentor = Mentor::create($data);
        $mentor->photo_url = $mentor->photo_url;
        return response()->json($mentor, 201);
    }

    // Show mentor by id
    public function show($id)
    {
        $mentor = Mentor::findOrFail($id);
        $mentor->photo_url = $mentor->photo_url;
        return response()->json($mentor);
    }

    // Update mentor
    public function update(Request $request, $id)
    {
        $mentor = Mentor::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:mentors,email,' . $id,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
        ]);
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($mentor->photo) {
                $oldPath = storage_path('app/public/admin/mentor/' . basename($mentor->photo));
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $photoPath = $request->file('photo')->store('admin/mentor', 'public');
            $data['photo'] = basename($photoPath);
        }
        $mentor->update($data);
        $mentor->photo_url = $mentor->photo_url;
        return response()->json($mentor);
    }


    // Delete mentor
    public function destroy($id)
    {
        $mentor = Mentor::findOrFail($id);
        $mentor->delete();
        return response()->json(['message' => 'Mentor deleted']);
    }
}
