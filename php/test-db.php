<?php
// Database connection test
require_once 'config.php';

try {
    echo "Testing database connection...\n";
    
    // Test basic connection
    $stmt = $pdo->query("SELECT 1");
    echo "✓ Database connection successful\n";
    
    // Test if database exists
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch();
    echo "✓ Connected to database: " . $result['db_name'] . "\n";
    
    // Test if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Users table exists\n";
    } else {
        echo "✗ Users table does not exist\n";
    }
    
    // Test if user_profiles table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'user_profiles'");
    if ($stmt->rowCount() > 0) {
        echo "✓ User_profiles table exists\n";
        
        // Check table structure
        $stmt = $pdo->query("DESCRIBE user_profiles");
        echo "User_profiles table columns:\n";
        while ($row = $stmt->fetch()) {
            echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "✗ User_profiles table does not exist\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "✗ General error: " . $e->getMessage() . "\n";
}
?>
