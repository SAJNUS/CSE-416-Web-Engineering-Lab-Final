-- Matrimonial Website Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS matrimonial_db;
USE matrimonial_db;

-- Users table for storing user information
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    date_of_birth DATE NOT NULL,
    religion VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    email_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_profile_id (profile_id),
    INDEX idx_gender (gender),
    INDEX idx_religion (religion),
    INDEX idx_status (status)
);

-- User profiles table for detailed information
CREATE TABLE IF NOT EXISTS user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    
    -- Basic Information
    marital_status ENUM('never_married', 'divorced', 'widowed', 'separated') DEFAULT 'never_married',
    have_children ENUM('no_children', 'yes_living_together', 'yes_not_living_together') DEFAULT 'no_children',
    
    -- Physical Appearance
    height VARCHAR(20),
    weight VARCHAR(20),
    body_type ENUM('slim', 'average', 'athletic', 'heavy', 'very_fair') DEFAULT 'average',
    complexion ENUM('very_fair', 'fair', 'wheatish', 'dark', 'very_dark') DEFAULT 'fair',
    hair_color ENUM('black', 'brown', 'blonde', 'gray', 'bald', 'others') DEFAULT 'black',
    eye_color ENUM('black', 'brown', 'blue', 'green', 'gray', 'hazel', 'others') DEFAULT 'black',
    disabilities ENUM('none', 'physical', 'hearing', 'visual', 'speech', 'others') DEFAULT 'none',
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'others') DEFAULT 'others',
    
    -- Astrological Information
    zodiac_sign ENUM('aries', 'taurus', 'gemini', 'cancer', 'leo', 'virgo', 'libra', 'scorpio', 'sagittarius', 'capricorn', 'aquarius', 'pisces'),
    birth_time TIME,
    birth_place VARCHAR(100),
    
    -- Professional Information
    education VARCHAR(100),
    education_details TEXT,
    occupation VARCHAR(100),
    company_name VARCHAR(100),
    annual_income VARCHAR(50),
    
    -- Location Information
    current_city VARCHAR(50),
    current_state VARCHAR(50),
    current_country VARCHAR(50) DEFAULT 'India',
    hometown VARCHAR(50),
    
    -- Cultural Information
    mother_tongue VARCHAR(50),
    languages_known TEXT,
    religion VARCHAR(50),
    caste VARCHAR(50),
    sub_caste VARCHAR(50),
    gotra VARCHAR(50),
    
    -- Lifestyle Information
    diet ENUM('vegetarian', 'non_vegetarian', 'eggetarian', 'vegan') DEFAULT 'vegetarian',
    drinking ENUM('never', 'occasionally', 'socially', 'regularly') DEFAULT 'never',
    smoking ENUM('never', 'occasionally', 'socially', 'regularly') DEFAULT 'never',
    
    -- Family Information
    family_type ENUM('nuclear', 'joint') DEFAULT 'nuclear',
    family_status ENUM('lower_middle_class', 'middle_class', 'upper_middle_class', 'rich', 'affluent') DEFAULT 'middle_class',
    father_occupation VARCHAR(100),
    mother_occupation VARCHAR(100),
    siblings_count INT DEFAULT 0,
    
    -- About and Preferences
    about_me TEXT,
    hobbies TEXT,
    interests TEXT,
    partner_preferences TEXT,
    
    -- Profile Settings
    profile_managed_by ENUM('self', 'parents', 'siblings', 'relatives', 'friends') DEFAULT 'self',
    looking_for ENUM('bride', 'groom') NOT NULL,
    profile_visibility ENUM('all', 'premium_members', 'paid_members') DEFAULT 'all',
    
    -- Media
    photo_url VARCHAR(255),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_marital_status (marital_status),
    INDEX idx_current_city (current_city),
    INDEX idx_education (education)
);

-- User photos table
CREATE TABLE IF NOT EXISTS user_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    photo_url VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT FALSE,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_primary (is_primary),
    INDEX idx_approved (is_approved)
);

-- Interests/matches table
CREATE TABLE IF NOT EXISTS interests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    status ENUM('sent', 'accepted', 'declined') DEFAULT 'sent',
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_interest (sender_id, receiver_id),
    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id),
    INDEX idx_status (status)
);

-- Messages table for communication
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id),
    INDEX idx_conversation (sender_id, receiver_id),
    INDEX idx_read (is_read),
    INDEX idx_created (created_at)
);

-- Visits/views table
CREATE TABLE IF NOT EXISTS profile_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id INT NOT NULL,
    visited_id INT NOT NULL,
    visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (visitor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (visited_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_visitor (visitor_id),
    INDEX idx_visited (visited_id),
    INDEX idx_visit_date (visited_at)
);

-- Favorites/shortlist table
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    favorite_user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (favorite_user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, favorite_user_id),
    INDEX idx_user (user_id),
    INDEX idx_favorite (favorite_user_id)
);

-- Blocked users table
CREATE TABLE IF NOT EXISTS blocked_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blocker_id INT NOT NULL,
    blocked_id INT NOT NULL,
    reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (blocker_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (blocked_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_block (blocker_id, blocked_id),
    INDEX idx_blocker (blocker_id),
    INDEX idx_blocked (blocked_id)
);

-- Email verification tokens
CREATE TABLE IF NOT EXISTS email_verification_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_token (token)
);

-- Password reset tokens
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_token (token)
);

-- Insert some sample data for testing
INSERT INTO users (profile_id, first_name, last_name, email, phone, gender, date_of_birth, religion, password) VALUES
('MAT123456', 'John', 'Doe', 'john.doe@example.com', '+1234567890', 'male', '1990-05-15', 'christian', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('MAT123457', 'Jane', 'Smith', 'jane.smith@example.com', '+1234567891', 'female', '1992-08-20', 'hindu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('MAT123458', 'Mike', 'Johnson', 'mike.johnson@example.com', '+1234567892', 'male', '1988-12-10', 'muslim', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample profile data
INSERT INTO user_profiles (user_id, height, weight, marital_status, education, occupation, annual_income, current_city, current_state, mother_tongue, about_me, looking_for) VALUES
(1, '5\'10"', '70kg', 'never_married', 'Bachelor\'s in Engineering', 'Software Engineer', '50000-75000', 'New York', 'NY', 'English', 'I am a software engineer who loves to travel and explore new places.', 'bride'),
(2, '5\'6"', '55kg', 'never_married', 'Master\'s in Business', 'Business Analyst', '40000-60000', 'Los Angeles', 'CA', 'English', 'I enjoy reading books and spending time with family and friends.', 'groom'),
(3, '6\'0"', '75kg', 'never_married', 'Bachelor\'s in Commerce', 'Marketing Manager', '60000-80000', 'Chicago', 'IL', 'English', 'I am passionate about sports and fitness.', 'bride');
