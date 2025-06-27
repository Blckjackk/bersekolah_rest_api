<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MentorController extends Controller
{
    // Get all mentors
    public function index()
    {
        return response()->json([
            'data' => Mentor::all(),
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
            $file = $request->file('photo');
            $originalName = $file->getClientOriginalName();
            
            // Use absolute path to mentor directory (more reliable)
            $destinationPath = 'C:\Users\mp2k5\Documents\GitHub\Project_Prokon\bersekolah_website\public\assets\image\mentor';
            
            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $originalName);
            
            // Store only filename in database (no mentor/ prefix)
            $data['photo'] = $originalName;
        }

        $mentor = Mentor::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Mentor created successfully',
            'data' => $mentor
        ], 201);
    }

    // Show mentor by id
    public function show($id)
    {
        $mentor = Mentor::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $mentor
        ]);
    }

    // Update mentor
    public function update(Request $request, $id)
    {
        $mentor = Mentor::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:mentors,email,' . $id,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:10240', // 10MB
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($mentor->photo) {
                $oldPhotoPath = 'C:\Users\mp2k5\Documents\GitHub\Project_Prokon\bersekolah_website\public\assets\image\mentor\\' . basename($mentor->photo);
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }
            
            $file = $request->file('photo');
            $originalName = $file->getClientOriginalName();
            
            // Use absolute path to mentor directory (more reliable)
            $destinationPath = 'C:\Users\mp2k5\Documents\GitHub\Project_Prokon\bersekolah_website\public\assets\image\mentor';
            
            // Create directory if it doesn't exist
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $originalName);
            
            // Store only filename in database (no mentor/ prefix)
            $data['photo'] = $originalName;
        }

        $mentor->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Mentor updated successfully',
            'data' => $mentor
        ]);
    }

    // Delete mentor
    public function destroy($id)
    {
        $mentor = Mentor::findOrFail($id);
        
        // Delete photo if exists
        if ($mentor->photo) {
            $photoPath = 'C:\Users\mp2k5\Documents\GitHub\Project_Prokon\bersekolah_website\public\assets\image\mentor\\' . basename($mentor->photo);
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }
        
        $mentor->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Mentor deleted successfully'
        ]);
    }
}
