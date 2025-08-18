<?php
require_once 'config.php';

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=matrimonial_db;charset=utf8mb4",
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    echo "Checking user details:\n";
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll();
    
    foreach ($users as $user) {
        echo "ID: {$user['id']}\n";
        echo "Profile ID: {$user['profile_id']}\n";
        echo "Name: {$user['first_name']} {$user['last_name']}\n";
        echo "Email: {$user['email']}\n";
        echo "Phone: {$user['phone']}\n";
        echo "Gender: {$user['gender']}\n";
        echo "Date of Birth: {$user['date_of_birth']}\n";
        echo "Religion: {$user['religion']}\n";
        echo "Status: {$user['status']}\n";
        echo "Email Verified: " . ($user['email_verified'] ? 'Yes' : 'No') . "\n";
        echo "Created: {$user['created_at']}\n";
        echo "Password Hash: " . substr($user['password'], 0, 20) . "...\n";
        echo "---\n";
    }
    
    // Test login for this user
    echo "\nTesting login functionality:\n";
    $user = new User();
    
    // Try with a common test password
    $testPasswords = ['123456', 'password', 'test123', 'admin123'];
    foreach ($testPasswords as $testPassword) {
        echo "Testing password: $testPassword\n";
        $result = $user->login('sajnussaharearhojayfa@gmail.com', $testPassword);
        echo "Result: " . json_encode($result) . "\n";
        if ($result['success']) {
            break;
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
