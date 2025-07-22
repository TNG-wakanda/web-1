<?php
session_start();

if (!isset($_SESSION['user'])) {
    die("⛔ You must be logged in.");
}

$user_id = $_SESSION['user']['id'];

$conn = new mysqli("localhost", "root", "", "orders");
if ($conn->connect_error) {
    die("❌ Connection to orders DB failed: " . $conn->connect_error);
}

$user_check = $conn->prepare("SELECT id FROM users WHERE id = ?");
if (!$user_check) {
    die("❌ Prepare failed: " . $conn->error);
}
$user_check->bind_param("i", $user_id);
$user_check->execute();
$user_result = $user_check->get_result();

if ($user_result->num_rows === 0) {
    die("❌ User not found in 'orders' database.");
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 1);
    $price = floatval($_POST['price'] ?? 0);

    // Handle file upload
    $product_image = null;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $tmpName = $_FILES['product_image']['tmp_name'];
        $filename = basename($_FILES['product_image']['name']);
        $targetFilePath = $uploadDir . time() . "_" . $filename; // unique name

        $fileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileType, $allowedTypes)) {
            $error = "❗ Only JPG, JPEG, PNG & GIF files are allowed.";
        } elseif (!move_uploaded_file($tmpName, $targetFilePath)) {
            $error = "❗ Failed to upload image.";
        } else {
            $product_image = $targetFilePath;
        }
    }

    if (!$error) {
        if ($product_name === '') {
            $error = "❗ Product name is required.";
        } elseif ($quantity <= 0) {
            $error = "❗ Quantity must be at least 1.";
        } elseif ($price <= 0) {
            $error = "❗ Price must be greater than 0.";
        }
    }

    if (!$error) {
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

        $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price, product_image) VALUES (?, ?, ?, ?, ?)");
        if (!$item_stmt) {
            die("❌ Prepare failed for order_items: " . $conn->error);
        }
        $item_stmt->bind_param("isids", $order_id, $product_name, $quantity, $price, $product_image);

        if ($item_stmt->execute()) {
            $success = "✅ Your order has been placed successfully!";
        } else {
            $error = "❌ Error inserting order item: " . $item_stmt->error;
        }

        $item_stmt->close();
        $order_stmt->close();
    }
}

$user_check->close();
$conn->close();
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
        label {
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php 
if ($success) echo "<p class='success'>$success</p>"; 
if ($error) echo "<p class='error'>$error</p>"; 
?>

<form method="POST" action="" enctype="multipart/form-data">
    <h2>Place Your Order</h2>
    
    <label for="product_name">Product Name:</label>
    <input type="text" name="product_name" id="product_name" required value="<?= htmlspecialchars($_POST['product_name'] ?? '') ?>" />

    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity" min="1" required value="<?= htmlspecialchars($_POST['quantity'] ?? '1') ?>" />

    <label for="price">Price (in RWF):</label>
    <input type="number" name="price" id="price" step="0.01" min="0" required value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" />

    <label for="product_image">Product Image:</label>
    <input type="file" name="product_image" id="product_image" accept=".jpg,.jpeg,.png,.gif" />

    <button type="submit">Place Order</button>
</form>

</body>
</html>
