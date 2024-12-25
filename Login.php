<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
</head>
<?php
session_start();

// Database connection
$host = "localhost"; // Hostname
$db_user = "root";   // Database username
$db_password = "";   // Database password
$db_name = "login_system"; // Database name

// Connect to the database
$conn = new mysqli($host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    
    // Query to verify the user
    $sql = "SELECT * FROM users WHERE username='$username' AND password=MD5('$password')";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
    } else {
        $error = "Invalid username or password!";
    }
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .container { max-width: 300px; margin: auto; }
        input, button { width: 100%; padding: 10px; margin: 10px 0; }
        button { background-color: #007BFF; color: white; border: none; }
        .error { color: red; }
        .logout { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['username'])): ?>
            <!-- Dashboard Section -->
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <a href="?logout=true" class="logout">Logout</a>
        <?php else: ?>
            <!-- Login Form -->
            <h2>Login</h2>
            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>