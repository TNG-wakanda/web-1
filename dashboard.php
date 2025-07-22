<?php
session_start();
$conn = new mysqli("localhost", "root", "", "Register");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM register");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .sidebar {
            width: 160px;
            background-color: #222;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 40px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.2);
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.2s;
        }

        .sidebar a:hover {
            background-color: #444;
        }

        .main-content {
            margin-left: 160px;
            padding: 20px;
            flex: 1;
        }

        h2 {
            text-align: center;
        }

        table {
            margin: auto;
            width: 90%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 10px gray;
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

        a.edit {
            background-color: gold;
            color: black;
        }

        a.delete {
            background-color: crimson;
            color: white;
        }

        a.edit, a.delete {
            padding: 5px 10px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="webpage.html">🏠 Home</a>
    <a href="register.php">📝 Register</a>
    <a href="profile.php">👤 Profile</a>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="settings.php">⚙️ Settings</a>
</div>

<div class="main-content">
    <h2>📋 All Registered Users</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
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
            <td><?= htmlspecialchars($row['Role'] ?? 'user') ?></td>
            <td>
                <a class="edit" href="edit.php?id=<?= $row['id'] ?>"onclick="return confirm('Are you sure you want to delete this user?');">Edit</a>
                <a class="delete" href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
