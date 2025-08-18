<?php
session_start();
require_once 'config.php';

// Simulate a logged-in user for testing
if (!isset($_SESSION['user_id'])) {
    echo "No user logged in. Testing registration first...\n";
    
    // Create a test user
    $userData = [
        'firstName' => 'Test',
        'lastName' => 'User',
        'email' => 'test@example.com',
        'phone' => '1234567890',
        'gender' => 'male',
        'dateOfBirth' => '1990-01-01',
        'religion' => 'hindu',
        'password' => 'test123'
    ];
    
    $user = new User();
    $result = $user->register($userData);
    
    if ($result['success']) {
        echo "Test user created successfully!\n";
        echo "Profile ID: " . $result['profile_id'] . "\n";
    } else {
        echo "Failed to create test user: " . $result['message'] . "\n";
        exit;
    }
}

// Now test the profile data retrieval
echo "\nTesting profile data retrieval...\n";

$user_id = $_SESSION['user_id'];

try {
    $user_stmt = $pdo->prepare("
        SELECT u.first_name, u.last_name, u.email, u.profile_id, u.gender, u.date_of_birth, u.religion
        FROM users u 
        WHERE u.id = ?
    ");
    $user_stmt->execute([$user_id]);
    $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);

    if ($user_data) {
        echo "User data found:\n";
        echo "Name: " . $user_data['first_name'] . " " . $user_data['last_name'] . "\n";
        echo "Email: " . $user_data['email'] . "\n";
        echo "Profile ID: " . $user_data['profile_id'] . "\n";
        echo "Gender: " . $user_data['gender'] . "\n";
        echo "Date of Birth: " . $user_data['date_of_birth'] . "\n";
        echo "Religion: " . $user_data['religion'] . "\n";
        
        // Check if user_profiles table exists
        $check_table = $pdo->query("SHOW TABLES LIKE 'user_profiles'");
        if ($check_table->rowCount() > 0) {
            echo "\nuser_profiles table exists.\n";
        } else {
            echo "\nuser_profiles table does NOT exist. Creating it...\n";
            
            // Create the user_profiles table
            $create_sql = "CREATE TABLE IF NOT EXISTS user_profiles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                marital_status ENUM('never_married', 'divorced', 'widowed', 'separated') DEFAULT 'never_married',
                have_children ENUM('no_children', 'yes_living_together', 'yes_not_living_together') DEFAULT 'no_children',
                height VARCHAR(20),
                weight VARCHAR(20),
                body_type ENUM('slim', 'average', 'athletic', 'heavy') DEFAULT 'average',
                complexion ENUM('very_fair', 'fair', 'wheatish', 'dark', 'very_dark') DEFAULT 'fair',
                hair_color ENUM('black', 'brown', 'blonde', 'gray', 'bald', 'others') DEFAULT 'black',
                eye_color ENUM('black', 'brown', 'blue', 'green', 'gray', 'hazel', 'others') DEFAULT 'black',
                disabilities ENUM('none', 'physical', 'hearing', 'visual', 'speech', 'others') DEFAULT 'none',
                blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'others') DEFAULT 'others',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )";
            
            $pdo->exec($create_sql);
            echo "user_profiles table created successfully!\n";
        }
        
    } else {
        echo "No user data found for user_id: $user_id\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nSession data:\n";
print_r($_SESSION);
?>
