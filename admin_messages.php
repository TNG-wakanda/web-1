<?php
session_start();

// Only allow admin
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "Register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'], $_POST['message_id'])) {
    $message_id = intval($_POST['message_id']);
    $reply = $conn->real_escape_string(trim($_POST['reply']));

    // Get user_id and user's email from the message
    $res = $conn->query("SELECT user_id FROM messages WHERE id = $message_id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $user_id = $row['user_id'];

        // Get user email
        $user_res = $conn->query("SELECT Email FROM register WHERE id = $user_id");
        if ($user_res && $user_res->num_rows > 0) {
            $user_row = $user_res->fetch_assoc();
            $user_email = $user_row['Email'];

            // Update message with reply
            $update_sql = "UPDATE messages SET reply='$reply', replied_at=NOW() WHERE id = $message_id";
            if ($conn->query($update_sql)) {
                // Send email to user
                $subject = "Reply from TNG Support";
                $body = "Hello,\n\nYou have a new reply from TNG Support:\n\n$reply\n\nRegards,\nTNG Team";
                $headers = "From: support@yourdomain.com\r\nReply-To: support@yourdomain.com\r\n";

                mail($user_email, $subject, $body, $headers);
                $success = "Reply sent and saved.";
            } else {
                $error = "Failed to save reply.";
            }
        } else {
            $error = "User email not found.";
        }
    } else {
        $error = "Message not found.";
    }
}

// Fetch all messages for admin view
$messages_result = $conn->query("SELECT m.id, m.user_id, m.message, m.reply, m.created_at, m.replied_at, r.Name, r.Email FROM messages m LEFT JOIN register r ON m.user_id = r.id ORDER BY m.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Messages - Reply</title>
<style>
    body { font-family: Arial, sans-serif; background:#f0f0f0; margin:0; padding:0; }
    .container { margin-left: 220px; padding: 20px; }
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #333; color: white; }
    form { margin: 0; }
    textarea { width: 100%; height: 60px; }
    .success { color: green; }
    .error { color: red; }
</style>
</head>
<body>

<div class="container">
    <h1>Admin - User Messages and Replies</h1>

    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <table>
        <tr>
            <th>User</th>
            <th>Email</th>
            <th>Message</th>
            <th>Reply</th>
            <th>Sent At</th>
            <th>Reply At</th>
            <th>Action</th>
        </tr>
        <?php while ($msg = $messages_result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($msg['Name']) ?></td>
            <td><?= htmlspecialchars($msg['Email']) ?></td>
            <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
            <td><?= nl2br(htmlspecialchars($msg['reply'] ?? '')) ?></td>
            <td><?= $msg['created_at'] ?></td>
            <td><?= $msg['replied_at'] ?? '-' ?></td>
            <td>
                <form method="post" onsubmit="return confirm('Send reply?');">
                    <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                    <textarea name="reply" placeholder="Write reply here..." required><?= htmlspecialchars($msg['reply'] ?? '') ?></textarea><br>
                    <button type="submit">Send Reply</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
