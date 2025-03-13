<?php

require_once __DIR__ . '/../config/db.php'; // Ensure database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $amount = $_POST['amount'] ?? null;
    $category = $_POST['category'] ?? null;

    if (!$user_id || !$amount || !$category) {
        echo json_encode(["error" => "User ID, Amount, and Category are required"]);
        exit;
    }

    // Cashback percentage based on category
    $cashback_percentages = [
        'A' => 10,
        'B' => 2,
        'C' => 7
    ];

    if (!isset($cashback_percentages[$category])) {
        echo json_encode(["error" => "Invalid category"]);
        exit;
    }

    $cashback_percentage = $cashback_percentages[$category];

    $conn->begin_transaction();
    try {
        // Fetch current wallet balance
        $stmt = $conn->prepare("SELECT wallet_balance FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $wallet_balance = $row['wallet_balance'] ?? 0;

        // Calculate cashback usage
        $cashback_used = ($cashback_percentage / 100) * $amount;
        $cashback_used = min($cashback_used, $wallet_balance); // Ensure cashback does not exceed balance

        // Deduct cashback from wallet
        $new_wallet_balance = $wallet_balance - $cashback_used;
        $stmt = $conn->prepare("UPDATE users SET wallet_balance = ? WHERE id = ?");
        $stmt->bind_param("di", $new_wallet_balance, $user_id);
        $stmt->execute();

        // Insert transaction record
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, amount, cashback_used, category) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idds", $user_id, $amount, $cashback_used, $category);
        $stmt->execute();

        $conn->commit();
        echo json_encode(["message" => "Transaction successful", "cashback_used" => $cashback_used, "new_wallet_balance" => $new_wallet_balance]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["error" => "Transaction failed", "details" => $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(["transactions" => $transactions]);
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>

