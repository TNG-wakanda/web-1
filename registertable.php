<?php

$conn = new mysqli("localhost", "root", "", "Register");

$message = "";
$result = null;

if ($conn->connect_error) {
    die("❌❎ connection failed: " . $conn->connect_error);
} else {
    $message = "✔✅ Database connected successful🥰🎉";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $phone    = trim($_POST["phone"]);
    $password = $_POST["password"];
    $confirm  = $_POST["cpassword"];

    if ($password !== $confirm) {
        $message = "❌ Passwords do not match!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO register (Name, Email, Phone, Password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $phone, $hashedPassword);

        if ($stmt->execute()) {
            $message = "✅ Registered successfully!";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$result = $conn->query("SELECT id, Name, Email, Phone FROM register ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        .message-box {
            max-width: 90%;
            margin: 20px auto;
            padding: 15px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
            font-size: 16px;
        }

        .success-box {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-box {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-button {
            text-align: center;
            margin: 20px 0;
        }

        .back-button a {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .back-button a:hover {
            background-color: #0056b3;
        }

        table {
            margin: auto;
            width: 90%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 128, 0, 0.2);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #222;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            padding: 5px 10px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 3px;
        }

        .edit {
            background-color: gold;
            color: black;
        }

        .delete {
            background-color: crimson;
            color: white;
        }
    </style>
</head>
<body>

<h2>📋 All Registered Users</h2>

<!-- 🔙 Back Button -->
<div class="back-button">
    <a href="dashboard.php">🔙 Back to Dashboard</a>
</div>

<?php if ($message): ?>
    <div class="message-box <?= strpos($message, '❌') === 0 ? 'error-box' : 'success-box' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Phone</th>
    </tr>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= (int)$row['id'] ?></td>
            <td><?= htmlspecialchars($row['Name']) ?></td>
            <td><?= htmlspecialchars($row['Email']) ?></td>
            <td><?= htmlspecialchars($row['Phone']) ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" style="text-align:center;">No users found.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>

<?php
if ($result) $result->free();
$conn->close();
?>
