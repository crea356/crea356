<?php
// update_task_status.php
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
    $task_id = $data['task_id'] ?? null;
    $status = $data['status'] ?? null;

    if (!$task_id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        exit;
    }

    $completed_at = ($status === 'completed') ? date('Y-m-d H:i:s') : null;

    $stmt = $pdo->prepare("UPDATE tasks SET status = :status, completed_at = :completed_at WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        'status' => $status,
        'completed_at' => $completed_at,
        'id' => $task_id,
        'user_id' => $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
