<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

try {
    // First, get the user's basic information from the users table
    $user_stmt = $pdo->prepare("SELECT first_name, last_name, email, phone, gender, date_of_birth, religion FROM users WHERE id = ?");
    $user_stmt->execute([$user_id]);
    $user_data = $user_stmt->fetch();
    
    if (!$user_data) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Check if profile exists
    $check_stmt = $pdo->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
    $check_stmt->execute([$user_id]);
    $existing_profile = $check_stmt->fetch();

    // Determine looking_for based on user's gender
    $looking_for = ($user_data['gender'] === 'male') ? 'bride' : 'groom';

    if ($existing_profile) {
        // Update existing profile
        $update_sql = "UPDATE user_profiles SET 
            marital_status = ?,
            have_children = ?,
            height = ?,
            body_type = ?,
            complexion = ?,
            hair_color = ?,
            eye_color = ?,
            disabilities = ?,
            blood_group = ?,
            zodiac_sign = ?,
            education = ?,
            occupation = ?,
            company_name = ?,
            annual_income = ?,
            current_city = ?,
            current_state = ?,
            mother_tongue = ?,
            religion = ?,
            caste = ?,
            diet = ?,
            about_me = ?,
            partner_preferences = ?,
            looking_for = ?,
            updated_at = CURRENT_TIMESTAMP
            WHERE user_id = ?";

        $update_stmt = $pdo->prepare($update_sql);
        $result = $update_stmt->execute([
            $input['marital_status'] ?? 'never_married',
            $input['have_children'] ?? 'no_children',
            $input['height'] ?? null,
            $input['body_type'] ?? 'average',
            $input['complexion'] ?? 'fair',
            $input['hair_color'] ?? 'black',
            $input['eye_color'] ?? 'black',
            $input['disabilities'] ?? 'none',
            $input['blood_group'] ?? 'others',
            $input['zodiac_sign'] ?? null,
            $input['education'] ?? null,
            $input['occupation'] ?? null,
            $input['company_name'] ?? null,
            $input['annual_income'] ?? null,
            $input['current_city'] ?? null,
            $input['current_state'] ?? null,
            $input['mother_tongue'] ?? null,
            $user_data['religion'], // Use religion from signup
            $input['caste'] ?? null,
            $input['diet'] ?? 'vegetarian',
            $input['about_me'] ?? null,
            $input['partner_preferences'] ?? null,
            $looking_for,
            $user_id
        ]);

    } else {
        // Insert new profile with signup data
        $insert_sql = "INSERT INTO user_profiles (
            user_id,
            marital_status,
            have_children,
            height,
            body_type,
            complexion,
            hair_color,
            eye_color,
            disabilities,
            blood_group,
            zodiac_sign,
            education,
            occupation,
            company_name,
            annual_income,
            current_city,
            current_state,
            mother_tongue,
            religion,
            caste,
            diet,
            about_me,
            partner_preferences,
            looking_for
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $insert_stmt = $pdo->prepare($insert_sql);
        $result = $insert_stmt->execute([
            $user_id,
            $input['marital_status'] ?? 'never_married',
            $input['have_children'] ?? 'no_children',
            $input['height'] ?? null,
            $input['body_type'] ?? 'average',
            $input['complexion'] ?? 'fair',
            $input['hair_color'] ?? 'black',
            $input['eye_color'] ?? 'black',
            $input['disabilities'] ?? 'none',
            $input['blood_group'] ?? 'others',
            $input['zodiac_sign'] ?? null,
            $input['education'] ?? null,
            $input['occupation'] ?? null,
            $input['company_name'] ?? null,
            $input['annual_income'] ?? null,
            $input['current_city'] ?? null,
            $input['current_state'] ?? null,
            $input['mother_tongue'] ?? null,
            $input['religion'] ?? null,
            $input['caste'] ?? null,
            $input['diet'] ?? 'vegetarian',
            $input['about_me'] ?? null,
            $input['partner_preferences'] ?? null,
            $looking_for
        ]);
    }

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
    }

} catch (PDOException $e) {
    // Log the detailed error
    error_log("Profile update error: " . $e->getMessage());
    error_log("Error Code: " . $e->getCode());
    error_log("SQL State: " . $e->errorInfo[0] ?? 'Unknown');
    
    // Return more specific error message for debugging
    if (strpos($e->getMessage(), 'Unknown column') !== false) {
        echo json_encode(['success' => false, 'message' => 'Database column error: ' . $e->getMessage()]);
    } elseif (strpos($e->getMessage(), 'Data too long') !== false) {
        echo json_encode(['success' => false, 'message' => 'Data too long for column']);
    } elseif (strpos($e->getMessage(), 'Incorrect') !== false) {
        echo json_encode(['success' => false, 'message' => 'Invalid data type or value']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
