<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$total_users = $conn->query("SELECT COUNT(*) AS total FROM register")->fetch_assoc()['total'];
$admins = $conn->query("SELECT COUNT(*) AS total FROM register WHERE Role = 'admin'")->fetch_assoc()['total'];
$messages = $conn->query("SELECT COUNT(*) AS total FROM messages")->fetch_assoc()['total'];

// Calculate regular users
$regular_users = $total_users - $admins;

// Auto chat summary
if ($messages > 0) {
    $chat_summary = "💬 There are <strong>$messages messages</strong> shared among <strong>$total_users users</strong>.";
} else {
    $chat_summary = "😶 No messages yet. Encourage users to start a conversation!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>📊 Admin Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 40px;
        }
        .report-card {
            background: white;
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        h2 { text-align: center; color: #333; }
        ul { list-style: none; padding: 0; }
        li {
            background: #f9f9f9;
            padding: 12px;
            margin-bottom: 10px;
            border-left: 5px solid #007bff;
            font-size: 16px;
        }
        .summary {
            background: #e9f7ef;
            border-left: 5px solid #28a745;
            padding: 15px;
            margin-top: 25px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="report-card">
        <h2>📊 System Report Overview</h2>
        <ul>
            <li>Total Registered Users: <strong><?= $total_users ?></strong></li>
            <li>Total Admins: <strong><?= $admins ?></strong></li>
            <li>Total Regular Users: <strong><?= $regular_users ?></strong></li>
            <li>Total Messages: <strong><?= $messages ?></strong></li>
        </ul>
        <div class="summary">
            <?= $chat_summary ?>
        </div>
    </div>
</body>
</html>
