<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "SELECT * FROM messages ORDER BY created_at DESC";
$result = $conn->query($sql);
if (!$result) {
    die("SQL Error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Messages</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f2f2f2; }
        tr.unread { background-color: #f9f9f9; font-weight: bold; }
        /* Styled reply button */
        .reply-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            transition: background-color 0.3s ease;
        }
        .reply-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<h2>📩 Messages</h2>
<table>
    <tr>
        <th>ID</th>
        <th>User ID</th>
        <th>Message</th>
        <th>Reply</th>
        <th>Sent At</th>
        <th>Replied At</th>
        <th>Status</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr class="<?= $row['is_read'] ? '' : 'unread' ?>">
            <td><?= $row['id'] ?></td>
            <td><?= $row['user_id'] ?></td>
            <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
            <td><?= nl2br(htmlspecialchars($row['reply'])) ?></td>
            <td><?= $row['created_at'] ?></td>
            <td><?= $row['replied_at'] ?: '—' ?></td>
            <td><?= $row['is_read'] ? '✅ Read' : '📩 Unread' ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="admin_reply.php" class="reply-button">Reply</a>

</body>
</html>
