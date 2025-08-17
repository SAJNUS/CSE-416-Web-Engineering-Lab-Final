<?php
require_once 'config.php';

try {
    echo "Creating user_profiles table...\n";
    
    $sql = "CREATE TABLE IF NOT EXISTS user_profiles (
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
    )";
    
    $pdo->exec($sql);
    echo "✓ user_profiles table created successfully\n";
    
    // Also create user_photos table if it doesn't exist
    $sql2 = "CREATE TABLE IF NOT EXISTS user_photos (
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
    )";
    
    $pdo->exec($sql2);
    echo "✓ user_photos table created successfully\n";
    
    echo "Database tables created successfully!\n";
    
} catch (PDOException $e) {
    echo "✗ Error creating tables: " . $e->getMessage() . "\n";
}
?>
