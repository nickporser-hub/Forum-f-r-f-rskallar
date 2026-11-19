<!-- http://localhost/Forum/Templates/login.php -->

<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $connection = new mysqli("localhost", "root", "", "farforum");

    if ($connection->connect_error) {
        die("Database connection failed!");
    }

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $connection->prepare($sql);

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            $_SESSION["username"] = $user["username"];
            $_SESSION["user_id"] = $user["id"];

            header("Location: index.php");
            exit();

        } else {
            $error = "Wrong password!";
        }

    } else {
        $error = "User does not exist!";
    }

    $stmt->close();
    $connection->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Farforum - Login</title>
</head>

<body>

    <h1>Welcome to Farforum!</h1>

    <form method="POST" action="login.php">

        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>

        <p style="color:red;">
            <?php echo htmlspecialchars($error); ?>
        </p>

    </form>

    <p>Don't have an account?</p>

    <a href="register.php">Create an account</a>

</body>
</html>