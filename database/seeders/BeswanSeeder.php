<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Beswan;
use App\Models\User;
use App\Models\SekolahBeswan;
use App\Models\AlamatBeswan;
use App\Models\KeluargaBeswan;

class BeswanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample users first
        $users = [
            [
                'name' => 'Ahmad Rizki Pratama',
                'email' => 'ahmad.rizki@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Maya Sari Dewi',
                'email' => 'maya.sari@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@example.com',
                'password' => bcrypt('password'),
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $createdUsers[] = User::create($userData);
        }

        // Create sample schools
        $schools = [
            [
                'nama_sekolah' => 'Universitas Indonesia',
                'alamat_sekolah' => 'Depok, Jawa Barat',
                'jenjang' => 'S1',
                'jurusan' => 'Teknik Informatika',
            ],
            [
                'nama_sekolah' => 'Institut Teknologi Bandung',
                'alamat_sekolah' => 'Bandung, Jawa Barat',
                'jenjang' => 'S1',
                'jurusan' => 'Teknik Elektro',
            ],
            [
                'nama_sekolah' => 'Universitas Gadjah Mada',
                'alamat_sekolah' => 'Yogyakarta',
                'jenjang' => 'S1',
                'jurusan' => 'Ekonomi',
            ],
            [
                'nama_sekolah' => 'Universitas Airlangga',
                'alamat_sekolah' => 'Surabaya, Jawa Timur',
                'jenjang' => 'S1',
                'jurusan' => 'Kedokteran',
            ],
            [
                'nama_sekolah' => 'Universitas Padjadjaran',
                'alamat_sekolah' => 'Bandung, Jawa Barat',
                'jenjang' => 'S1',
                'jurusan' => 'Hukum',
            ],
        ];

        $createdSchools = [];
        foreach ($schools as $schoolData) {
            $createdSchools[] = SekolahBeswan::create($schoolData);
        }

        // Create sample beswan data
        for ($i = 0; $i < 5; $i++) {
            $user = $createdUsers[$i];
            $school = $createdSchools[$i];

            // Create Beswan
            $beswan = Beswan::create([
                'user_id' => $user->id,
                'nama_panggilan' => explode(' ', $user->name)[0],
                'tempat_lahir' => ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Medan'][$i],
                'tanggal_lahir' => now()->subYears(20 + $i)->format('Y-m-d'),
                'jenis_kelamin' => $i % 2 == 0 ? 'L' : 'P',
                'agama' => ['Islam', 'Kristen', 'Hindu', 'Buddha', 'Katolik'][$i],
            ]);

            // Create Address
            AlamatBeswan::create([
                'beswan_id' => $beswan->id,
                'alamat_lengkap' => 'Jl. Contoh No. ' . ($i + 1) . ', Jakarta',
                'kota' => 'Jakarta',
                'provinsi' => 'DKI Jakarta',
                'kode_pos' => '12345',
            ]);

            // Create Family
            KeluargaBeswan::create([
                'beswan_id' => $beswan->id,
                'nama_ayah' => 'Ayah ' . $user->name,
                'nama_ibu' => 'Ibu ' . $user->name,
                'pekerjaan_ayah' => ['PNS', 'Wiraswasta', 'Petani', 'Buruh', 'Guru'][$i],
                'pekerjaan_ibu' => ['Ibu Rumah Tangga', 'PNS', 'Wiraswasta', 'Guru', 'Perawat'][$i],
                'penghasilan_orangtua' => [2000000, 3000000, 1500000, 2500000, 3500000][$i],
            ]);

            // Associate with school
            $beswan->sekolah()->associate($school);
            $beswan->save();
        }
    }
}
