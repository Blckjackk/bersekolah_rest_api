#!/bin/bash

echo "🔄 Menjalankan migration untuk menambah kolom link_grup_beasiswa..."
php artisan migrate --path=database/migrations/2025_07_05_000000_add_link_grup_beasiswa_to_media_sosial_table.php

echo "🔄 Menjalankan seeder untuk update data media sosial..."
php artisan db:seed --class=MediaSosialSeeder

echo "✅ Selesai! Link grup beasiswa telah ditambahkan."
echo "📱 Default link grup: https://chat.whatsapp.com/DBWgEhlvkz3E0SqpdvIL1q"
