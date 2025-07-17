<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Send a reset link/code to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Cek apakah email terdaftar
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Email tidak terdaftar',
                'errors' => ['email' => ['Email tidak terdaftar dalam sistem kami.']],
            ], 404);
        }

        // Hapus token reset lama jika ada
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Buat token baru (6 digit OTP)
        $token = rand(100000, 999999);
        
        // Simpan token ke database
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => bcrypt($token), // Simpan token yang sudah di-hash
            'created_at' => Carbon::now()
        ]);

        // Kirim email reset password dengan token
        try {
            Mail::to($request->email)->send(new ResetPasswordMail($token));
            
            return response()->json([
                'message' => 'Kode verifikasi telah dikirim ke email Anda.',
                'status' => 'success',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email reset password: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Gagal mengirim kode verifikasi. Silakan coba lagi nanti.',
                'status' => 'error',
            ], 500);
        }
    }

    /**
     * Reset the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Cek token dan email
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenData) {
            return response()->json([
                'message' => 'Kode verifikasi tidak valid atau telah kedaluwarsa.',
                'errors' => ['token' => ['Kode verifikasi tidak valid atau telah kedaluwarsa.']],
            ], 400);
        }

        // Cek apakah token sudah expired (token valid selama 60 menit)
        $createdAt = Carbon::parse($tokenData->created_at);
        if (Carbon::now()->diffInMinutes($createdAt) > 60) {
            // Hapus token yang expired
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            
            return response()->json([
                'message' => 'Kode verifikasi telah kedaluwarsa.',
                'errors' => ['token' => ['Kode verifikasi telah kedaluwarsa. Silakan minta kode baru.']],
            ], 400);
        }

        // Verifikasi token (OTP)
        if (!password_verify($request->token, $tokenData->token)) {
            return response()->json([
                'message' => 'Kode verifikasi tidak valid.',
                'errors' => ['token' => ['Kode verifikasi yang Anda masukkan tidak valid.']],
            ], 400);
        }

        // Update password user
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        // Hapus token karena sudah digunakan
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Kata sandi berhasil diubah.',
            'status' => 'success',
        ], 200);
    }
}
