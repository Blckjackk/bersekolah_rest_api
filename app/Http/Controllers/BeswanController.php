<?php

namespace App\Http\Controllers;

use App\Models\Beswan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class BeswanController extends Controller
{
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $beswan = Beswan::create($request->only([
                'nama_lengkap', 'nama_panggilan', 'tempat_lahir',
                'tanggal_lahir', 'jenis_kelamin', 'agama'
            ]));

       

            // Tidak insert dokumen_pendukung karena bisa lebih dari 1 dan diisi belakangan
        });

        return redirect()->back()->with('success', 'Pendaftaran berhasil dibuat!');
    }

    /**
     * Display a listing of beswans
     */
    public function index(Request $request)
    {
        try {
            $query = Beswan::with(['keluarga', 'sekolah', 'alamat', 'user']);
            
            // Filter by period if period_id is provided
            if ($request->has('period_id') && $request->period_id) {
                $periodId = $request->period_id;
                $query->whereHas('beasiswaApplications', function($q) use ($periodId) {
                    $q->where('beasiswa_period_id', $periodId);
                });
            }
            
            $beswans = $query->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $beswans
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified beswan
     */
    public function show($id)
    {
        try {
            $beswan = Beswan::with(['keluarga', 'sekolah', 'alamat'])
                ->findOrFail($id);
            
            return response()->json([
                'status' => 'success',
                'data' => $beswan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Beswan tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update the specified beswan
     */
    public function update(Request $request, $id)
    {
        try {
            $beswan = Beswan::findOrFail($id);
            
            $beswan->update($request->only([
                'nama_lengkap', 'nama_panggilan', 'tempat_lahir',
                'tanggal_lahir', 'jenis_kelamin', 'agama'
            ]));

            return response()->json([
                'status' => 'success',
                'message' => 'Data beswan berhasil diperbarui',
                'data' => $beswan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified beswan
     */
    public function destroy($id)
    {
        try {
            $beswan = Beswan::findOrFail($id);
            $beswan->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data beswan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

