<?php
require_once 'config/database.php';

try {
    // Test database connection
    echo "Testing database connection...\n";
    $pdo->query('SELECT 1');
    echo "Database connection successful!\n\n";

    // Check if users table exists
    echo "Checking users table...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        echo "Error: Users table does not exist!\n";
        exit(1);
    }
    echo "Users table exists.\n\n";

    // Check if default users exist
    echo "Checking default users...\n";
    $stmt = $pdo->query("SELECT username, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "Error: No users found in database!\n";
        echo "Importing default users...\n";
        
        // Import default users
        $schema = file_get_contents('database/schema.sql');
        $pdo->exec($schema);
        
        echo "Default users imported.\n";
    } else {
        echo "Found " . count($users) . " users:\n";
        foreach ($users as $user) {
            echo "- {$user['username']} ({$user['role']})\n";
        }
    }

    echo "\nAll tests completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
