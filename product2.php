<?php
session_start();

// Database connection (only one now: orders)
$conn = new mysqli("localhost", "root", "", "orders");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Step 1: Check if user is logged in
if (!isset($_SESSION['user'])) {
    die("⛔ You must be logged in to place an order.");
}

$user_id = $_SESSION['user']['id'];

// Step 2: Check if user exists in orders.users table
$stmt = $conn->prepare("SELECT id, fullnames, email FROM users WHERE id = ?");
if (!$stmt) {
    die("❌ Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Prompt user to enter details
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['fullnames']) && isset($_POST['email'])) {
        $fullnames = trim($_POST['fullnames']);
        $email = trim($_POST['email']);
        $password = password_hash("defaultpassword", PASSWORD_DEFAULT); // Default password
        $role = "user";

        $insert_user = $conn->prepare("INSERT INTO users (id, fullnames, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if (!$insert_user) {
            die("❌ Insert prepare failed: " . $conn->error);
        }
        $insert_user->bind_param("issss", $user_id, $fullnames, $email, $password, $role);
        if ($insert_user->execute()) {
            echo "✅ User profile created. You can now place your order.";
        } else {
            die("❌ Failed to save user: " . $insert_user->error);
        }
    } else {
        // Show form to enter fullnames and email
        ?>
        <h3>Complete Your Profile</h3>
        <form method="POST">
            Full Names: <input type="text" name="fullnames" required><br><br>
            Email: <input type="email" name="email" required><br><br>
            <button type="submit">Save and Continue</button>
        </form>
        <?php
        exit();
    }
}

// Step 3: Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'])) {
    $quantity = intval($_POST['quantity']);
    if ($quantity <= 0) {
        die("❗ Invalid quantity.");
    }

    // Insert order
    $status = 'pending';
    $order_stmt = $conn->prepare("INSERT INTO orders (user_id, order_date, status) VALUES (?, NOW(), ?)");
    if (!$order_stmt) {
        die("❌ Failed to prepare orders query: " . $conn->error);
    }
    $order_stmt->bind_param("is", $user_id, $status);

    if (!$order_stmt->execute()) {
        die("❌ Failed to save order: " . $order_stmt->error);
    }

    $order_id = $conn->insert_id;

    // Insert order item (example product)
    $product_name = "Fast Charging Power Bank";
    $price = 15000;

    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
    if (!$item_stmt) {
        die("❌ Failed to prepare order_items query: " . $conn->error);
    }
    $item_stmt->bind_param("isid", $order_id, $product_name, $quantity, $price);

    if ($item_stmt->execute()) {
        $success = "✅ Your order has been placed!";
    } else {
        $error = "❌ Failed to save order item.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Place Order</title>
</head>
<body>
    <?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
        Quantity: <input type="number" name="quantity" min="1" required />
        <button type="submit">Place Order</button>
    </form>
</body>
</html>
