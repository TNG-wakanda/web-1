<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = (int)$_SESSION['user']['id']; 

$conn = new mysqli("localhost", "root", "", "Register");
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$sql = "SELECT message, reply, created_at, replied_at FROM messages WHERE user_id = $user_id ORDER BY created_at ASC";
$result = $conn->query($sql);

$chat = [];

while ($row = $result->fetch_assoc()) {
    $chat[] = [
        'sender' => 'user',
        'text' => $row['message'],
        'timestamp' => $row['created_at']
    ];

    if (!empty($row['reply'])) {
        $chat[] = [
            'sender' => 'bot',
            'text' => $row['reply'],
            'timestamp' => $row['replied_at'] ?? null
        ];
    }
}

echo json_encode($chat);

$conn->close();
