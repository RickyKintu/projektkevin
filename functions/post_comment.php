<?php
require_once "../settings.php"; // Ensure this path is correctly set

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content_type = mysqli_real_escape_string($con, $_POST['content_type']);
    $content_id = intval($_POST['content_id']);
    $user_name = isset($_POST['name']) ? mysqli_real_escape_string($con, $_POST['name']) : 'Anonymous';
    $text = mysqli_real_escape_string($con, $_POST['text']);

    // Validate comment length
    if (strlen($text) < 4 || strlen($text) > 250) {
        $response['error'] = 'Comment must be between 4 and 250 characters.';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    $user_id = 1;

    $query = "INSERT INTO comments (content_type, content_id, user_id, user_name, text) VALUES ('$content_type', $content_id, $user_id, '$user_name', '$text')";

    if (mysqli_query($con, $query)) {
        $response['success'] = true;
    } else {
        $response['error'] = mysqli_error($con);
    }
}

header('Content-Type: application/json');
echo json_encode($response);
