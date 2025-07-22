<?php
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 

    $conn = new mysqli("localhost", "root", "", "register");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM register WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: registertable.php");
        exit();
    } else {
        echo "❌ Error deleting user: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "❌ No user ID provided for deletion.";
}
?>
