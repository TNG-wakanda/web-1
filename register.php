<?php
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $cpass    = $_POST['cpassword'];
    $phone    = $_POST['phone'];
    $address  = $_POST['address'];

    if ($password !== $cpass) {
        $message = "❌ Passwords do not match!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $conn = new mysqli("localhost", "root", "", "register");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $stmt = $conn->prepare("INSERT INTO register (Name, Email, Password, Phone, Address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $hashed, $phone, $address);

        if ($stmt->execute()) {
            $message = "✅ Registration successful!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-size: 20px;
      font-weight: bolder;
      background: black;
    }

    .container {
      width: 350px;
      padding: 30px;
      margin: 100px auto;
      text-align: center;
      border: 2px solid yellow;
      border-radius: 20px;
      box-shadow: 10px 10px 0 0 yellow;
      background-color: aqua;
      font-family: Arial, sans-serif;
    }

    .form-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 20px;
    }

    .form-footer a {
      text-decoration: none;
      color: blue;
      font-size: 16px;
    }

    .form-footer button {
      font-size: 16px;
      padding: 5px 10px;
      border-radius: 15px;
      border: none;
      cursor: pointer;
      background-color: blue;
      color: white;
    }

    .message {
      margin-top: 15px;
      font-size: 16px;
      color: red;
    }

    .message a {
      color: blue;
      text-decoration: underline;
    }

    legend {
      color: white;
      text-align: center;
      font-size: 30px;
      font-weight: bolder;
      text-decoration: underline;
    }

    input {
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 16px;
    }
  </style>
</head>
<body>

  <legend>Register Form</legend>
  <div class="container">
    <header><strong>Create Account</strong></header><br>

    <?php if (!empty($message)): ?>
      <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
      <input type="text" name="name" placeholder="Username" required><br>
      <input type="email" name="email" placeholder="Email" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <input type="password" name="cpassword" placeholder="Confirm Password" required><br>
      <input type="number" name="phone" placeholder="Phone Number" required><br>
      <input type="text" name="address" placeholder="Address" required><br>

      <div class="form-footer">
        <a href="login.php">Already have an account?</a>
        <button type="submit">Register</button>
      </div>
    </form>
  </div>
</body>
</html>
