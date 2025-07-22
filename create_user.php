<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name       = trim($_POST['name']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];
    $confirm    = $_POST['confirm_password'];
    $phone      = trim($_POST['phone']);
    $role       = $_POST['role'];

    if ($name && $email && $password && $confirm && $phone && $role) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "❌ Invalid email format.";
        } elseif ($password !== $confirm) {
            $message = "❌ Passwords do not match.";
        } else {
            $conn = new mysqli("localhost", "root", "", "register");
            if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

            $check = $conn->prepare("SELECT id FROM register WHERE Email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $message = "❌ Email already registered.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO register (Name, Email, Password, Phone, Role) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $name, $email, $hashed, $phone, $role);

                if ($stmt->execute()) {
                    $message = "✅ User created successfully!";
                } else {
                    $message = "❌ Error: " . $conn->error;
                }
                $stmt->close();
            }

            $check->close();
            $conn->close();
        }
    } else {
        $message = "❌ All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create User - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: black;
            margin: 0;
            padding: 30px;
        }
        .container {
            max-width: 500px;
            background: white;
            padding: 30px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center;
            background: black;
            color: white; 
        }
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        form input, form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .success { background-color: #d4edda; color: #155724; }
        .error   { background-color: #f8d7da; color: #721c24; }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>➕ Create New User</h2>

    <?php if ($message): ?>
        <div class="message <?= strpos($message, '✅') === 0 ? 'success' : 'error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" required>

        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" required>

        <label for="phone">Phone Number</label>
        <input type="text" name="phone" id="phone" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <label for="role">Role</label>
        <select name="role" id="role" required>
            <option value="">--Select Role--</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>

        <button type="submit">Create User</button>
    </form>
</div>
</body>
</html>
