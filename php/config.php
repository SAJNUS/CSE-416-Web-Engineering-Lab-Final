<?php
// Database configuration
class Database {
    private $host = 'localhost';
    private $dbname = 'matrimonial_db';
    private $username = 'root';
    private $password = '';
    private $pdo;
    
    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
}

// Global PDO connection for simple access
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
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    throw new Exception("Database connection failed");
}

// User class for handling user operations
class User {
    private $db;
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
    }
    
    public function register($userData) {
        try {
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$userData['email']]);
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email already registered'];
            }
            
            // Hash password
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            // Generate profile ID
            $profileId = $this->generateProfileId();
            
            // Insert user
            $stmt = $this->db->prepare("
                INSERT INTO users (
                    profile_id, first_name, last_name, email, phone, 
                    gender, date_of_birth, religion, password, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $profileId,
                $userData['firstName'],
                $userData['lastName'],
                $userData['email'],
                $userData['phone'],
                $userData['gender'],
                $userData['dateOfBirth'],
                $userData['religion'],
                $hashedPassword
            ]);
            
            return [
                'success' => true, 
                'message' => 'Registration successful',
                'profile_id' => $profileId
            ];
            
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
    
    public function login($emailOrProfileId, $password) {
        try {
            // Check by email or profile ID
            $stmt = $this->db->prepare("
                SELECT id, profile_id, first_name, last_name, email, password, status 
                FROM users 
                WHERE email = ? OR profile_id = ?
            ");
            $stmt->execute([$emailOrProfileId, $emailOrProfileId]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            if ($user['status'] !== 'active') {
                return ['success' => false, 'message' => 'Account is not active'];
            }
            
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Invalid password'];
            }
            
            // Update last login
            $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            // Start session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['profile_id'] = $user['profile_id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['email'] = $user['email'];
            
            return [
                'success' => true, 
                'message' => 'Login successful',
                'redirect' => 'dashboard.html'
            ];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed'];
        }
    }
    
    private function generateProfileId() {
        $prefix = 'MAT';
        $number = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        return $prefix . $number;
    }
}

// Utility functions
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    return preg_match('/^[\+]?[1-9][\d]{7,15}$/', $phone);
}

function validateAge($dateOfBirth) {
    $birthDate = new DateTime($dateOfBirth);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    return $age >= 18;
}
?>
