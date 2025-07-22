session_start();
$conn = new mysqli("localhost", "root", "", "Register");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM register WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // ✅ THIS is the correct way to check hashed passwords
        if (password_verify($password, $user['Password'])) {
            $_SESSION['user'] = $user;

            if ($user['Role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            echo "❌ Incorrect password!";
        }
    } else {
        echo "❌ Email not found!";
    }
}
