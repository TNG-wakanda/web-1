<?php
session_start();
$conn = new mysqli("localhost", "root", "", "Register");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE register SET Name=?, Email=?, Phone=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $email, $phone, $user_id);

    if ($stmt->execute()) {
        $message = "✅ Profile updated successfully.";
        $_SESSION['user']['Name'] = $name;
        $_SESSION['user']['Email'] = $email;
        $_SESSION['user']['Phone'] = $phone;
    } else {
        $message = "❌ Failed to update profile.";
    }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM register WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <style>
        body {
            background: #f0f0f0;
            font-family: Arial;
            padding: 20px;
        }
        .profile-box {
            background: white;
            max-width: 500px;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px gray;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background: black;
            color: white;
            border: none;
            border-radius: 6px;
        }
        .message {
            margin-top: 15px;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="profile-box">
    <h2>👤 My Profile</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['Name']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['Phone']) ?>" required>

        <button type="submit">Update Profile</button>
    </form>
</div>

</body>
</html>
