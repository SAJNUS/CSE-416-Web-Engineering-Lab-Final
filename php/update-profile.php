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

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

try {
    // Check if profile exists
    $check_stmt = $pdo->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
    $check_stmt->execute([$user_id]);
    $existing_profile = $check_stmt->fetch();

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
            updated_at = CURRENT_TIMESTAMP
            WHERE user_id = ?";

        $update_stmt = $pdo->prepare($update_sql);
        $result = $update_stmt->execute([
            $input['marital_status'] ?? null,
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
            $user_id
        ]);

    } else {
        // Insert new profile
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

        // Determine looking_for based on user's gender
        $user_stmt = $pdo->prepare("SELECT gender FROM users WHERE id = ?");
        $user_stmt->execute([$user_id]);
        $user_data = $user_stmt->fetch();
        $looking_for = ($user_data['gender'] === 'male') ? 'bride' : 'groom';

        $insert_stmt = $pdo->prepare($insert_sql);
        $result = $insert_stmt->execute([
            $user_id,
            $input['marital_status'] ?? null,
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
    error_log("Profile update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
