<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION['user']['Email'];
$conn = new mysqli("localhost", "root", "", "register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_message'])) {
    $newMsg = trim($_POST['new_message']);
    if ($newMsg !== "") {
        $stmt = $conn->prepare("INSERT INTO messages (email, message, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $userEmail, $newMsg);
        if ($stmt->execute()) {
            $message = "✅ Message sent!";
        } else {
            $message = "❌ Failed to send message.";
        }
        $stmt->close();
    } else {
        $message = "⚠️ Message cannot be empty.";
    }
}

// Fetch user's messages
$stmt = $conn->prepare("SELECT message, reply, created_at, replied_at FROM messages WHERE email = ? ORDER BY created_at ASC");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Messages - TNG</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        h2 { text-align: center; }
        .chat-box { max-height: 400px; overflow-y: auto; margin-top: 20px; border: 1px solid #ddd; padding: 10px; border-radius: 5px; background: #fdfdfd; }
        .msg { margin: 10px 0; padding: 10px; border-radius: 10px; }
        .user-msg { background-color: #dcf8c6; text-align: right; }
        .admin-msg { background-color: #eee; text-align: left; }
        .form-group { margin-top: 20px; }
        input[type=text] { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
        input[type=submit] { padding: 10px 20px; margin-top: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        input[type=submit]:hover { background-color: #0056b3; }
        .message-box { margin-bottom: 10px; }
        .timestamp { font-size: 0.8em; color: gray; }
        .flash { color: green; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2>📨 My Messages</h2>

    <?php if ($message): ?>
        <p class="flash"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <div class="chat-box">
        <?php foreach ($messages as $msg): ?>
            <div class="msg user-msg">
                <div><strong>You:</strong> <?= htmlspecialchars($msg['message']) ?></div>
                <div class="timestamp"><?= $msg['created_at'] ?></div>
            </div>
            <?php if (!empty($msg['reply'])): ?>
                <div class="msg admin-msg">
                    <div><strong>Admin:</strong> <?= htmlspecialchars($msg['reply']) ?></div>
                    <div class="timestamp"><?= $msg['replied_at'] ?: 'Pending' ?></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <form method="POST" class="form-group">
        <input type="text" name="new_message" placeholder="Type your message here..." required>
        <input type="submit" value="Send Message">
    </form>
</div>

</body>
</html>
