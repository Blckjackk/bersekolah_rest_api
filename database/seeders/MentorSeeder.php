<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MentorSeeder extends Seeder
{
    public function run(): void
    {
        $mentors = [
            [
                'name' => 'Ahmad Izuddin Azzam',
                'email' => 'ahmad.izzuddin.azzam@bersekolah.org',
                'photo' => 'assets/image/Ahmad Izzuddin Azzam_Non Formal.JPG',
            ],
            [
                'name' => 'Abdurrahman Alghifari',
                'email' => 'abdurrahman.alghifari@bersekolah.org',
                'photo' => 'ImageTemp/Ghifari.png'
            ],
            [
                'name' => 'Julian Dwi',
                'email' => 'julian.dwi@bersekolah.org',
                'photo' => 'ImageTemp/julian.png'
            ],
            [
                'name' => 'Arya Jagadhita',
                'email' => 'arya.jagadhita@bersekolah.org',
                'photo' => 'ImageTemp/Rhea.png',
            ],
            [
                'name' => 'Erin Armaida',
                'email' => 'erin.armaida@bersekolah.org',
                'photo' => 'ImageTemp/Erin.png',
            ],
            [
                'name' => 'Dinal Azmi',
                'email' => 'dinal.azmi@bersekolah.org',
                'photo' => 'ImageTemp/Dinal.png',
            ],
            [
                'name' => 'Fathir vandarvelis',
                'email' => 'fathir.vandarvelis@bersekolah.org',
                'photo' => 'ImageTemp/Fathir.png',
            ],
            [
                'name' => 'Dedi',
                'email' => 'dedi@bersekolah.org',
                'photo' => 'ImageTemp/Dedi.png',
            ],
            [
                'name' => 'Rifat Syafaat',
                'email' => 'rifat.syafaat@bersekolah.org',
                'photo' => 'ImageTemp/kakRifat.png',
            ],
            [
                'name' => 'Andrian Fauzi',
                'email' => 'andrian.fauzi@bersekolah.org',
                'photo' => 'ImageTemp/kakAndrian.png',
            ],
            [
                'name' => 'Shantika',
                'email' => 'shantika@bersekolah.org',
                'photo' => 'ImageTemp/kakSantika.png',
            ],
            [
                'name' => 'Tyas Ningrum',
                'email' => 'tyas.ningrum@bersekolah.org',
                'photo' => 'ImageTemp/kakTyas.png',
            ],
            [
                'name' => 'Pebi Sukamdani',
                'email' => 'pebi.sukamdani@bersekolah.org',
                'photo' => 'ImageTemp/kakPebi.png',
            ],
        ];

        DB::table('mentors')->insert($mentors);
    }
}
