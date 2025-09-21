#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Banner
echo -e "${CYAN}
╔══════════════════════════════════════════════════════════════╗
║                ⚙️  BERSEKOLAH BACKEND DEPLOYMENT ⚙️           ║
║                                                              ║
║  Script mudah untuk deploy backend API Bersekolah ke Hostinger ║
╚══════════════════════════════════════════════════════════════╝
${NC}"

# Configuration
HOSTINGER_HOST="u123456789.hostinger.com"
HOSTINGER_USER="u123456789"
HOSTINGER_PASS="your_password_here"
REMOTE_PATH="/domains/api.bersekolah.com/public_html"

# Function to test SSH connection
test_ssh() {
    echo -e "${YELLOW}🔐 Testing SSH connection...${NC}"
    sshpass -p "$HOSTINGER_PASS" ssh -o StrictHostKeyChecking=no "$HOSTINGER_USER@$HOSTINGER_HOST" "echo 'SSH connection successful!'" 2>/dev/null
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ SSH connection berhasil!${NC}"
        return 0
    else
        echo -e "${RED}❌ SSH connection gagal!${NC}"
        return 1
    fi
}

# Function to deploy full backend
deploy_full() {
    echo -e "${PURPLE}🚀 Deploying Full Backend API...${NC}"
    
    if ! test_ssh; then
        echo -e "${RED}❌ Deployment dibatalkan karena SSH connection gagal${NC}"
        return 1
    fi
    
    echo -e "${YELLOW}📦 Preparing backend files...${NC}"
    
    # Create temporary directory for backend files
    TEMP_DIR=$(mktemp -d)
    cp -r . "$TEMP_DIR/backend"
    cd "$TEMP_DIR/backend"
    
    # Remove unnecessary files
    rm -rf node_modules vendor storage/logs/*.log .env
    
    echo -e "${YELLOW}📤 Uploading backend files...${NC}"
    sshpass -p "$HOSTINGER_PASS" rsync -avz --delete . "$HOSTINGER_USER@$HOSTINGER_HOST:$REMOTE_PATH/"
    
    if [ $? -eq 0 ]; then
        echo -e "${YELLOW}🔧 Setting permissions...${NC}"
        sshpass -p "$HOSTINGER_PASS" ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "chmod -R 755 $REMOTE_PATH"
        echo -e "${GREEN}✅ Full backend deployed successfully!${NC}"
        echo -e "${CYAN}🔗 API: https://api.bersekolah.com${NC}"
    else
        echo -e "${RED}❌ Backend upload gagal!${NC}"
        return 1
    fi
    
    # Cleanup
    cd /Users/rhea/Downloads/bersekolah/bersekolah_rest_api-main
    rm -rf "$TEMP_DIR"
}

# Function to deploy core files only
deploy_core() {
    echo -e "${PURPLE}⚙️ Deploying Core Files (app, routes, config)...${NC}"
    
    if ! test_ssh; then
        echo -e "${RED}❌ Deployment dibatalkan karena SSH connection gagal${NC}"
        return 1
    fi
    
    echo -e "${YELLOW}📦 Preparing core files for upload...${NC}"
    
    # Create temporary directory for core files
    TEMP_DIR=$(mktemp -d)
    mkdir -p "$TEMP_DIR/core"
    
    # Copy core files
    cp -r app "$TEMP_DIR/core/"
    cp -r routes "$TEMP_DIR/core/"
    cp -r config "$TEMP_DIR/core/"
    cp -r database "$TEMP_DIR/core/"
    cp -r bootstrap "$TEMP_DIR/core/"
    cp artisan "$TEMP_DIR/core/"
    cp composer.json "$TEMP_DIR/core/"
    cp composer.lock "$TEMP_DIR/core/"
    
    echo -e "${YELLOW}📤 Creating directory and uploading core files...${NC}"
    sshpass -p "$HOSTINGER_PASS" ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "mkdir -p $REMOTE_PATH"
    sshpass -p "$HOSTINGER_PASS" rsync -avz "$TEMP_DIR/core/" "$HOSTINGER_USER@$HOSTINGER_HOST:$REMOTE_PATH/"
    
    if [ $? -eq 0 ]; then
        echo -e "${YELLOW}🔧 Setting permissions...${NC}"
        sshpass -p "$HOSTINGER_PASS" ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "chmod -R 755 $REMOTE_PATH"
        
        # Run Laravel commands
        echo -e "${YELLOW}🔧 Running Laravel commands...${NC}"
        sshpass -p "$HOSTINGER_PASS" ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "cd $REMOTE_PATH && php artisan config:cache"
        sshpass -p "$HOSTINGER_PASS" ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "cd $REMOTE_PATH && php artisan route:cache"
        sshpass -p "$HOSTINGER_PASS" ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "cd $REMOTE_PATH && php artisan view:cache"
        
        echo -e "${GREEN}✅ Core files deployed successfully${NC}"
        echo -e "${GREEN}✅ Core files deployed successfully!${NC}"
        echo -e "${CYAN}🔗 API: https://api.bersekolah.com${NC}"
    else
        echo -e "${RED}❌ Core files upload gagal!${NC}"
        return 1
    fi
    
    # Cleanup
    cd /Users/rhea/Downloads/bersekolah/bersekolah_rest_api-main
    rm -rf "$TEMP_DIR"
}

# Function to deploy with storage
deploy_with_storage() {
    echo -e "${PURPLE}📁 Deploying with Storage & Public...${NC}"
    
    if ! test_ssh; then
        echo -e "${RED}❌ Deployment dibatalkan karena SSH connection gagal${NC}"
        return 1
    fi
    
    echo -e "${YELLOW}📦 Preparing files with storage...${NC}"
    
    # Create temporary directory
    TEMP_DIR=$(mktemp -d)
    cp -r . "$TEMP_DIR/backend"
    cd "$TEMP_DIR/backend"
    
    # Remove unnecessary files but keep storage
    rm -rf node_modules vendor storage/logs/*.log .env
    
    echo -e "${YELLOW}📤 Uploading files with storage...${NC}"
    sshpass -p "$HOSTINGER_PASS" rsync -avz --delete . "$HOSTINGER_USER@$HOSTINGER_HOST:$REMOTE_PATH/"
    
    if [ $? -eq 0 ]; then
        echo -e "${YELLOW}🔧 Setting permissions...${NC}"
        sshpass -p "$HOSTINGER_PASS" ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "chmod -R 755 $REMOTE_PATH"
        sshpass -p "$HOSTINGER_PASS" ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "chmod -R 777 $REMOTE_PATH/storage"
        echo -e "${GREEN}✅ Backend with storage deployed successfully!${NC}"
        echo -e "${CYAN}🔗 API: https://api.bersekolah.com${NC}"
    else
        echo -e "${RED}❌ Backend upload gagal!${NC}"
        return 1
    fi
    
    # Cleanup
    cd /Users/rhea/Downloads/bersekolah/bersekolah_rest_api-main
    rm -rf "$TEMP_DIR"
}

# Function to deploy images only
deploy_images() {
    echo -e "${PURPLE}🖼️ Deploying Images Only (ke public_html)...${NC}"
    
    if ! test_ssh; then
        echo -e "${RED}❌ Deployment dibatalkan karena SSH connection gagal${NC}"
        return 1
    fi
    
    echo -e "${YELLOW}📤 Uploading images...${NC}"
    sshpass -p "$HOSTINGER_PASS" rsync -avz public/assets/ "$HOSTINGER_USER@$HOSTINGER_HOST:$REMOTE_PATH/assets/"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Images deployed successfully!${NC}"
        echo -e "${CYAN}🔗 API: https://api.bersekolah.com${NC}"
    else
        echo -e "${RED}❌ Images upload gagal!${NC}"
        return 1
    fi
}

# Function to check API status
check_api_status() {
    echo -e "${YELLOW}🔍 Checking API status...${NC}"
    
    # Check API endpoint
    echo -e "${BLUE}API Status (api.bersekolah.com):${NC}"
    curl -s -o /dev/null -w "%{http_code}" https://api.bersekolah.com/api
    echo ""
    
    # Check specific endpoints
    echo -e "${BLUE}Testing specific endpoints:${NC}"
    curl -s -o /dev/null -w "Beasiswa Periods: %{http_code}\n" https://api.bersekolah.com/api/public/beasiswa-periods
    curl -s -o /dev/null -w "Testimoni: %{http_code}\n" https://api.bersekolah.com/api/testimoni
    curl -s -o /dev/null -w "Konten: %{http_code}\n" https://api.bersekolah.com/api/konten
}

# Function to update database schema
update_database() {
    echo -e "${PURPLE}🗄️ Updating Database Schema...${NC}"
    
    if ! test_ssh; then
        echo -e "${RED}❌ Deployment dibatalkan karena SSH connection gagal${NC}"
        return 1
    fi
    
    echo -e "${YELLOW}🔄 Running database migrations...${NC}"
    sshpass -p "$HOSTINGER_PASS" ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "cd $REMOTE_PATH && php artisan migrate --force"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Database schema updated successfully!${NC}"
    else
        echo -e "${RED}❌ Database update gagal!${NC}"
        return 1
    fi
}

# Function to setup bersekolah system
setup_system() {
    echo -e "${PURPLE}🔧 Setup Bersekolah System (Migration + Seeder)...${NC}"
    
    if ! test_ssh; then
        echo -e "${RED}❌ Deployment dibatalkan karena SSH connection gagal${NC}"
        return 1
    fi
    
    echo -e "${YELLOW}🔄 Running migrations and seeders...${NC}"
    sshpass -p "$HOSTINGER_PASS" ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "cd $REMOTE_PATH && php artisan migrate:fresh --seed --force"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Bersekolah system setup successfully!${NC}"
    else
        echo -e "${RED}❌ System setup gagal!${NC}"
        return 1
    fi
}

# Function to clear cache
clear_cache() {
    echo -e "${PURPLE}🧹 Clearing Cache...${NC}"
    
    if ! test_ssh; then
        echo -e "${RED}❌ Deployment dibatalkan karena SSH connection gagal${NC}"
        return 1
    fi
    
    echo -e "${YELLOW}🧹 Clearing all caches...${NC}"
    sshpass -p "$HOSTINGER_PASS" ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "cd $REMOTE_PATH && php artisan cache:clear"
    sshpass -p "$HOSTINGER_PASS" ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "cd $REMOTE_PATH && php artisan config:clear"
    sshpass -p "$HOSTINGER_PASS" ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "cd $REMOTE_PATH && php artisan route:clear"
    sshpass -p "$HOSTINGER_PASS" ssh "$HOSTINGER_USER@$HOSTINGER_HOST" "cd $REMOTE_PATH && php artisan view:clear"
    
    echo -e "${GREEN}✅ Cache cleared successfully!${NC}"
}

# Main menu
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
        deploy_images
        ;;
    5)
        test_ssh
        ;;
    6)
        check_api_status
        ;;
    7)
        update_database
        ;;
    8)
        setup_system
        ;;
    9)
        clear_cache
        ;;
    10)
        echo -e "${GREEN}👋 Sampai jumpa!${NC}"
        exit 0
        ;;
    *)
        echo -e "${RED}❌ Opsi tidak valid!${NC}"
        exit 1
        ;;
esac