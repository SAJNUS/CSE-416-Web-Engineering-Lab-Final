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

// Check if file was uploaded
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['photo'];
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
$max_size = 5 * 1024 * 1024; // 5MB

// Validate file type
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, and GIF are allowed.']);
    exit;
}

// Validate file size
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB.']);
    exit;
}

try {
    // Create uploads directory if it doesn't exist
    $upload_dir = '../uploads/profiles/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $user_id . '_' . time() . '.' . $file_extension;
    $file_path = $upload_dir . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        // Update database
        $relative_path = 'uploads/profiles/' . $filename;
        
        // Check if profile exists
        $check_stmt = $pdo->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
        $check_stmt->execute([$user_id]);
        $existing_profile = $check_stmt->fetch();

        if ($existing_profile) {
            // Update existing profile
            $update_stmt = $pdo->prepare("UPDATE user_profiles SET photo_url = ? WHERE user_id = ?");
            $update_stmt->execute([$relative_path, $user_id]);
        } else {
            // Get user's gender to determine looking_for
            $user_stmt = $pdo->prepare("SELECT gender FROM users WHERE id = ?");
            $user_stmt->execute([$user_id]);
            $user_data = $user_stmt->fetch();
            $looking_for = ($user_data['gender'] === 'male') ? 'bride' : 'groom';

            // Insert new profile with photo
            $insert_stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, photo_url, looking_for) VALUES (?, ?, ?)");
            $insert_stmt->execute([$user_id, $relative_path, $looking_for]);
        }

        // Also insert into user_photos table
        $photo_stmt = $pdo->prepare("INSERT INTO user_photos (user_id, photo_url, is_primary, is_approved) VALUES (?, ?, 1, 0)");
        $photo_stmt->execute([$user_id, $relative_path]);

        echo json_encode(['success' => true, 'message' => 'Photo uploaded successfully', 'photo_url' => $relative_path]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
    }

} catch (PDOException $e) {
    error_log("Photo upload error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
