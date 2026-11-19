
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$connection = new mysqli("localhost", "root", "", "farforum");

$topic_id = intval($_GET["id"] ?? 0);

if ($topic_id <= 0) {
    header("Location: index.php");
    exit();
}

$error = "";

// hantera nya inlägg
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $content = trim($_POST["content"]);
    $user_id = $_SESSION["user_id"];

    if ($content == "") {
        $error = "Write something first!";
    } else {

        $sql = "INSERT INTO posts (topic_id, user_id, content)
                VALUES (?, ?, ?)";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iis", $topic_id, $user_id, $content);
        $stmt->execute();

        header("Location: topic.php?id=" . $topic_id);
        exit();
    }
}

// ta topic info
$sql = "SELECT topics.*, users.username
        FROM topics
        JOIN users ON topics.user_id = users.id
        WHERE topics.id = ?";

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $topic_id);
$stmt->execute();

$topic = $stmt->get_result()->fetch_assoc();

if (!$topic) {
    header("Location: index.php");
    exit();
}

// ta alla inlägg i topic
$sql = "SELECT posts.*, users.username
        FROM posts
        JOIN users ON posts.user_id = users.id
        WHERE posts.topic_id = ?
        ORDER BY posts.created_at ASC";

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $topic_id);
$stmt->execute();

$posts = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($topic["title"]); ?></title>
</head>

<body>

    <a href="index.php">Back to Forum</a>

    <h1>
        <?php echo htmlspecialchars($topic["title"]); ?>
    </h1>

    <p>
        Started by:
        <?php echo htmlspecialchars($topic["username"]); ?>
    </p>

    <hr>

    <h2>Discussion</h2>

    <?php while ($post = $posts->fetch_assoc()) { ?>

        <div class="post">

            <h3>
                <?php echo htmlspecialchars($post["username"]); ?>
            </h3>

            <p>
                <?php echo nl2br(htmlspecialchars($post["content"])); ?>
            </p>

            <small>
                <?php echo $post["created_at"]; ?>
            </small>

        </div>

        <hr>

    <?php } ?>

    <h2>Write a Reply</h2>

    <form method="POST"
          action="topic.php?id=<?php echo $topic_id; ?>">

        <textarea name="content"
                  placeholder="Write your message..."
                  required></textarea>

        <button type="submit">Post Reply</button>

        <p style="color:red;">
            <?php echo htmlspecialchars($error); ?>
        </p>

    </form>

</body>
</html>

<?php
$connection->close();
?>