# Dokumentasi API — Yayasan Inspirasi Semangat Sekolah

## Ringkasan
Dokumentasi API ini menyediakan informasi lengkap tentang Sistem Informasi Program Beasiswa SMP & SMA dari Yayasan Inspirasi Semangat Sekolah.

## URL Dasar
```
https://api/.../v1
```

## Autentikasi
Semua endpoint yang dilindungi memerlukan autentikasi melalui token Bearer dalam header Authorization:
```
Authorization: Bearer {token}
```

### Pendaftaran
Mendaftarkan akun pengguna baru.

**Endpoint:** `POST /api/register`

**Request Body:**
| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|----------|-------------|
| name | string | Ya | Nama lengkap pengguna |
| email | string | Ya | Alamat email yang valid |
| phone | string | Ya | Nomor telepon yang valid |
| password | string | Ya | Kata sandi (minimal 8 karakter) |
| password_confirmation | string | Ya | Harus sama dengan kata sandi |

**Response (200 OK):**
```json
{
  "message": "Pendaftaran berhasil",
  "data": {
    "user": {
      "id": "uuid",
      "name": "Nama Pengguna",
      "email": "pengguna@contoh.com",
      "phone": "+6281234567890",
      "created_at": "2025-08-15T09:20:30Z",
      "updated_at": "2025-08-15T09:20:30Z"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

### Login
Mengautentikasi pengguna dan menerima token akses.

**Endpoint:** `POST /api/login`

**Request Body:**
| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|----------|-------------|
| email | string | Ya | Alamat email terdaftar |
| password | string | Ya | Kata sandi pengguna |

**Response (200 OK):**
```json
{
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": "uuid",
      "name": "Nama Pengguna",
      "email": "pengguna@contoh.com",
      "phone": "+6281234567890",
      "role": "applicant",
      "created_at": "2025-08-15T09:20:30Z",
      "updated_at": "2025-08-15T09:20:30Z"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

### Logout
Mengakhiri sesi pengguna saat ini.

**Endpoint:** `POST /api/logout`

**Headers:**
- Authorization: Bearer {token}

**Response (200 OK):**
```json
{
  "message": "Berhasil logout"
}
```

### Lupa Kata Sandi
Meminta token untuk mengatur ulang kata sandi.

**Endpoint:** `POST /api/forgot-password`

**Request Body:**
| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|----------|-------------|
| phone | string | Ya | Nomor telepon terdaftar |

**Response (200 OK):**
```json
{
  "message": "Token reset kata sandi telah dikirim ke nomor telepon Anda"
}
```

### Reset Kata Sandi
Mengatur ulang kata sandi pengguna menggunakan token yang diterima.

**Endpoint:** `POST /api/reset-password`

**Request Body:**
| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|----------|-------------|
| phone | string | Ya | Nomor telepon terdaftar |
| token | string | Ya | Token reset yang diterima melalui SMS |
| password | string | Ya | Kata sandi baru (minimal 8 karakter) |
| password_confirmation | string | Ya | Harus sama dengan kata sandi |

**Response (200 OK):**
```json
{
  "message": "Kata sandi berhasil diatur ulang"
}
```

## Profil Calon Beswan

### Membuat Profil Calon Beswan
Membuat profil baru untuk calon penerima beasiswa.

**Endpoint:** `POST /api/calon-beswan`

**Headers:**
- Authorization: Bearer {token}

**Request Body:**
| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|----------|-------------|
| tempat_lahir | string | Ya | Tempat lahir |
| tanggal_lahir | date | Ya | Tanggal lahir (format: YYYY-MM-DD) |
| nama_ayah | string | Ya | Nama ayah |
| pekerjaan_ayah | string | Ya | Pekerjaan ayah |
| nama_ibu | string | Ya | Nama ibu |
| pekerjaan_ibu | string | Ya | Pekerjaan ibu |
| penghasilan_orang_tua | numeric | Ya | Penghasilan bulanan orang tua (dalam IDR) |
| alamat | string | Ya | Alamat rumah |

**Response (201 Created):**
```json
{
  "message": "Profil calon beswan berhasil dibuat",
  "data": {
    "id": "uuid",
    "user_id": "uuid",
    "tempat_lahir": "Jakarta",
    "tanggal_lahir": "2005-05-15",
    "nama_ayah": "Budi Santoso",
    "pekerjaan_ayah": "Wiraswasta",
    "nama_ibu": "Siti Aminah",
    "pekerjaan_ibu": "Guru",
    "penghasilan_orang_tua": 5000000,
    "alamat": "Jl. Merdeka No. 123, Jakarta Selatan",
    "created_at": "2025-08-15T10:30:45Z",
    "updated_at": "2025-08-15T10:30:45Z"
  }
}
```

### Mendapatkan Profil Calon Beswan
Mengambil profil calon beswan untuk pengguna yang terautentikasi.

**Endpoint:** `GET /api/calon-beswan`

**Headers:**
- Authorization: Bearer {token}

**Response (200 OK):**
```json
{
  "message": "Profil berhasil diambil",
  "data": {
    "id": "uuid",
    "user_id": "uuid",
    "tempat_lahir": "Jakarta",
    "tanggal_lahir": "2005-05-15",
    "nama_ayah": "Budi Santoso",
    "pekerjaan_ayah": "Wiraswasta",
    "nama_ibu": "Siti Aminah",
    "pekerjaan_ibu": "Guru",
    "penghasilan_orang_tua": 5000000,
    "alamat": "Jl. Merdeka No. 123, Jakarta Selatan",
    "created_at": "2025-08-15T10:30:45Z",
    "updated_at": "2025-08-15T10:30:45Z"
  }
}
```

### Memperbarui Profil Calon Beswan
Memperbarui profil calon beswan untuk pengguna yang terautentikasi.

**Endpoint:** `PUT /api/calon-beswan`

**Headers:**
- Authorization: Bearer {token}

**Request Body:**
Semua field bersifat opsional. Sertakan hanya field yang perlu diperbarui.

| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|----------|-------------|
| tempat_lahir | string | Tidak | Tempat lahir |
| tanggal_lahir | date | Tidak | Tanggal lahir (format: YYYY-MM-DD) |
| nama_ayah | string | Tidak | Nama ayah |
| pekerjaan_ayah | string | Tidak | Pekerjaan ayah |
| nama_ibu | string | Tidak | Nama ibu |
| pekerjaan_ibu | string | Tidak | Pekerjaan ibu |
| penghasilan_orang_tua | numeric | Tidak | Penghasilan bulanan orang tua (dalam IDR) |
| alamat | string | Tidak | Alamat rumah |

**Response (200 OK):**
```json
{
  "message": "Profil berhasil diperbarui",
  "data": {
    "id": "uuid",
    "user_id": "uuid",
    "tempat_lahir": "Jakarta",
    "tanggal_lahir": "2005-05-15",
    "nama_ayah": "Budi Santoso",
    "pekerjaan_ayah": "Pegawai Swasta",
    "nama_ibu": "Siti Aminah",
    "pekerjaan_ibu": "Guru",
    "penghasilan_orang_tua": 6000000,
    "alamat": "Jl. Merdeka No. 123, Jakarta Selatan",
    "created_at": "2025-08-15T10:30:45Z",
    "updated_at": "2025-08-15T11:45:20Z"
  }
}
```

## Manajemen Dokumen

### Unggah Dokumen
Mengunggah dokumen yang diperlukan untuk pendaftaran beasiswa.

**Endpoint:** `POST /api/calon-beswan/berkas`

**Headers:**
- Authorization: Bearer {token}
- Content-Type: multipart/form-data

**Request Body:**
| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|----------|-------------|
| nama_item | string | Ya | Nama/jenis dokumen (misalnya, "Kartu Keluarga", "Rapor") |
| file | file | Ya | File dokumen (PDF/JPG/PNG, maks 5MB) |
| keterangan | string | Tidak | Informasi tambahan tentang dokumen |
| publikasi | boolean | Tidak | Apakah dokumen ini dapat ditampilkan secara publik (default: false) |

**Response (201 Created):**
```json
{
  "message": "Dokumen berhasil diunggah",
  "data": {
    "id": "uuid",
    "calon_beswan_id": "uuid",
    "nama_item": "Kartu Keluarga",
    "file_path": "documents/user_uuid/kartu_keluarga_20250815.pdf",
    "keterangan": "Kartu Keluarga terbaru",
    "publikasi": false,
    "created_at": "2025-08-15T14:25:10Z",
    "updated_at": "2025-08-15T14:25:10Z"
  }
}
```

### Daftar Dokumen
Mengambil semua dokumen yang diunggah oleh pengguna yang terautentikasi.

**Endpoint:** `GET /api/calon-beswan/berkas`

**Headers:**
- Authorization: Bearer {token}

**Response (200 OK):**
```json
{
  "message": "Dokumen berhasil diambil",
  "data": [
    {
      "id": "uuid",
      "calon_beswan_id": "uuid",
      "nama_item": "Kartu Keluarga",
      "file_path": "documents/user_uuid/kartu_keluarga_20250815.pdf",
      "keterangan": "Kartu Keluarga terbaru",
      "publikasi": false,
      "created_at": "2025-08-15T14:25:10Z",
      "updated_at": "2025-08-15T14:25:10Z"
    },
    {
      "id": "uuid",
      "calon_beswan_id": "uuid",
      "nama_item": "Rapor",
      "file_path": "documents/user_uuid/rapor_semester1_20250815.pdf",
      "keterangan": "Rapor semester 1",
      "publikasi": false,
      "created_at": "2025-08-15T14:27:35Z",
      "updated_at": "2025-08-15T14:27:35Z"
    }
  ]
}
```

## Dokumen Pendukung Tambahan

### Unggah Dokumen Tambahan
Mengunggah dokumen pendukung tambahan untuk pendaftaran beasiswa.

**Endpoint:** `POST /api/calon-beswan/additional-uploads`

**Headers:**
- Authorization: Bearer {token}
- Content-Type: multipart/form-data

**Request Body:**
| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|----------|-------------|
| upload_type_id | string | Ya | ID jenis dokumen (dari daftar yang telah ditentukan) |
| file | file | Ya | File dokumen (PDF/JPG/PNG, maks 5MB) |
| keterangan | string | Tidak | Informasi tambahan tentang dokumen |

**Response (201 Created):**
```json
{
  "message": "Dokumen tambahan berhasil diunggah",
  "data": {
    "id": "uuid",
    "calon_beswan_id": "uuid",
    "upload_type_id": "uuid",
    "file_path": "additional_documents/user_uuid/certificate_20250815.pdf",
    "keterangan": "Sertifikat penghargaan olimpiade matematika",
    "created_at": "2025-08-15T15:10:22Z",
    "updated_at": "2025-08-15T15:10:22Z"
  }
}
```

### Daftar Dokumen Tambahan
Mengambil semua dokumen tambahan yang diunggah oleh pengguna yang terautentikasi.

**Endpoint:** `GET /api/calon-beswan/additional-uploads`

**Headers:**
- Authorization: Bearer {token}

**Response (200 OK):**
```json
{
  "message": "Dokumen tambahan berhasil diambil",
  "data": [
    {
      "id": "uuid",
      "calon_beswan_id": "uuid",
      "upload_type_id": "uuid",
      "file_path": "additional_documents/user_uuid/certificate_20250815.pdf",
      "keterangan": "Sertifikat penghargaan olimpiade matematika",
      "created_at": "2025-08-15T15:10:22Z",
      "updated_at": "2025-08-15T15:10:22Z",
      "upload_type": {
        "id": "uuid",
        "name": "Sertifikat Prestasi"
      }
    },
    {
      "id": "uuid",
      "calon_beswan_id": "uuid",
      "upload_type_id": "uuid",
      "file_path": "additional_documents/user_uuid/recommendation_20250815.pdf",
      "keterangan": "Surat rekomendasi dari kepala sekolah",
      "created_at": "2025-08-15T15:12:45Z",
      "updated_at": "2025-08-15T15:12:45Z",
      "upload_type": {
        "id": "uuid",
        "name": "Surat Rekomendasi"
      }
    }
  ]
}
```

## Pendaftaran Beasiswa

### Daftar Periode Beasiswa
Mengambil semua periode pendaftaran beasiswa yang tersedia.

**Endpoint:** `GET /api/beasiswa/periods`

**Headers:**
- Authorization: Bearer {token}

**Response (200 OK):**
```json
{
  "message": "Periode beasiswa berhasil diambil",
  "data": [
    {
      "id": "uuid",
      "tahun": "2025",
      "mulai_pendaftaran": "2025-11-01T00:00:00Z",
      "akhir_pendaftaran": "2025-11-30T23:59:59Z",
      "mulai_beasiswa": "2026-01-15T12:00:00Z",
      "akhir_beasiswa": "2026-12-15T12:00:00Z",
      "created_at": "2025-10-15T09:00:00Z",
      "updated_at": "2025-10-15T09:00:00Z"
    },
    {
      "id": "uuid",
      "tahun": "2024",
      "mulai_pendaftaran": "2024-11-01T00:00:00Z",
      "akhir_pendaftaran": "2024-11-30T23:59:59Z",
      "mulai_beasiswa": "2025-01-15T12:00:00Z",
      "akhir_beasiswa": "2025-12-15T12:00:00Z",
      "created_at": "2024-10-15T09:00:00Z",
      "updated_at": "2024-10-15T09:00:00Z"
    }
  ]
}
```

### Daftar Beasiswa
Mengajukan pendaftaran beasiswa untuk periode tertentu.

**Endpoint:** `POST /api/beasiswa/apply`

**Headers:**
- Authorization: Bearer {token}

**Request Body:**
| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|----------|-------------|
| beasiswa_period_id | string | Ya | ID dari periode beasiswa |

**Response (201 Created):**
```json
{
  "message": "Pendaftaran beasiswa berhasil diajukan",
  "data": {
    "id": "uuid",
    "beasiswa_period_id": "uuid",
    "calon_beswan_id": "uuid",
    "status": "submitted",
    "created_at": "2025-08-15T16:30:00Z",
    "updated_at": "2025-08-15T16:30:00Z"
  }
}
```

### Cek Status Pendaftaran
Mengambil status pendaftaran beasiswa pengguna yang terautentikasi.

**Endpoint:** `GET /api/beasiswa/my-application`

**Headers:**
- Authorization: Bearer {token}

**Response (200 OK):**
```json
{
  "message": "Status pendaftaran berhasil diambil",
  "data": {
    "id": "uuid",
    "beasiswa_period_id": "uuid",
    "calon_beswan_id": "uuid",
    "status": "pending",
    "created_at": "2025-08-15T16:30:00Z",
    "updated_at": "2025-08-16T10:15:30Z",
    "beasiswa_period": {
      "id": "uuid",
      "tahun": "2025",
      "mulai_pendaftaran": "2025-11-01T00:00:00Z",
      "akhir_pendaftaran": "2025-11-30T23:59:59Z",
    }
  }
}
```

## Notifikasi

### Daftar Notifikasi
Mengambil semua notifikasi untuk pengguna yang terautentikasi.

**Endpoint:** `GET /api/notifications`

**Headers:**
- Authorization: Bearer {token}

**Parameter Query:**
| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|----------|-------------|
| page | integer | Tidak | Nomor halaman untuk paginasi (default: 1) |
| per_page | integer | Tidak | Jumlah item per halaman (default: 15) |

**Response (200 OK):**
```json
{
  "message": "Notifikasi berhasil diambil",
  "data": [
    {
      "id": "uuid",
      "user_id": "uuid",
      "title": "Dokumen Diterima",
      "message": "Dokumen ... Anda telah diterima dan diverifikasi.",
      "is_read": false,
      "created_at": "2025-08-16T09:45:00Z",
      "updated_at": "2025-08-16T09:45:00Z"
    },
    {
      "id": "uuid",
      "user_id": "uuid",
      "title": "Status Aplikasi Diperbarui",
      "message": "Status aplikasi beasiswa Anda telah diperbarui menjadi 'pending'.",
      "is_read": true,
      "created_at": "2025-08-16T10:15:30Z",
      "updated_at": "2025-08-16T10:20:15Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 2
  }
}
```

### Tandai Notifikasi Sudah Dibaca
Menandai notifikasi tertentu sebagai sudah dibaca.

**Endpoint:** `POST /api/notifications/{id}/read`

**Headers:**
- Authorization: Bearer {token}

**Parameter Path:**
| Parameter | Tipe | Wajib | Deskripsi |
|-----------|------|----------|-------------|
| id | string | Ya | ID Notifikasi |

**Response (200 OK):**
```json
{
  "message": "Notifikasi ditandai sebagai sudah dibaca",
  "data": {
    "id": "uuid",
    "user_id": "uuid",
    "title": "Dokumen Diterima",
    "message": "Dokumen ... Anda telah diterima dan diverifikasi.",
    "is_read": true,
    "created_at": "2025-08-16T09:45:00Z",
    "updated_at": "2025-08-16T11:20:00Z"
  }
}
```

## Konten Website

### Pengaturan
Mengambil semua pengaturan konten website.

**Endpoint:** `GET /api/settings`

**Response (200 OK):**
```json
{
  "message": "Pengaturan berhasil diambil",
  "data": {
    "company_name": "Yayasan Inspirasi Semangat Sekolah",
    "company_address": "Jl. Pendidikan No. 123, Jakarta Pusat",
    "company_phone": "+6221123456789",
    "company_email": "info@yiss.or.id",
    "hero_title": "Wujudkan Mimpi Pendidikan Anda",
    "hero_subtitle": "Program beasiswa untuk siswa SMP & SMA berprestasi",
    "hero_cta": "Daftar Sekarang",
    "about_title": "Tentang Yayasan Inspirasi Semangat Sekolah",
    "about_description": "Yayasan Inspirasi Semangat Sekolah didirikan pada tahun 2015 dengan misi membantu siswa-siswi berprestasi namun kurang mampu untuk melanjutkan pendidikan mereka.",
    "about_vision": "Menjadi yayasan terdepan dalam memberikan akses pendidikan berkualitas bagi seluruh anak Indonesia.",
    "about_mission": "Memberikan dukungan finansial dan mentoring kepada siswa berprestasi dari keluarga kurang mampu."
  }
}
```

### FAQ (Pertanyaan yang Sering Diajukan)
Mengambil daftar pertanyaan yang sering diajukan.

**Endpoint:** `GET /api/faqs`

**Response (200 OK):**
```json
{
  "message": "FAQ berhasil diambil",
  "data": [
    {
      "id": "uuid",
      "pertanyaan": "Bagaimana cara mendaftar beasiswa?",
      "jawaban": "Silakan membuat akun dan mengisi formulir pendaftaran online di website kami.",
      "created_at": "2025-08-15T09:00:00Z",
      "updated_at": "2025-08-15T09:00:00Z"
    },
    {
      "id": "uuid",
      "pertanyaan": "Apa saja persyaratan untuk mendaftar beasiswa?",
      "jawaban": "Persyaratan meliputi nilai akademik, kondisi ekonomi keluarga, dan prestasi non-akademik.",
      "created_at": "2025-08-15T09:00:00Z",
      "updated_at": "2025-08-15T09:00:00Z"
    },
    {
      "id": "uuid",
      "pertanyaan": "Kapan pendaftaran beasiswa dibuka?",
      "jawaban": "Pendaftaran beasiswa dibuka setiap bulan November setiap tahunnya.",
      "created_at": "2025-08-15T09:00:00Z",
      "updated_at": "2025-08-15T09:00:00Z"
    }
  ]
}
```

### Testimonial
Mengambil daftar testimonial dari alumni beswan atau donatur.

**Endpoint:** `GET /api/testimonials`

**Response (200 OK):**
```json
{
  "message": "Testimonial berhasil diambil",
  "data": [
    {
      "id": "uuid",
      "name": "Budi Santoso",
      "photo_path": "testimonials/budi_santoso.jpg",
      "content": "Beasiswa ini telah membantu saya fokus belajar tanpa memikirkan biaya pendidikan.",
      "created_at": "2025-08-15T09:00:00Z",
      "updated_at": "2025-08-15T09:00:00Z"
    },
    {
      "id": "uuid",
      "name": "Siti Rahayu",
      "photo_path": "testimonials/siti_rahayu.jpg",
      "content": "Berkat beasiswa dari YISS, saya bisa mengejar impian saya untuk kuliah di universitas terbaik.",
      "created_at": "2025-08-15T09:00:00Z",
      "updated_at": "2025-08-15T09:00:00Z"
    },
    {
      "id": "uuid",
      "name": "Ahmad Rizki",
      "photo_path": "testimonials/ahmad_rizki.jpg",
      "content": "Program mentoring dari YISS tidak hanya membantu secara finansial tapi juga membentuk karakter dan kepemimpinan saya.",
      "created_at": "2025-08-15T09:00:00Z",
      "updated_at": "2025-08-15T09:00:00Z"
    }
  ]
}
```

## Format Response Error

### 400 Bad Request
```json
{
  "message": "Validasi gagal",
  "errors": {
    "field_name": [
      "Pesan error untuk field ini"
    ]
  }
}
```

### 401 Unauthorized
```json
{
  "message": "Tidak terautentikasi",
  "errors": {
    "auth": [
      "Anda tidak terautentikasi. Silakan login terlebih dahulu."
    ]
  }
}
```

### 403 Forbidden
```json
{
  "message": "Akses ditolak",
  "errors": {
    "permission": [
      "Anda tidak memiliki izin untuk mengakses sumber daya ini."
    ]
  }
}
```

### 404 Not Found
```json
{
  "message": "Sumber daya tidak ditemukan",
  "errors": {
    "resource": [
      "Sumber daya yang diminta tidak dapat ditemukan."
    ]
  }
}
```

### 422 Unprocessable Entity
```json
{
  "message": "Data yang diberikan tidak valid",
  "errors": {
    "email": [
      "Email sudah digunakan."
    ],
    "password": [
      "Kata sandi harus minimal 8 karakter."
    ]
  }
}
```

### 500 Internal Server Error
```json
{
  "message": "Kesalahan server",
  "errors": {
    "server": [
      "Terjadi kesalahan yang tidak terduga. Silakan coba lagi nanti."
    ]
  }
}
```

## Kode Status

API ini menggunakan kode status berikut:

| Kode Status | Deskripsi |
|-------------|-------------|
| 200 | OK - Permintaan berhasil |
| 201 | Created - Sumber daya baru berhasil dibuat |
| 400 | Bad Request - Permintaan tidak dapat dipahami atau parameter yang diperlukan hilang |
| 401 | Unauthorized - Autentikasi gagal atau pengguna tidak memiliki izin |
| 403 | Forbidden - Akses ditolak |
| 404 | Not Found - Sumber daya tidak ditemukan |
| 422 | Unprocessable Entity - Error validasi |
| 429 | Too Many Requests - Batas rate permintaan terlampaui |
| 500 | Internal Server Error - Kesalahan server |

## Batasan Rate 

Permintaan API dibatasi untuk mencegah penyalahgunaan. Batasan rate diterapkan berdasarkan per-pengguna:

- 60 permintaan per menit
- 1000 permintaan per jam

Ketika batas rate terlampaui, API akan merespons dengan kode status 429 Too Many Requests.

## Catatan untuk Integrasi Frontend

- Untuk pengaturan konten dinamis, kelompokkan pengaturan berdasarkan awalan `key`-nya:
  - `company_*` → Profil Perusahaan
  - `hero_*` → Bagian Hero
  - `about_*` → Tentang Yayasan
  
- Gunakan endpoint `/api/faqs` untuk mengambil data FAQ
  
- Gunakan endpoint `/api/testimonials` untuk mengambil data testimonial

- Selalu tangani respons error dengan tepat dan tampilkan pesan yang ramah pengguna
- Simpan token autentikasi dengan aman
- Implementasikan validasi formulir yang ses uai