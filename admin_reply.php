<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    die("Access denied");
}

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$conn = new mysqli("localhost", "root", "", "register");
if ($conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

$errorMsg = "";
$replySent = isset($_GET['replySent']) && $_GET['replySent'] == 1;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $messageId = intval($_POST['message_id']);
    $replyText = trim($_POST['reply']);

    if (empty($replyText)) {
        $errorMsg = "Reply cannot be empty.";
    } else {
        // Get user_id from messages
        $stmt = $conn->prepare("SELECT user_id FROM messages WHERE id = ?");
        $stmt->bind_param("i", $messageId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $errorMsg = "Message not found.";
        } else {
            $row = $result->fetch_assoc();
            $userId = $row['user_id'];
            $stmt->close();

            // Get user's email from register table
            $stmt = $conn->prepare("SELECT email FROM register WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows === 0) {
                $errorMsg = "User email not found.";
            } else {
                $user = $res->fetch_assoc();
                $userEmail = $user['email'];
                $stmt->close();

                // Update reply in database
                $stmt = $conn->prepare("UPDATE messages SET reply = ?, replied_at = NOW(), is_read = 1 WHERE id = ?");
                $stmt->bind_param("si", $replyText, $messageId);

                if ($stmt->execute()) {
                    // Send email using PHPMailer
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'nshimiyimanagad24@gmail.com';          // 🔁 Change this
                        $mail->Password = 'TNG@37717';             // 🔁 Change this
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        $mail->setFrom('your_gmail@gmail.com', 'TNG Admin');  // 🔁
                        $mail->addAddress($userEmail);

                        $mail->Subject = "Reply from TNG Admin";
                        $mail->Body = "Admin replied to your message:\n\n" . $replyText;
                        $mail->isHTML(false);

                        $mail->send();
                    } catch (Exception $e) {
                        $errorMsg = "Email not sent. PHPMailer error: " . $mail->ErrorInfo;
                    }

                    if (empty($errorMsg)) {
                        header("Location: " . $_SERVER['PHP_SELF'] . "?replySent=1");
                        exit();
                    }
                } else {
                    $errorMsg = "Failed to update reply in database.";
                }
            }
        }
        $stmt->close();
    }
}

// Get all messages with user email
$sql = "
    SELECT m.id, m.message, m.reply, m.created_at, m.replied_at, m.is_read, r.email
    FROM messages m
    LEFT JOIN register r ON m.user_id = r.id
    ORDER BY m.created_at DESC
";
$result = $conn->query($sql);
if (!$result) {
    die("SQL Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Reply Panel - TNG</title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; }
    h1 { text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top; }
    th { background-color: #f0f0f0; }
    textarea { width: 100%; height: 60px; }
    input[type="submit"] { padding: 6px 12px; cursor: pointer; }
    .success { color: green; margin-bottom: 20px; }
    .error { color: red; margin-bottom: 20px; }
    .reply-text { white-space: pre-wrap; }
  </style>
</head>
<body>

<h1>Admin Reply Panel</h1>

<?php if ($replySent): ?>
  <p class="success">✅ Reply sent successfully!</p>
<?php endif; ?>

<?php if ($errorMsg): ?>
  <p class="error">⚠️ <?= htmlspecialchars($errorMsg) ?></p>
<?php endif; ?>

<p>Found <?= $result->num_rows ?> messages.</p>

<?php if ($result->num_rows > 0): ?>
  <table>
    <thead>
      <tr>
        <th>User Email</th>
        <th>Message</th>
        <th>Admin Reply</th>
        <th>Date Sent</th>
        <th>Date Replied</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($msg = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($msg['email'] ?? 'Unknown') ?></td>
        <td class="reply-text"><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
        <td class="reply-text"><?= $msg['reply'] ? nl2br(htmlspecialchars($msg['reply'])) : '<em>No reply yet</em>' ?></td>
        <td><?= $msg['created_at'] ?></td>
        <td><?= $msg['replied_at'] ?: '-' ?></td>
        <td>
          <form method="POST" action="">
            <input type="hidden" name="message_id" value="<?= $msg['id'] ?>" />
            <textarea name="reply" required placeholder="Write your reply..."><?= htmlspecialchars($msg['reply'] ?? '') ?></textarea><br />
            <input type="submit" value="Send Reply" />
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>No user messages found.</p>
<?php endif; ?>

</body>
</html>

<?php $conn->close(); ?>
