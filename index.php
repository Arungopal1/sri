<?php
session_start();
$conn = new mysqli("db", "user", "pass", "auth_db");

if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Signup Logic
if (isset($_POST['signup'])) {
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    echo "<p style='color:green;'>Account Created! Login panni paaru da.</p>";
}

// Login Logic
if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $stmt = $conn->prepare("SELECT password FROM users WHERE username=?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify($_POST['password'], $row['password'])) {
        $_SESSION['user'] = $user;
    } else {
        echo "<p style='color:red;'>Thappana details da!</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>PHP Auth</title></head>
<body>
    <?php if (isset($_SESSION['user'])): ?>
        <h1>Vanakka nanba, <?php echo $_SESSION['user']; ?>!</h1>
        <a href="?logout=1">Logout</a>
        <?php if(isset($_GET['logout'])) { session_destroy(); header("Location: index.php"); } ?>
    <?php else: ?>
        <h2>Signup</h2>
        <form method="POST"><input type="text" name="username" placeholder="Username" required><input type="password" name="password" placeholder="Password" required><button name="signup">Register</button></form>
        <hr>
        <h2>Login</h2>
        <form method="POST"><input type="text" name="username" placeholder="Username" required><input type="password" name="password" placeholder="Password" required><button name="login">Login</button></form>
    <?php endif; ?>
</body>
</html>