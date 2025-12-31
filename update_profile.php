<?php
// update_profile.php
header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid or missing JSON data in request.']);
        exit;
    }
    $user_id = $_SESSION['user_id'];
    
    $full_name = $data['full_name'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $bio = $data['bio'] ?? '';

    if (empty($full_name) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Name and Email are required.']);
        exit;
    }

    // Check if email is already taken by another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
    $stmt->execute(['email' => $email, 'id' => $user_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email is already in use by another account.']);
        exit;
    }

    // Update user profile
    $query = "UPDATE users SET full_name = :name, email = :email, phone = :phone, bio = :bio";
    $params = [
        'name' => $full_name,
        'email' => $email,
        'phone' => $phone,
        'bio' => $bio,
        'id' => $user_id
    ];

    $password = $data['password'] ?? '';
    if (!empty($password)) {
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
            exit;
        }
        $query .= ", password = :password";
        $params['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $query .= " WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
