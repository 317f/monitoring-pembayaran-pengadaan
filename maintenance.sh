#!/bin/bash

# Maintenance script for Monitoring Pembayaran Pengadaan

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored messages
print_message() {
    echo -e "${2}${1}${NC}"
}

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Function to backup database
backup_database() {
    if [ -f "config/database.php" ]; then
        # Extract database credentials from config file
        DB_HOST=$(grep "DB_HOST" config/database.php | cut -d "'" -f 4)
        DB_USER=$(grep "DB_USER" config/database.php | cut -d "'" -f 4)
        DB_NAME=$(grep "DB_NAME" config/database.php | cut -d "'" -f 4)
        
        # Create backups directory if it doesn't exist
        mkdir -p backups
        
        # Generate backup filename with timestamp
        BACKUP_FILE="backups/db_backup_$(date +%Y%m%d_%H%M%S).sql"
        
        print_message "Creating database backup..." "$YELLOW"
        
        # Prompt for database password
        read -sp "Enter database password: " DB_PASS
        echo
        
        # Create backup
        if mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null; then
            print_message "Backup created successfully: $BACKUP_FILE" "$GREEN"
        else
            print_message "Failed to create backup" "$RED"
            exit 1
        fi
    else
        print_message "Database configuration file not found" "$RED"
        exit 1
    fi
}

# Function to check file permissions
check_permissions() {
    print_message "Checking file permissions..." "$YELLOW"
    
    # List of directories to check
    DIRECTORIES=(
        "public/uploads"
        "config"
        "views"
        "models"
        "controllers"
    )
    
    # Check each directory
    for dir in "${DIRECTORIES[@]}"; do
        if [ -d "$dir" ]; then
            current_perm=$(stat -c "%a" "$dir")
            print_message "Permission for $dir: $current_perm" "$YELLOW"
            
            # Fix permissions if needed
            if [ "$current_perm" != "755" ]; then
                read -p "Fix permissions for $dir? (y/n) " -n 1 -r
                echo
                if [[ $REPLY =~ ^[Yy]$ ]]; then
                    chmod -R 755 "$dir"
                    print_message "Fixed permissions for $dir" "$GREEN"
                fi
            fi
        else
            print_message "Directory not found: $dir" "$RED"
        fi
    done
}

# Function to clear cache
clear_cache() {
    print_message "Clearing cache..." "$YELLOW"
    
    # Remove any PHP session files
    if [ -d "/tmp" ]; then
        rm -f /tmp/sess_*
        print_message "PHP session files cleared" "$GREEN"
    fi
    
    # Clear uploaded temp files
    if [ -d "public/uploads/temp" ]; then
        rm -rf public/uploads/temp/*
        print_message "Temporary upload files cleared" "$GREEN"
    fi
}

# Function to check system requirements
check_requirements() {
    print_message "Checking system requirements..." "$YELLOW"
    
    # Check PHP version
    if command_exists php; then
        PHP_VERSION=$(php -r "echo PHP_VERSION;")
        print_message "PHP Version: $PHP_VERSION" "$GREEN"
    else
        print_message "PHP not found" "$RED"
    fi
    
    # Check MySQL version
    if command_exists mysql; then
        MYSQL_VERSION=$(mysql --version | awk '{print $5}' | cut -d',' -f1)
        print_message "MySQL Version: $MYSQL_VERSION" "$GREEN"
    else
        print_message "MySQL not found" "$RED"
    fi
    
    # Check required PHP extensions
    REQUIRED_EXTENSIONS=("pdo" "pdo_mysql" "gd" "fileinfo")
    for ext in "${REQUIRED_EXTENSIONS[@]}"; do
        if php -m | grep -q "^$ext$"; then
            print_message "PHP Extension $ext: Installed" "$GREEN"
        else
            print_message "PHP Extension $ext: Not installed" "$RED"
        fi
    done
}

# Main menu
while true; do
    echo
    print_message "=== Maintenance Menu ===" "$YELLOW"
    echo "1. Backup Database"
    echo "2. Check Permissions"
    echo "3. Clear Cache"
    echo "4. Check Requirements"
    echo "5. Exit"
    echo
    
    read -p "Select an option (1-5): " option
    echo
    
    case $option in
        1)
            backup_database
            ;;
        2)
            check_permissions
            ;;
        3)
            clear_cache
            ;;
        4)
            check_requirements
            ;;
        5)
            print_message "Exiting maintenance script" "$GREEN"
            exit 0
            ;;
        *)
            print_message "Invalid option" "$RED"
            ;;
    esac
done
