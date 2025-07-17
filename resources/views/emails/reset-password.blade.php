<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Bersekolah Project</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 25px;
            border: 1px solid #eee;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            color: #2563eb;
            margin: 10px 0;
        }
        .content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #eee;
        }
        .code {
            background: #f0f7ff;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            letter-spacing: 5px;
            border-radius: 5px;
            color: #2563eb;
            border: 1px dashed #2563eb;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 30px;
        }
        .note {
            font-size: 12px;
            color: #777;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Bersekolah Project</h2>
            <p>Reset Password</p>
        </div>
        
        <div class="content">
            <p>Halo,</p>
            
            <p>Kami menerima permintaan untuk mengatur ulang kata sandi akun Bersekolah Anda. Gunakan kode verifikasi berikut:</p>
            
            <div class="code">{{ $token }}</div>
            
            <p>Kode verifikasi ini hanya berlaku selama 60 menit.</p>
            
            <p>Jika Anda tidak meminta pengaturan ulang kata sandi, abaikan email ini.</p>
            
            <div class="note">
                <p>Catatan: Jangan pernah membagikan kode verifikasi ini dengan siapapun.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim otomatis, mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} Bersekolah Project. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>
