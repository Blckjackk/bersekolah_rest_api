<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use Illuminate\Http\Request;

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
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:10240', // max 2MB
        ]);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('mentors', 'public');
            $data['photo'] = $photoPath;
        }

        $mentor = Mentor::create($data);

        return response()->json($mentor, 201);
    }

    // Show mentor by id
    public function show($id)
    {
        $mentor = Mentor::findOrFail($id);
        return response()->json($mentor);
    }

    // Update mentor
    public function update(Request $request, $id)
    {
        $mentor = Mentor::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:mentors,email,' . $id,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:10240', // 10MB = 10240 KB
        ]);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('mentors', 'public');
            $data['photo'] = $photoPath;
        }

        $mentor->update($data);

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
