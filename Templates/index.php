
<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$connection = new mysqli("localhost", "root", "", "farforum");

$sql = "SELECT topics.*, users.username
        FROM topics
        JOIN users ON topics.user_id = users.id
        ORDER BY topics.created_at DESC";

$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farforum</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <h1>Welcome to Farforum!</h1>

    <p>
        Logged in as:
        <?php echo htmlspecialchars($_SESSION["username"]); ?>
    </p>

    <a href="logout.php">Logout</a>

    <hr>

    <h2>Discussion Topics</h2>

    <a href="create_topic.php">+ Create New Topic</a>

    <hr>

    <?php while ($topic = $result->fetch_assoc()) { ?>

        <div class="topic">

            <h3>
                <a href="topic.php?id=<?php echo $topic["id"]; ?>">
                    <?php echo htmlspecialchars($topic["title"]); ?>
                </a>
            </h3>

            <p>
                Created by:
                <?php echo htmlspecialchars($topic["username"]); ?>
            </p>

            <p>
                <?php echo $topic["created_at"]; ?>
            </p>

        </div>

        <hr>

    <?php } ?>

</body>
</html>

<?php
$connection->close();
?>
