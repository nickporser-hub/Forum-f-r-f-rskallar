
<?php
$connection = new mysqli("localhost", "root", "", "farforum");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (strlen($username) < 3) {
        $error = "Username must be at least 3 characters!";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters!";
    } else {

        // kolla om användarnamnet redan finns
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username already exists!";
        } else {

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // skapa ny användare
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("ss", $username, $hashedPassword);

            if ($stmt->execute()) {
                $success = "Account created successfully!";
            } else {
                $error = "Something went wrong!";
            }
        }

        $stmt->close();
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farforum - Register</title>
</head>

<body>

    <h1>Create an account</h1>

    <form method="POST" action="register.php">

        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Sign Up</button>

        <p style="color:red;">
            <?php echo htmlspecialchars($error); ?>
        </p>

        <p style="color:green;">
            <?php echo htmlspecialchars($success); ?>
        </p>

    </form>

    <p>Already have an account?</p>
    <a href="login.php">Login here</a>

</body>
</html>
