<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = (int)$_SESSION['user']['id'];
$user_email = $_SESSION['user']['email'];

$conn = new mysqli("localhost", "root", "", "Register");
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['message']) || trim($data['message']) === '') {
    echo json_encode(['error' => 'Message is empty']);
    exit();
}

$message = trim($data['message']);

// Store user message
$stmt = $conn->prepare("INSERT INTO messages (user_id, message, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("is", $user_id, $message);
$stmt->execute();
$last_id = $stmt->insert_id;
$stmt->close();

// PHPMailer part
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';               // Gmail SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'nshimiyimanagad24@gmail.com';         // YOUR Gmail address here
    $mail->Password   = 'TNG@37717';            // YOUR Gmail App Password here
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom('no-reply@tng.com', 'TNG Chat System');
    $mail->addAddress('nshimiyimanagad24@gmail.com', 'TNG Admin');  // Admin email
    $mail->addReplyTo($user_email);

    // Content
    $mail->isHTML(false);
    $mail->Subject = "New message from TNG user (ID: $user_id)";
    $mail->Body    = "You received a new message:\n\nFrom: $user_email (User ID: $user_id)\n\nMessage:\n$message";

    $mail->send();
} catch (Exception $e) {
    error_log("Mailer Error: " . $mail->ErrorInfo);
    // optionally handle error gracefully or ignore so user is not affected
}

// Auto-reply from admin (bot)
$auto_reply = "Thank you for your message. We will reply shortly.";
$stmt = $conn->prepare("UPDATE messages SET reply = ?, replied_at = NOW() WHERE id = ?");
$stmt->bind_param("si", $auto_reply, $last_id);
$stmt->execute();
$stmt->close();

$conn->close();

echo json_encode(['reply' => $auto_reply]);
