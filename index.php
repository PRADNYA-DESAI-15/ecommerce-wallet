<?php
//require_once '../config/db.php';
require_once __DIR__ . '../src/wallet.php';
require_once __DIR__ . '../src/transaction.php';

require_once __DIR__ . '/config/db.php';  // Ensure correct path

header('Content-Type: application/json');

$request = $_GET['request'] ?? '';

if ($request === 'purchase') {
    $userId = $_POST['user_id'];
    $amount = $_POST['amount'];
    $category = $_POST['category'];

    $cashback = applyCashback($userId, $amount, $category);
    echo json_encode(["message" => "Cashback of $$cashback added"]);
}

elseif ($request === 'use-wallet') {
    $userId = $_POST['user_id'];
    $amount = $_POST['amount'];

    $result = useWalletBalance($userId, $amount);
    echo json_encode($result);
}

elseif ($request === 'wallet') {
    $userId = $_GET['user_id'];
    $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(["wallet_balance" => $wallet['wallet_balance']]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(["message" => "API is working!"]);
} else {
    echo json_encode(["error" => "Invalid request"]);
}

?>

