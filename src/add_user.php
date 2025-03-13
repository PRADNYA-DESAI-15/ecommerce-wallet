<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;

    if (!$name || !$email) {
        echo json_encode(["error" => "Name and Email are required"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);

    if ($stmt->execute()) {
        echo json_encode(["message" => "User added successfully", "user_id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to add user"]);
    }
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
