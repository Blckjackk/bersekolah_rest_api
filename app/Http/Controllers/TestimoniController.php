<?php

namespace App\Http\Controllers;

use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimoniController extends Controller
{
    public function index()
    {
        $testimoni = Testimoni::where('status', 'active')->get();
        return response()->json($testimoni);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:100',
            'angkatan_beswan' => 'required|string|max:20',
            'sekarang_dimana' => 'nullable|string|max:255',
            'isi_testimoni' => 'required|string',
            'foto_testimoni' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'in:active,inactive'
        ]);

        if ($request->hasFile('foto_testimoni')) {
            $path = $request->file('foto_testimoni')->store('testimoni', 'public');
            $validatedData['foto_testimoni'] = $path;
        }

        $validatedData['status'] = $validatedData['status'] ?? 'inactive';

        $testimoni = Testimoni::create($validatedData);

        return response()->json([
            'message' => 'Testimoni created successfully.',
            'data' => $testimoni
        ], 201);
    }

    public function show(Testimoni $testimoni)
    {
        return response()->json($testimoni);
    }

    public function update(Request $request, Testimoni $testimoni)
    {
        $validatedData = $request->validate([
            'nama' => 'sometimes|required|string|max:100',
            'angkatan_beswan' => 'sometimes|required|string|max:20',
            'sekarang_dimana' => 'nullable|string|max:255',
            'isi_testimoni' => 'sometimes|required|string',
            'foto_testimoni' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'in:active,inactive'
        ]);

        if ($request->hasFile('foto_testimoni')) {
            // Hapus foto lama jika ada
            if ($testimoni->foto_testimoni) {
                Storage::disk('public')->delete($testimoni->foto_testimoni);
            }

            $path = $request->file('foto_testimoni')->store('testimoni', 'public');
            $validatedData['foto_testimoni'] = $path;
        }

        $testimoni->update($validatedData);

        return response()->json([
            'message' => 'Testimoni updated successfully.',
            'data' => $testimoni
        ]);
    }

    public function destroy(Testimoni $testimoni)
    {
        if ($testimoni->foto_testimoni) {
            Storage::disk('public')->delete($testimoni->foto_testimoni);
        }

        $testimoni->delete();

        return response()->json([
            'message' => 'Testimoni deleted successfully.'
        ]);
    }
}
