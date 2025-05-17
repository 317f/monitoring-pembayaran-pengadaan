#!/bin/bash

# Database Backup and Restore Script for Monitoring Pembayaran Pengadaan

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored messages
print_message() {
    echo -e "${2}${1}${NC}"
}

# Function to extract database credentials from config file
get_db_credentials() {
    if [ ! -f "config/database.php" ]; then
        print_message "Database configuration file not found!" "$RED"
        exit 1
    }

    DB_HOST=$(grep "DB_HOST" config/database.php | cut -d "'" -f 4)
    DB_USER=$(grep "DB_USER" config/database.php | cut -d "'" -f 4)
    DB_NAME=$(grep "DB_NAME" config/database.php | cut -d "'" -f 4)
}

# Function to create backup
create_backup() {
    # Create backups directory if it doesn't exist
    mkdir -p backups

    # Generate backup filename with timestamp
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    BACKUP_FILE="backups/db_backup_${TIMESTAMP}.sql"
    
    print_message "Creating database backup..." "$YELLOW"
    
    # Prompt for database password
    read -sp "Enter database password: " DB_PASS
    echo

    # Create backup with schema and data
    if mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" \
        --add-drop-table \
        --add-locks \
        --create-options \
        --disable-keys \
        --extended-insert \
        --single-transaction \
        --quick \
        --set-charset \
        --triggers \
        --routines \
        --comments \
        "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null; then
        
        # Compress the backup
        gzip "$BACKUP_FILE"
        print_message "Backup created successfully: ${BACKUP_FILE}.gz" "$GREEN"
        
        # Create MD5 checksum
        md5sum "${BACKUP_FILE}.gz" > "${BACKUP_FILE}.gz.md5"
        print_message "MD5 checksum created" "$GREEN"
        
        # Set proper permissions
        chmod 600 "${BACKUP_FILE}.gz"
        chmod 600 "${BACKUP_FILE}.gz.md5"
    else
        print_message "Failed to create backup" "$RED"
        exit 1
    fi
}

# Function to restore backup
restore_backup() {
    # List available backups
    print_message "\nAvailable backups:" "$YELLOW"
    ls -1 backups/*.sql.gz 2>/dev/null
    echo

    # Prompt for backup selection
    read -p "Enter backup file name to restore (or 'q' to quit): " BACKUP_FILE
    
    if [ "$BACKUP_FILE" = "q" ]; then
        exit 0
    fi

    if [ ! -f "$BACKUP_FILE" ]; then
        print_message "Backup file not found!" "$RED"
        exit 1
    fi

    print_message "WARNING: This will overwrite the current database!" "$RED"
    read -p "Are you sure you want to continue? (y/n): " -n 1 -r
    echo

    if [[ $REPLY =~ ^[Yy]$ ]]; then
        # Verify MD5 checksum
        if [ -f "${BACKUP_FILE}.md5" ]; then
            print_message "Verifying backup integrity..." "$YELLOW"
            if ! md5sum -c "${BACKUP_FILE}.md5"; then
                print_message "Backup file is corrupted!" "$RED"
                exit 1
            fi
        fi

        # Prompt for database password
        read -sp "Enter database password: " DB_PASS
        echo

        print_message "Restoring database..." "$YELLOW"
        
        # Decompress and restore
        gunzip < "$BACKUP_FILE" | mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME"
        
        if [ $? -eq 0 ]; then
            print_message "Database restored successfully!" "$GREEN"
        else
            print_message "Failed to restore database" "$RED"
            exit 1
        fi
    fi
}

# Function to clean old backups
clean_old_backups() {
    read -p "Enter number of days to keep backups (older backups will be deleted): " DAYS
    
    if ! [[ "$DAYS" =~ ^[0-9]+$ ]]; then
        print_message "Please enter a valid number" "$RED"
        exit 1
    fi

    print_message "Cleaning old backups..." "$YELLOW"
    
    find backups/ -name "db_backup_*.sql.gz" -mtime +$DAYS -exec rm {} \;
    find backups/ -name "db_backup_*.sql.gz.md5" -mtime +$DAYS -exec rm {} \;
    
    print_message "Old backups cleaned successfully" "$GREEN"
}

# Function to show backup statistics
show_stats() {
    print_message "\nBackup Statistics:" "$YELLOW"
    
    # Count total backups
    TOTAL_BACKUPS=$(ls -1 backups/*.sql.gz 2>/dev/null | wc -l)
    echo "Total backups: $TOTAL_BACKUPS"
    
    # Show latest backup
    LATEST_BACKUP=$(ls -t backups/*.sql.gz 2>/dev/null | head -1)
    if [ ! -z "$LATEST_BACKUP" ]; then
        echo "Latest backup: $LATEST_BACKUP"
        echo "Size: $(du -h "$LATEST_BACKUP" | cut -f1)"
        echo "Date: $(date -r "$LATEST_BACKUP")"
    fi
    
    # Show total size
    echo "Total size: $(du -sh backups 2>/dev/null | cut -f1)"
    
    # Show available disk space
    echo "Available disk space: $(df -h . | awk 'NR==2 {print $4}')"
}

# Get database credentials
get_db_credentials

# Main menu
while true; do
    echo
    print_message "=== Database Backup Menu ===" "$YELLOW"
    echo "1. Create Backup"
    echo "2. Restore Backup"
    echo "3. Clean Old Backups"
    echo "4. Show Statistics"
    echo "5. Exit"
    echo
    
    read -p "Select an option (1-5): " option
    echo
    
    case $option in
        1)
            create_backup
            ;;
        2)
            restore_backup
            ;;
        3)
            clean_old_backups
            ;;
        4)
            show_stats
            ;;
        5)
            print_message "Exiting backup script" "$GREEN"
            exit 0
            ;;
        *)
            print_message "Invalid option" "$RED"
            ;;
    esac
done
