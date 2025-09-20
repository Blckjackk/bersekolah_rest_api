#!/bin/bash

# 🚀 Bersekolah Backend Deployment Script
# Script mudah untuk deploy backend API Bersekolah

set -e

# Colors untuk output yang lebih menarik
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Banner
echo -e "${CYAN}"
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║                ⚙️  BERSEKOLAH BACKEND DEPLOYMENT ⚙️           ║"
echo "║                                                              ║"
echo "║  Script mudah untuk deploy backend API Bersekolah ke Hostinger ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Function untuk menampilkan menu
show_menu() {
    echo -e "${YELLOW}📋 Pilih opsi deployment:${NC}"
    echo -e "${BLUE}1.${NC} Deploy Backend API (Full Upload)"
    echo -e "${BLUE}2.${NC} Deploy Core Files Only (app, routes, config)"
    echo -e "${BLUE}3.${NC} Deploy dengan Storage & Public"
    echo -e "${BLUE}4.${NC} Deploy Images Only (ke public_html)"
    echo -e "${BLUE}5.${NC} Test SSH Connection"
    echo -e "${BLUE}6.${NC} Cek Status API"
    echo -e "${BLUE}7.${NC} Update Database Schema"
    echo -e "${BLUE}8.${NC} Setup Bersekolah System (Migration + Seeder)"
    echo -e "${BLUE}9.${NC} Clear Cache"
    echo -e "${BLUE}10.${NC} Keluar"
    echo ""
}

# Function untuk test SSH connection
test_ssh() {
    echo -e "${YELLOW}🔐 Testing SSH connection...${NC}"
    
    if sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no -o ConnectTimeout=10 u787393221@46.202.138.221 "echo 'SSH connection successful!'"; then
        echo -e "${GREEN}✅ SSH connection berhasil!${NC}"
        return 0
    else
        echo -e "${RED}❌ SSH connection gagal!${NC}"
        echo -e "${YELLOW}💡 Tips:${NC}"
        echo -e "   - Pastikan internet connection stabil"
        echo -e "   - Cek apakah sshpass sudah terinstall"
        echo -e "   - Coba jalankan: brew install hudochenkov/sshpass/sshpass"
        return 1
    fi
}

# Function untuk deploy core files only
deploy_core() {
    echo -e "${PURPLE}⚙️  Deploying Core Files (app, routes, config)...${NC}"
    
    # Check if we're in the right directory
    if [ ! -d "app" ] || [ ! -d "routes" ] || [ ! -d "config" ]; then
        echo -e "${RED}❌ Error: Folder app, routes, atau config tidak ditemukan${NC}"
        echo -e "${YELLOW}💡 Pastikan Anda berada di root directory Bersekolah API${NC}"
        return 1
    fi
    
    # Test SSH first
    if ! test_ssh; then
        return 1
    fi
    
    # Create temp directory for selective upload
    echo -e "${YELLOW}📦 Preparing core files for upload...${NC}"
    mkdir -p temp_deploy
    
    # Copy core folders
    cp -r app temp_deploy/
    cp -r routes temp_deploy/
    cp -r config temp_deploy/
    cp -r bootstrap temp_deploy/
    cp -r artisan temp_deploy/
    cp -r composer.json temp_deploy/
    cp -r composer.lock temp_deploy/ 2>/dev/null || true
    
    echo -e "${YELLOW}📤 Creating directory and uploading core files...${NC}"
    sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "mkdir -p /home/u787393221/domains/api.bersekolah.com/project_files"
    sshpass -p "Bersekolah_123456" scp -P 65002 -o StrictHostKeyChecking=no -r temp_deploy/* u787393221@46.202.138.221:/home/u787393221/domains/api.bersekolah.com/project_files/
    
    echo -e "${YELLOW}🔧 Setting permissions...${NC}"
    sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
        cd /home/u787393221/domains/api.bersekolah.com/project_files
        chmod -R 755 .
        chmod -R 755 storage bootstrap/cache 2>/dev/null || true
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        echo '✅ Core files deployed successfully'
    "
    
    # Cleanup temp directory
    rm -rf temp_deploy
    
    echo -e "${GREEN}✅ Core files deployed successfully!${NC}"
    echo -e "${CYAN}🔗 API: https://api.bersekolah.com${NC}"
}

# Function untuk deploy dengan storage dan public
deploy_with_storage() {
    echo -e "${PURPLE}📁 Deploying with Storage & Public folders...${NC}"
    
    # Check if we're in the right directory
    if [ ! -d "app" ] || [ ! -d "routes" ] || [ ! -d "config" ]; then
        echo -e "${RED}❌ Error: Folder app, routes, atau config tidak ditemukan${NC}"
        echo -e "${YELLOW}💡 Pastikan Anda berada di root directory Bersekolah API${NC}"
        return 1
    fi
    
    # Test SSH first
    if ! test_ssh; then
        return 1
    fi
    
    # Ask for storage upload
    echo -e "${YELLOW}📁 Upload storage folder? (y/n):${NC}"
    read -p "Upload storage: " upload_storage
    
    # Ask for public upload
    echo -e "${YELLOW}📁 Upload public folder? (y/n):${NC}"
    read -p "Upload public: " upload_public
    
    # Create temp directory for selective upload
    echo -e "${YELLOW}📦 Preparing files for upload...${NC}"
    mkdir -p temp_deploy
    
    # Copy core folders
    cp -r app temp_deploy/
    cp -r routes temp_deploy/
    cp -r config temp_deploy/
    cp -r bootstrap temp_deploy/
    cp -r artisan temp_deploy/
    cp -r composer.json temp_deploy/
    cp -r composer.lock temp_deploy/ 2>/dev/null || true
    
    # Copy storage if requested
    if [[ "$upload_storage" == "y" || "$upload_storage" == "Y" ]]; then
        echo -e "${YELLOW}📁 Including storage folder...${NC}"
        cp -r storage temp_deploy/
    fi
    
    # Copy public if requested
    if [[ "$upload_public" == "y" || "$upload_public" == "Y" ]]; then
        echo -e "${YELLOW}📁 Including public folder...${NC}"
        cp -r public temp_deploy/
    fi
    
    echo -e "${YELLOW}📤 Creating directory and uploading files...${NC}"
    sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "mkdir -p /home/u787393221/domains/api.bersekolah.com/project_files"
    sshpass -p "Bersekolah_123456" scp -P 65002 -o StrictHostKeyChecking=no -r temp_deploy/* u787393221@46.202.138.221:/home/u787393221/domains/api.bersekolah.com/project_files/
    
    # Upload public files to public_html (only images folder)
    if [[ "$upload_public" == "y" || "$upload_public" == "Y" ]]; then
        echo -e "${YELLOW}📁 Uploading images to public_html...${NC}"
        sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "mkdir -p /home/u787393221/domains/api.bersekolah.com/public_html"
        sshpass -p "Bersekolah_123456" scp -P 65002 -o StrictHostKeyChecking=no -r public/assets u787393221@46.202.138.221:/home/u787393221/domains/api.bersekolah.com/public_html/
    fi
    
    echo -e "${YELLOW}🔧 Setting permissions and optimizing...${NC}"
    sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
        cd /home/u787393221/domains/api.bersekolah.com/project_files
        chmod -R 755 .
        chmod -R 755 storage bootstrap/cache 2>/dev/null || true
        php artisan storage:link 2>/dev/null || true
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        echo '✅ Files deployed with storage/public successfully'
    "
    
    # Cleanup temp directory
    rm -rf temp_deploy
    
    echo -e "${GREEN}✅ Files deployed successfully!${NC}"
    echo -e "${CYAN}🔗 API: https://api.bersekolah.com${NC}"
}

# Function untuk deploy images saja
deploy_images_only() {
    echo -e "${PURPLE}🖼️  Deploying Images Only to public_html...${NC}"
    
    # Check if assets folder exists
    if [ ! -d "public/assets" ]; then
        echo -e "${RED}❌ Error: Folder public/assets tidak ditemukan${NC}"
        echo -e "${YELLOW}💡 Pastikan Anda berada di root directory Bersekolah API${NC}"
        return 1
    fi
    
    # Test SSH first
    if ! test_ssh; then
        return 1
    fi
    
    echo -e "${YELLOW}📁 Uploading assets folder to public_html...${NC}"
    
    # Create public_html directory if not exists
    sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "mkdir -p /home/u787393221/domains/api.bersekolah.com/public_html"
    
    # Upload assets folder with force overwrite
    sshpass -p "Bersekolah_123456" scp -P 65002 -o StrictHostKeyChecking=no -r public/assets u787393221@46.202.138.221:/home/u787393221/domains/api.bersekolah.com/public_html/
    
    # Set proper permissions
    sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
        chmod -R 755 /home/u787393221/domains/api.bersekolah.com/public_html/assets
        echo '✅ Assets uploaded successfully to public_html'
    "
    
    echo -e "${GREEN}✅ Images deployed successfully to public_html!${NC}"
    echo -e "${CYAN}🔗 API: https://api.bersekolah.com${NC}"
    echo -e "${YELLOW}📁 Assets path: /home/u787393221/domains/api.bersekolah.com/public_html/assets/${NC}"
}

# Function untuk deploy full
deploy_full() {
    echo -e "${PURPLE}🚀 Deploying Full Backend API...${NC}"
    
    # Check if we're in the right directory
    if [ ! -f "composer.json" ]; then
        echo -e "${RED}❌ Error: Tidak ada file composer.json${NC}"
        echo -e "${YELLOW}💡 Pastikan Anda berada di folder bersekolah_rest_api${NC}"
        return 1
    fi
    
    # Test SSH first
    if ! test_ssh; then
        return 1
    fi
    
    echo -e "${YELLOW}📦 Installing dependencies...${NC}"
    composer install --no-dev --optimize-autoloader --no-interaction
    
    echo -e "${YELLOW}📤 Uploading files...${NC}"
    sshpass -p "Bersekolah_123456" scp -P 65002 -o StrictHostKeyChecking=no -r . u787393221@46.202.138.221:/home/u787393221/domains/api.bersekolah.com/project_files/
    
    echo -e "${YELLOW}🔧 Setting permissions and running migrations...${NC}"
    sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
        cd /home/u787393221/domains/api.bersekolah.com/project_files
        chmod -R 755 .
        chmod -R 755 storage bootstrap/cache
        php artisan migrate --force
        php artisan storage:link
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        echo '✅ Backend setup completed'
    "
    
    echo -e "${GREEN}✅ Backend deployed successfully!${NC}"
    echo -e "${CYAN}🔗 API: https://api.bersekolah.com${NC}"
}

# Function untuk cek status API
check_status() {
    echo -e "${YELLOW}🔍 Checking API status...${NC}"
    
    echo -e "${BLUE}Backend API (api.bersekolah.com):${NC}"
    if curl -s -o /dev/null -w "%{http_code}" https://api.bersekolah.com | grep -q "200"; then
        echo -e "${GREEN}✅ API: Online${NC}"
    else
        echo -e "${RED}❌ API: Offline${NC}"
    fi
    
    echo -e "${BLUE}API Health Check:${NC}"
    if curl -s https://api.bersekolah.com/api/health 2>/dev/null | grep -q "ok"; then
        echo -e "${GREEN}✅ API Health: OK${NC}"
    else
        echo -e "${YELLOW}⚠️  API Health: Unknown${NC}"
    fi
}

# Function untuk run migrations
run_migrations() {
    echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
    
    if ! test_ssh; then
        return 1
    fi
    
    sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
        cd /home/u787393221/domains/api.bersekolah.com/project_files
        php artisan migrate --force
        echo '✅ Migrations completed'
    "
    
    echo -e "${GREEN}✅ Database migrations completed!${NC}"
}

# Function untuk setup Bersekolah System
setup_bersekolah_system() {
    echo -e "${PURPLE}🎓 Setting up Bersekolah System...${NC}"
    
    # Test SSH first
    if ! test_ssh; then
        return 1
    fi
    
    echo -e "${YELLOW}📋 Pilih opsi setup:${NC}"
    echo -e "${BLUE}1.${NC} Fresh Install (Migration + Seeder)"
    echo -e "${BLUE}2.${NC} Migration Only"
    echo -e "${BLUE}3.${NC} Seeder Only"
    echo -e "${BLUE}4.${NC} Initialize Beasiswa Data"
    echo -e "${BLUE}5.${NC} Kembali ke menu utama"
    echo ""
    
    read -p "Pilih opsi (1-5): " setup_choice
    
    case $setup_choice in
        1)
            echo -e "${YELLOW}🚀 Running fresh install...${NC}"
            sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
                cd /home/u787393221/domains/api.bersekolah.com/project_files
                php artisan migrate:fresh --force
                php artisan db:seed --force
                echo '✅ Fresh install completed successfully'
            "
            ;;
        2)
            echo -e "${YELLOW}🗄️ Running migrations...${NC}"
            sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
                cd /home/u787393221/domains/api.bersekolah.com/project_files
                php artisan migrate --force
                echo '✅ Migrations completed successfully'
            "
            ;;
        3)
            echo -e "${YELLOW}🌱 Running seeders...${NC}"
            sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
                cd /home/u787393221/domains/api.bersekolah.com/project_files
                php artisan db:seed --force
                echo '✅ Seeders completed successfully'
            "
            ;;
        4)
            echo -e "${YELLOW}📊 Initializing beasiswa data...${NC}"
            sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
                cd /home/u787393221/domains/api.bersekolah.com/project_files
                php artisan db:seed --class=BeasiswaSeeder --force
                echo '✅ Beasiswa data initialized successfully'
            "
            ;;
        5)
            return 0
            ;;
        *)
            echo -e "${RED}❌ Pilihan tidak valid${NC}"
            ;;
    esac
}

# Function untuk update database schema
update_database() {
    echo -e "${PURPLE}🗄️  Database Schema Update Tool...${NC}"
    
    echo -e "${YELLOW}📋 Pilih opsi database update:${NC}"
    echo -e "${BLUE}1.${NC} Lihat migration yang belum dijalankan"
    echo -e "${BLUE}2.${NC} Jalankan migration tertentu"
    echo -e "${BLUE}3.${NC} Rollback migration terakhir"
    echo -e "${BLUE}4.${NC} Reset database (HATI-HATI!)"
    echo -e "${BLUE}5.${NC} Kembali ke menu utama"
    echo ""
    
    read -p "Pilih opsi (1-5): " db_choice
    
    case $db_choice in
        1)
            echo -e "${YELLOW}🔍 Checking pending migrations...${NC}"
            sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
                cd /home/u787393221/domains/api.bersekolah.com/project_files
                php artisan migrate:status
            "
            ;;
        2)
            echo -e "${YELLOW}🔍 Menampilkan list migration yang tersedia...${NC}"
            sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
                cd /home/u787393221/domains/api.bersekolah.com/project_files
                echo '📋 Migration Status:'
                php artisan migrate:status
                echo ''
                echo '📁 Available Migration Files:'
                ls -la database/migrations/ | grep -E '\.php$' | awk '{print \$9}' | sed 's/\.php$//' | nl
            "
            echo ""
            echo -e "${YELLOW}📝 Masukkan nama migration file (tanpa .php):${NC}"
            echo -e "${BLUE}Contoh: 2024_01_15_123456_create_users_table${NC}"
            read -p "Migration name: " migration_name
            if [ -z "$migration_name" ]; then
                echo -e "${RED}❌ Nama migration tidak boleh kosong${NC}"
            else
                echo -e "${YELLOW}🚀 Running migration: $migration_name...${NC}"
                sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
                    cd /home/u787393221/domains/api.bersekolah.com/project_files
                    php artisan migrate --path=database/migrations/$migration_name.php
                "
                echo -e "${GREEN}✅ Migration $migration_name berhasil dijalankan!${NC}"
            fi
            ;;
        3)
            echo -e "${YELLOW}⚠️  Rolling back last migration...${NC}"
            sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
                cd /home/u787393221/domains/api.bersekolah.com/project_files
                php artisan migrate:rollback --step=1
            "
            ;;
        4)
            echo -e "${RED}⚠️  PERINGATAN: Ini akan menghapus semua data!${NC}"
            echo -e "${YELLOW}Ketik 'RESET' untuk konfirmasi:${NC}"
            read -p "Konfirmasi: " confirm
            if [[ "$confirm" == "RESET" ]]; then
                echo -e "${YELLOW}🔄 Resetting database...${NC}"
                sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
                    cd /home/u787393221/domains/api.bersekolah.com/project_files
                    php artisan migrate:reset
                    php artisan migrate
                "
            else
                echo -e "${GREEN}✅ Reset dibatalkan${NC}"
            fi
            ;;
        5)
            return 0
            ;;
        *)
            echo -e "${RED}❌ Pilihan tidak valid${NC}"
            ;;
    esac
}

# Function untuk clear cache
clear_cache() {
    echo -e "${YELLOW}🧹 Clearing cache...${NC}"
    
    if ! test_ssh; then
        return 1
    fi
    
    sshpass -p "Bersekolah_123456" ssh -p 65002 -o StrictHostKeyChecking=no u787393221@46.202.138.221 "
        cd /home/u787393221/domains/api.bersekolah.com/project_files
        php artisan cache:clear
        php artisan config:clear
        php artisan route:clear
        php artisan view:clear
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        echo '✅ Cache cleared and optimized'
    "
    
    echo -e "${GREEN}✅ Cache cleared and optimized!${NC}"
}

# Check if sshpass is installed
if ! command -v sshpass &> /dev/null; then
    echo -e "${RED}❌ sshpass tidak ditemukan!${NC}"
    echo -e "${YELLOW}💡 Install sshpass terlebih dahulu:${NC}"
    echo -e "${BLUE}macOS:${NC} brew install hudochenkov/sshpass/sshpass"
    echo -e "${BLUE}Linux:${NC} sudo apt-get install sshpass"
    exit 1
fi

# Main menu loop
while true; do
    echo ""
    show_menu
    read -p "Pilih opsi (1-10): " choice
    
    case $choice in
        1)
            deploy_full
            ;;
        2)
            deploy_core
            ;;
        3)
            deploy_with_storage
            ;;
        4)
            deploy_images_only
            ;;
        5)
            test_ssh
            ;;
        6)
            check_status
            ;;
        7)
            update_database
            ;;
        8)
            setup_bersekolah_system
            ;;
        9)
            clear_cache
            ;;
        10)
            echo -e "${GREEN}👋 Terima kasih! Happy coding! 🚀${NC}"
            exit 0
            ;;
        *)
            echo -e "${RED}❌ Pilihan tidak valid. Pilih 1-10.${NC}"
            ;;
    esac
    
    echo ""
    read -p "Tekan Enter untuk melanjutkan..."
done
