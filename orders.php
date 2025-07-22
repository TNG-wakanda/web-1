<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "vubavuba");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user']['id'];
$sql = "SELECT o.id, o.order_date, o.status, SUM(oi.quantity * oi.price) AS total
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id, o.order_date, o.status
        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }
        th {
            background-color: #3D63F1;
            color: white;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        a.back {
            display: block;
            width: 150px;
            margin: 30px auto;
            padding: 10px;
            text-align: center;
            background: #3D63F1;
            color: white;
            text-decoration: none;
            border-radius: 25px;
        }
    </style>
</head>
<body>
    <h1>My Orders</h1>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Date</th>
            <th>Status</th>
            <th>Total</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['order_date'] ?></td>
            <td><?= ucfirst($row['status']) ?></td>
            <td>$<?= number_format($row['total'], 2) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a class="back" href="user_dashboard.php">⬅ Back to Dashboard</a>
</body>
</html>
