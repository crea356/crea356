<?php
// login.php
// Enable error reporting but capture output to prevent JSON breakage
ini_set('display_errors', 0); // Hide errors from output, log them instead (or catch them)
error_reporting(E_ALL);

// Start output buffering to catch any stray text/warnings
ob_start();

header('Content-Type: application/json');

function logDebug($message) {
    file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

logDebug("Login script started");

// Check for correct request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("This is an API endpoint. Please use the <a href='logintask.html'>Login Page</a>.");
}

try {
    require_once 'config.php';
    logDebug("Config loaded");

    if (!isset($pdo) || !$pdo) {
        throw new Exception("Database connection failed (PDO not set)");
    }

    $input = file_get_contents('php://input');
    logDebug("Input received: " . $input);

    $data = json_decode($input, true);
    if ($data === null) {
        throw new Exception("Invalid or missing JSON data.");
    }

    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if (empty($email) || empty($password)) {
        ob_clean(); // Clear buffer
        echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
        exit;
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, full_name, password FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        logDebug("Login successful for user: " . $email);
        
        ob_clean(); // Clear any warnings/output
        echo json_encode(['success' => true]);
    } else {
        logDebug("Login failed for: " . $email);
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }

} catch (PDOException $e) {
    logDebug("PDO Error: " . $e->getMessage());
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Database error.']);
} catch (Exception $e) {
    logDebug("General Error: " . $e->getMessage());
    ob_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Flush buffer
ob_end_flush();
?>
