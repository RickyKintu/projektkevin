<?php
require_once "../settings.php";

$response = ['success' => false];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Sanitize and validate input
    $parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $text = isset($_POST['text']) ? trim($_POST['text']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : 'Anonymous';

    if (empty($parent_id) || empty($text) || strlen($text) > 250 || strlen($text) < 4) {
        $response['error'] = "Invalid input provided.";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    $user_id = $_SESSION['user_id'] ?? 0; //or guest

    $query = "INSERT INTO comments (parent_id, user_id, user_name, text, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($con, $query);

    mysqli_stmt_bind_param($stmt, 'iiss', $parent_id, $user_id, $name, $text);

    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
    } else {
        $response['error'] = "Failed to insert the reply: " . mysqli_error($con);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

header('Content-Type: application/json');
echo json_encode($response);
