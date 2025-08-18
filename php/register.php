<?php
session_start(); // Start session at the beginning
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get and sanitize input data
    $userData = [
        'firstName' => sanitizeInput($_POST['firstName'] ?? ''),
        'lastName' => sanitizeInput($_POST['lastName'] ?? ''),
        'email' => sanitizeInput($_POST['email'] ?? ''),
        'phone' => sanitizeInput($_POST['phone'] ?? ''),
        'gender' => sanitizeInput($_POST['gender'] ?? ''),
        'dateOfBirth' => sanitizeInput($_POST['dateOfBirth'] ?? ''),
        'religion' => sanitizeInput($_POST['religion'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirmPassword' => $_POST['confirmPassword'] ?? ''
    ];
    
    // Log registration attempt for debugging
    error_log("Registration attempt for email: " . $userData['email']);
    
    // Validation
    $errors = [];
    
    if (empty($userData['firstName']) || empty($userData['lastName'])) {
        $errors[] = 'First name and last name are required';
    }
    
    if (!validateEmail($userData['email'])) {
        $errors[] = 'Valid email address is required';
    }
    
    if (!validatePhone($userData['phone'])) {
        $errors[] = 'Valid phone number is required';
    }
    
    if (!in_array($userData['gender'], ['male', 'female'])) {
        $errors[] = 'Valid gender selection is required';
    }
    
    if (empty($userData['dateOfBirth']) || !validateAge($userData['dateOfBirth'])) {
        $errors[] = 'You must be at least 18 years old';
    }
    
    if (empty($userData['religion'])) {
        $errors[] = 'Religion selection is required';
    }
    
    if (strlen($userData['password']) < 6) {
        $errors[] = 'Password must be at least 6 characters long';
    }
    
    if ($userData['password'] !== $userData['confirmPassword']) {
        $errors[] = 'Passwords do not match';
    }
    
    if (!isset($_POST['terms'])) {
        $errors[] = 'You must accept the terms and conditions';
    }
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
        exit;
    }
    
    // Create user instance and attempt registration
    $user = new User();
    $result = $user->register($userData);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log("Registration script error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred during registration']);
}
?>
