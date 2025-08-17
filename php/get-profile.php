<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Get user basic info
    $user_stmt = $pdo->prepare("
        SELECT u.first_name, u.last_name, u.email, u.profile_id, u.gender, u.date_of_birth, u.religion
        FROM users u 
        WHERE u.id = ?
    ");
    $user_stmt->execute([$user_id]);
    $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // Get profile data
    $profile_stmt = $pdo->prepare("
        SELECT * FROM user_profiles 
        WHERE user_id = ?
    ");
    $profile_stmt->execute([$user_id]);
    $profile_data = $profile_stmt->fetch(PDO::FETCH_ASSOC);

    // Combine user and profile data
    $response_data = [
        'success' => true,
        'user' => $user_data,
        'profile' => $profile_data ?: []
    ];

    echo json_encode($response_data);

} catch (PDOException $e) {
    error_log("Profile fetch error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
