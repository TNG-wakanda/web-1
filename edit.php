<?php
session_start();


if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "Register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);


$user = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM register WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if (!$user) {
        die("❌ User not found.");
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id    = $_POST['id'];
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE register SET Name=?, Email=?, Phone=? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $phone, $id);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
</head>
<body>
    <h2>Edit User</h2>
    <?php if ($user): ?>
    <form method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($user['Name']) ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required><br><br>

        <label>Phone:</label><br>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['Phone']) ?>" required><br><br>

        <button type="submit">Update</button>
    </form>
    <?php else: ?>
        <p>⚠️ No user data to edit.</p>
    <?php endif; ?>

    <p><a href="dashboard.php">🔙 Back to Dashboard</a></p>
</body>
</html>
