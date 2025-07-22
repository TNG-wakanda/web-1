<?php
session_start();

// Redirect if not logged in or not admin
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "Register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch users for management
$result = $conn->query("SELECT * FROM register");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            width: 200px;
            height: 100vh;
            background: #333;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 50px;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #555;
        }
        .main {
            margin-left: 200px;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #222;
            color: white;
        }
        a.btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .edit {
            background-color: orange;
            color: white;
        }
        .delete {
            background-color: red;
            color: white;
        }
        .logout {
            background-color: #444;
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2 style="text-align:center;">Admin</h2>
    <a href="admin_dashboard.php">📋 User Management</a>
    <a href="create_user.php">➕ Add User</a>
    <a href="site_settings.php">⚙️ Site Settings</a>
    <a href="messages.php">📩 View Messages</a>
    <a href="reports.php">📈 Reports</a>
    <a href="logout.php" class="logout">🚪 Logout</a>
</div>

<div class="main">
    <h1>Admin Dashboard - User Management</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['Name']) ?></td>
            <td><?= htmlspecialchars($row['Email']) ?></td>
            <td><?= htmlspecialchars($row['Phone']) ?></td>
            <td><?= htmlspecialchars($row['Role']) ?></td>
            <td>
                <a class="btn edit" href="edit.php?id=<?= $row['id'] ?>">Edit</a>
                <a class="btn delete" href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this user?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
