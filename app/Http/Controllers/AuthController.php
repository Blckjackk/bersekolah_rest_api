<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Beswan;
use App\Models\KeluargaBeswan;
use App\Models\AlamatBeswan;
use App\Models\SekolahBeswan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Debug: Log request data
        \Log::info('Register request data:', $request->all());
        
        try {
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users',
                'phone' => 'required|numeric|unique:users',
                'password' => 'required|confirmed',
            ]);

            // Debug: Log validated data
            \Log::info('Validated data:', $validatedData);

            // Gunakan database transaction untuk memastikan konsistensi data
            DB::beginTransaction();

            try {
                // Buat user baru
                $validatedData['password'] = Hash::make($validatedData['password']);
                $user = User::create($validatedData);

                // Buat record beswan yang terkait dengan user
                $beswan = Beswan::create([
                    'user_id' => $user->id,
                ]);

                // Simpan beswan_id untuk digunakan ke tabel lain
                $beswanId = $beswan->id;
                
                // Buat token untuk user
                $token = $user->createToken($request->email)->plainTextToken;

                DB::commit();

                // Load relasi beswan untuk response
                $user->load('beswan');

                return response()->json([
                    'message' => 'Registrasi berhasil.',
                    'user' => $user,
                    'token' => $token,
                    'beswan_id' => $beswanId
                ], 201);

            } catch (\Exception $e) {
                DB::rollback();
                \Log::error('Database transaction failed:', ['error' => $e->getMessage()]);
                return response()->json([
                    'message' => 'Terjadi kesalahan saat menyimpan data.',
                    'errors' => ['server' => [$e->getMessage()]]
                ], 500);
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', $e->errors());
            return response()->json([
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Registration error:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Terjadi kesalahan server',
                'errors' => ['server' => ['Gagal membuat akun']]
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Email atau kata sandi salah.',
            ], 401);
        }

        $user = Auth::user();
        
        // Hapus token yang sudah ada (opsional)
        $user->tokens()->delete();
        
        // Buat token baru dengan nama aplikasi dan role sebagai ability
        $token = $user->createToken('auth_token', [$user->role])->plainTextToken;
        
        // Load relasi beswan
        $user->load('beswan');
        
        return response()->json([
            'message' => 'Login berhasil.',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }
}
