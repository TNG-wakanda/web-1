<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$message = "";

$conn = new mysqli("localhost", "root", "", "Register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
       $photoPath = $user['Photo'] ?? '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) mkdir($targetDir);
            $photoPath = $targetDir . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
        }
 $stmt = $conn->prepare("UPDATE register SET Name=?, Email=?, Phone=?, Photo=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $photoPath, $user['id']);
        if ($stmt->execute()) {
          $_SESSION['user']['Photo'] = $photoPath;
            $message = "✅ Profile updated successfully!";
        } else {
            $message = "❌ Failed to update profile.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard - TNG</title>
  <style>
    body {
      font-family: 'Courier New', Courier, monospace;
      background-color: whitesmoke;
      color: black;
      margin: 0;
      padding: 0;
    }
    header {
      background-color: white;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    header h1 {
      display: flex;
      align-items: center;
      margin: 0;
    }
    header img {
      width: 50px;
      height: 30px;
      margin-right: 10px;
      border-radius: 15px;
    }
    nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      margin: 0;
      padding: 0;
    }
    nav a {
      text-decoration: none;
      color: black;
      font-weight: bold;
    }
    nav a:hover {
      color: blue;
    }
    .container {
      padding: 40px;
      max-width: 1000px;
      margin: auto;
    }
    .user-info {
      background-color: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    .user-info img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 20px;
    }
    .user-info h2 {
      margin-bottom: 10px;
    }
    .user-info p {
      margin: 5px 0;
    }
    .actions {
      margin-top: 20px;
    }
    .actions a {
      text-decoration: none;
      background-color: #3D63F1;
      color: white;
      padding: 10px 20px;
      border-radius: 30px;
      margin: 10px;
      display: inline-block;
    }
    .logout {
      margin-top: 30px;
    }
    .logout a {
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <header>
    <h1><img src="img/logo2.jpg" alt="Logo">TNG</h1>
    <nav>
      <ul>
        <li><a href="webpage.php">Home</a></li>
        <li><a href="settings.php">Settings</a></li>
        <li><a href="chat.php">Chat✨Support</a></li>
        <li><a href="settings.php"></a></li>
      </ul>
    </nav>
  </header>

  <div class="container">
    
    <div class="user-info">
       <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="user-photo">
        <img src="<?= isset($_SESSION['user']['Photo']) ? $_SESSION['user']['Photo'] : 'user_default.png' ?>" alt="Profile Picture">
    </div>
      <h2><?= htmlspecialchars($_SESSION['user']['Name']) ?></h2>
      <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['Email']) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($_SESSION['user']['Phone']) ?></p>
      <p><strong>Address:</strong> <?= htmlspecialchars($_SESSION['user']['Address']) ?></p>

      <div class="actions">
        <a href="settings.php">Update Profile</a>
        <a href="user_messages.php">My Messages</a>
        <a href="orders.php">My Orders</a>
      </div>

      <div class="logout">
        <a href="logout.php">🚪 Logout</a>
      </div>
    </div>
  </div>
</body>
</html>