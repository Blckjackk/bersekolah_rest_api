<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBeasiswaPeriodsRequest;
use App\Http\Requests\UpdateBeasiswaPeriodsRequest;
use App\Models\BeasiswaPeriods;
use Illuminate\Http\Request;

class BeasiswaPeriodsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BeasiswaPeriods::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_periode', 'LIKE', "%{$search}%")
                  ->orWhere('tahun', 'LIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by year
        if ($request->has('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Load application counts
        $query->withCount('applications as applicants_count');

        // Order by latest
        $query->orderBy('tahun', 'desc')->orderBy('created_at', 'desc');

        // Pagination
        $perPage = $request->get('per_page', 15);
        $beasiswaPeriods = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Beasiswa periods retrieved successfully',
            'data' => $beasiswaPeriods->items(),
            'meta' => [
                'current_page' => $beasiswaPeriods->currentPage(),
                'per_page' => $beasiswaPeriods->perPage(),
                'total' => $beasiswaPeriods->total(),
                'last_page' => $beasiswaPeriods->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBeasiswaPeriodsRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $beasiswaPeriod = BeasiswaPeriods::create($validatedData);

            // Load counts
            $beasiswaPeriod->loadCount('applications as applicants_count');

            return response()->json([
                'success' => true,
                'message' => 'Periode beasiswa berhasil dibuat',
                'data' => $beasiswaPeriod
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat periode beasiswa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $beasiswaPeriod = BeasiswaPeriods::findOrFail($id);
            
            // Load relationships and counts
            $beasiswaPeriod->loadCount('applications as applicants_count');
            $beasiswaPeriod->load('applications');

            return response()->json([
                'success' => true,
                'message' => 'Periode beasiswa retrieved successfully',
                'data' => $beasiswaPeriod
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Periode beasiswa tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBeasiswaPeriodsRequest $request, $id)
    {
        try {
            // Cari periode berdasarkan ID
            $beasiswaPeriod = BeasiswaPeriods::findOrFail($id);
            
            $validatedData = $request->validated();

            // Update data
            $beasiswaPeriod->update($validatedData);

            // Refresh model untuk mendapatkan data terbaru
            $beasiswaPeriod->refresh();

            // Load counts
            $beasiswaPeriod->loadCount('applications as applicants_count');

            return response()->json([
                'success' => true,
                'message' => 'Periode beasiswa berhasil diperbarui',
                'data' => $beasiswaPeriod
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Periode beasiswa tidak ditemukan',
                'error' => 'Data tidak ditemukan dengan ID: ' . $id
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui periode beasiswa',
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
            // Cari periode berdasarkan ID
            $beasiswaPeriod = BeasiswaPeriods::findOrFail($id);
            
            // Check if period has applications
            $applicationsCount = $beasiswaPeriod->applications()->count();
            
            if ($applicationsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak dapat menghapus periode ini karena sudah memiliki {$applicationsCount} aplikasi beasiswa"
                ], 422);
            }

            $beasiswaPeriod->delete();

            return response()->json([
                'success' => true,
                'message' => 'Periode beasiswa berhasil dihapus'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Periode beasiswa tidak ditemukan',
                'error' => 'Data tidak ditemukan dengan ID: ' . $id
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus periode beasiswa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
