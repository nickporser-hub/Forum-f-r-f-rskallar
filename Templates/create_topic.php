
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$connection = new mysqli("localhost", "root", "", "farforum");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $user_id = $_SESSION["user_id"];

    if ($title == "" || $content == "") {
        $error = "Please fill in all fields!";
    } else {

        // skapa topic
        $sql = "INSERT INTO topics (title, user_id)
                VALUES (?, ?)";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param("si", $title, $user_id);
        $stmt->execute();

        // ta nya topic id
        $topic_id = $connection->insert_id;

        // skapa första inlägget
        $sql = "INSERT INTO posts (topic_id, user_id, content)
                VALUES (?, ?, ?)";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iis", $topic_id, $user_id, $content);
        $stmt->execute();

        header("Location: topic.php?id=" . $topic_id);
        exit();
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Topic - Farforum</title>
</head>

<body>

    <h1>Create a New Topic</h1>

    <form method="POST" action="create_topic.php">

        <label>Topic title:</label>
        <input type="text" name="title" maxlength="150" required>

        <label>Your message:</label>
        <textarea name="content" required></textarea>

        <button type="submit">Create Topic</button>

        <p style="color:red;">
            <?php echo htmlspecialchars($error); ?>
        </p>

    </form>

    <a href="index.php">Back to Forum</a>

</body>
</html>
