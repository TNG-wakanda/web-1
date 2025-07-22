<?php
session_start();
$message = "";

$conn = new mysqli("localhost", "root", "", "register");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM register WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['Password'])) {
            $_SESSION['user'] = $user; 

            if ($user['Role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            $message = "❌ Incorrect password!";
        }
    } else {
        $message = "❌ Email not found!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - TNG</title>
    <style>
        body {
            background: #111;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
            margin: 0;
        }

        .login-box {
            background: #fff;
            color: #000;
            padding: 30px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 0 10px rgba(255,255,255,0.3);
        }

        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: black;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #444;
        }

        .message {
            color: red;
            margin-top: 10px;
            font-weight: bold;
        }

        a {
            text-decoration: none;
            color: blue;
            display: block;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login to TNG</h2>

    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login Now</button>
    </form>

    <a href="register.php">Don't have an account? Register here</a>
</div>

</body>
</html>
