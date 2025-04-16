<?php
/**
 * User Functions
 * 
 * Functions to handle user operations
 */

// Initialize user session
function initUserSession() {
    if (!isset($_SESSION['user'])) {
        $_SESSION['user'] = null;
    }
}

// Check if user is logged in
function isLoggedIn() {
    initUserSession();
    return isset($_SESSION['user']) && $_SESSION['user'] !== null;
}

// Get current user
function getCurrentUser() {
    initUserSession();
    return $_SESSION['user'] ?? null;
}

    // Check if email already exists
    function registerUser($conn, $data) {
        $name = clean_input($data['name']);
        $email = clean_input($data['email']);
        $phone = clean_input($data['phone']);
        $password = clean_input($data['password']);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        // ✅ Cek apakah email sudah ada
        $checkQuery = "SELECT id FROM users WHERE email = :email";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([':email' => $email]);
        if ($checkStmt->fetch(PDO::FETCH_ASSOC)) {
            return [
                'success' => false,
                'message' => 'Email sudah terdaftar'
            ];
        }
    
        // Lanjut insert jika email belum ada
        $query = "INSERT INTO users (name, email, phone, password, created_at) 
                  VALUES (:name, :email, :phone, :password, NOW())";
    
        $stmt = $conn->prepare($query);
        $success = $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':password' => $hashed_password
        ]);
    
        if ($success) {
            $user_id = $conn->lastInsertId();
            $user = getUserById($conn, $user_id);
            $_SESSION['user'] = $user;
    
            return [
                'success' => true,
                'message' => 'Registrasi berhasil',
                'user' => $user
            ];
        }
    
        return [
            'success' => false,
            'message' => 'Registrasi gagal'
        ];
    }
    
    // Fungsi untuk membersihkan input
function clean_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
    // Get user by email
    function loginUser($conn, $email, $password) {
        $email = clean_input($email);
    
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && password_verify($password, $user['password'])) {
            return [
                'success' => true,
                'user' => $user
            ];
        }
    
        return [
            'success' => false,
            'message' => 'Email atau password salah'
        ];
    }    
    
// Logout user
function logoutUser() {
    // Unset user session
    $_SESSION['user'] = null;
    
    return [
        'success' => true,
        'message' => 'Logout berhasil'
    ];
}

// Get user by ID
function getUserById($conn, $id) {
    $id = (int)$id;

    $query = "SELECT id, name, email, phone, created_at FROM users WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute([':id' => $id]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ? $user : null;
}


// Update user profile
function updateUserProfile($conn, $id, $data) {
    $id = (int)$id;
    $name = clean_input($conn, $data['name']);
    $phone = clean_input($conn, $data['phone'] ?? '');
    
    $query = "UPDATE users SET name = '$name', phone = '$phone', updated_at = NOW() WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        // Get updated user data
        $user = getUserById($conn, $id);
        
        // Update user session
        $_SESSION['user'] = $user;
        
        return [
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'user' => $user
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Gagal memperbarui profil: ' . mysqli_error($conn)
    ];
}

// Change user password
function changeUserPassword($conn, $id, $current_password, $new_password) {
    $id = (int)$id;
    
    // Get user with password
    $query = "SELECT password FROM users WHERE id = $id";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) === 0) {
        return [
            'success' => false,
            'message' => 'User tidak ditemukan'
        ];
    }
    
    $user = mysqli_fetch_assoc($result);
    
    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        return [
            'success' => false,
            'message' => 'Password saat ini salah'
        ];
    }
    
    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update password
    $query = "UPDATE users SET password = '$hashed_password', updated_at = NOW() WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        return [
            'success' => true,
            'message' => 'Password berhasil diubah'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Gagal mengubah password: ' . mysqli_error($conn)
    ];
}
?>
