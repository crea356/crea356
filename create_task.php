<?php
// create_task.php
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
    $title = trim($data['title'] ?? '');
    $description = trim($data['description'] ?? '');
    $priority = $data['priority'] ?? 'medium';
    $category = $data['category'] ?? 'personal';
    $due_date = $data['due_date'] ?? date('Y-m-d');
    $due_time = $data['due_time'] ?? '00:00:00';
    if (strlen($due_time) === 5) {
        $due_time .= ':00';
    }
    
    $full_due_date = $due_date . ' ' . $due_time;

    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Title is required.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, priority, category, due_date) VALUES (:user_id, :title, :description, :priority, :category, :due_date)");
    $stmt->execute([
        'user_id' => $user_id,
        'title' => $title,
        'description' => $description,
        'priority' => $priority,
        'category' => $category,
        'due_date' => $full_due_date
    ]);

    echo json_encode(['success' => true, 'message' => 'Task created successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
