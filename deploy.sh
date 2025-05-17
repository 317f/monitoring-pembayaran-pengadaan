#!/bin/bash

# Deployment script for Monitoring Pembayaran Pengadaan

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored messages
print_message() {
    echo -e "${2}${1}${NC}"
}

# Check if running with root privileges
if [ "$EUID" -ne 0 ]; then
    print_message "Please run this script as root or with sudo" "$RED"
    exit 1
fi

# Welcome message
print_message "=== Monitoring Pembayaran Pengadaan Deployment Script ===" "$YELLOW"
echo

# Check system requirements
print_message "Checking system requirements..." "$YELLOW"

# Check PHP
if ! command -v php &> /dev/null; then
    print_message "PHP is not installed. Installing PHP and required extensions..." "$YELLOW"
    apt-get update
    apt-get install -y php php-mysql php-gd php-fileinfo php-json php-mbstring
else
    print_message "PHP is installed" "$GREEN"
fi

# Check MySQL
if ! command -v mysql &> /dev/null; then
    print_message "MySQL is not installed. Please install MySQL server first." "$RED"
    exit 1
else
    print_message "MySQL is installed" "$GREEN"
fi

# Create necessary directories
print_message "Creating necessary directories..." "$YELLOW"
directories=(
    "public/uploads"
    "views/errors"
    "views/auth"
    "views/payments"
    "views/layouts"
    "config"
    "models"
    "controllers"
)

for dir in "${directories[@]}"; do
    if [ ! -d "$dir" ]; then
        mkdir -p "$dir"
        chmod 755 "$dir"
        print_message "Created directory: $dir" "$GREEN"
    fi
done

# Set proper permissions
print_message "Setting file permissions..." "$YELLOW"
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod 755 maintenance.sh
chmod 755 deploy.sh

# Special permissions for upload directory
chmod -R 775 public/uploads
chown -R www-data:www-data public/uploads

print_message "File permissions set" "$GREEN"

# Create production environment file
if [ ! -f "config/env.local.php" ]; then
    print_message "Creating production environment configuration..." "$YELLOW"
    cat > config/env.local.php << EOF
<?php
define('APP_ENV', 'production');
define('APP_DEBUG', false);
define('APP_URL', 'http://'.\$_SERVER['HTTP_HOST']);
EOF
    print_message "Production environment file created" "$GREEN"
fi

# Database setup
print_message "Setting up database..." "$YELLOW"
read -p "Enter database name: " dbname
read -p "Enter database username: " dbuser
read -sp "Enter database password: " dbpass
echo
read -sp "Confirm database password: " dbpass2
echo

if [ "$dbpass" != "$dbpass2" ]; then
    print_message "Passwords do not match" "$RED"
    exit 1
fi

# Create database and user
mysql -e "CREATE DATABASE IF NOT EXISTS ${dbname}"
mysql -e "CREATE USER IF NOT EXISTS '${dbuser}'@'localhost' IDENTIFIED BY '${dbpass}'"
mysql -e "GRANT ALL PRIVILEGES ON ${dbname}.* TO '${dbuser}'@'localhost'"
mysql -e "FLUSH PRIVILEGES"

# Import database schema
if [ -f "database/schema.sql" ]; then
    mysql -u "$dbuser" -p"$dbpass" "$dbname" < database/schema.sql
    print_message "Database schema imported successfully" "$GREEN"
else
    print_message "Database schema file not found" "$RED"
    exit 1
fi

# Update database configuration
print_message "Updating database configuration..." "$YELLOW"
cat > config/database.php << EOF
<?php
define('DB_HOST', 'localhost');
define('DB_USER', '${dbuser}');
define('DB_PASS', '${dbpass}');
define('DB_NAME', '${dbname}');

try {
    \$pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException \$e) {
    die("Connection failed: " . \$e->getMessage());
}
EOF

print_message "Database configuration updated" "$GREEN"

# Clean up installation files
print_message "Cleaning up..." "$YELLOW"
if [ -f "install.php" ]; then
    rm install.php
    print_message "Removed installation file" "$GREEN"
fi

# Create .htaccess backup
if [ -f ".htaccess" ]; then
    cp .htaccess .htaccess.bak
    print_message "Created .htaccess backup" "$GREEN"
fi

# Final steps
print_message "
=== Deployment Complete ===

Please complete these final steps:
1. Configure your web server to point to this directory
2. Ensure mod_rewrite is enabled for Apache
3. Set up SSL certificate for production
4. Remove or secure this deployment script
5. Default login credentials:
   - Admin: admin/admin123
   - User: user/user123
   
Important: Change the default passwords immediately!
" "$GREEN"

# Ask to remove deployment script
read -p "Remove this deployment script? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    rm -- "$0"
    print_message "Deployment script removed" "$GREEN"
fi
