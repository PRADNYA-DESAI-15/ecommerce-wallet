<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../config/db.php'; // Ensure correct path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $amount = $_POST['amount'] ?? null;

    if (!$user_id || !$amount) {
        echo json_encode(["error" => "User ID and Amount are required"]);
        exit;
    }

    $conn->begin_transaction();
    try {
        // Fetch current wallet balance
        $stmt = $conn->prepare("SELECT wallet_balance FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $wallet_balance = $row['wallet_balance'] ?? 0;

        // Update wallet balance
        $new_wallet_balance = $wallet_balance + $amount;
        $stmt = $conn->prepare("UPDATE users SET wallet_balance = ? WHERE id = ?");
        $stmt->bind_param("di", $new_wallet_balance, $user_id);
        $stmt->execute();

        $conn->commit();
        echo json_encode(["message" => "Wallet updated successfully", "new_wallet_balance" => $new_wallet_balance]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["error" => "Wallet update failed", "details" => $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
