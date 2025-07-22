<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    die("⛔ You must be logged in.");
}

$user_id = $_SESSION['user']['id'];

// Connect to 'orders' database
$conn = new mysqli("localhost", "root", "", "orders");
if ($conn->connect_error) {
    die("❌ Connection to orders DB failed: " . $conn->connect_error);
}

// Check if user exists in users table (in 'orders' DB)
$user_check = $conn->prepare("SELECT id, fullnames, email, password, role, created_at FROM users WHERE id = ?");
if (!$user_check) {
    die("❌ Prepare failed: " . $conn->error);
}
$user_check->bind_param("i", $user_id);
$user_check->execute();
$user_result = $user_check->get_result();

if ($user_result->num_rows === 0) {
    die("❌ User not found in 'orders' database.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = intval($_POST['quantity'] ?? 1);
    if ($quantity <= 0) {
        die("❗ Invalid quantity.");
    }

    // Step 1: Insert into orders table
    $status = 'pending';
    $order_stmt = $conn->prepare("INSERT INTO orders (user_id, order_date, status) VALUES (?, NOW(), ?)");
    if (!$order_stmt) {
        die("❌ Failed to prepare order query: " . $conn->error);
    }
    $order_stmt->bind_param("is", $user_id, $status);

    if (!$order_stmt->execute()) {
        die("❌ Failed to insert order: " . $order_stmt->error);
    }

    $order_id = $conn->insert_id;

    // Step 2: Insert into order_items table
    $product_name = "Fast Charging Power Bank";
    $price = 15000; // If your DB expects decimal(10,2), use 15000.00

    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
    if (!$item_stmt) {
        die("❌ Prepare failed for order_items: " . $conn->error);
    }
    $item_stmt->bind_param("isid", $order_id, $product_name, $quantity, $price);

    if ($item_stmt->execute()) {
        $success = "✅ Your order has been placed successfully!";
    } else {
        die("❌ Error inserting order item: " . $item_stmt->error);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Place Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 30px;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 6px;
            max-width: 400px;
            margin: auto;
        }
        input, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
        }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>

<form method="POST">
    <h2>Order Fast Charging Power Bank</h2>
    <label>Quantity:</label>
    <input type="number" name="quantity" min="1" required />
    <button type="submit">Place Order</button>
</form>

</body>
</html>
