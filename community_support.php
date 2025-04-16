<?php
session_start(); // Start the session

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['email']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get logged in user's ID
$user_id = $_SESSION['user_id'];
$message = "";

// Database connection
try {
    $conn = new PDO("mysql:host=localhost;dbname=maternal", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle group creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_group'])) {
    $group_name = trim($_POST['group_name']);
    $description = trim($_POST['description']);

    if (!empty($group_name) && !empty($description)) {
        try {
            $stmt = $conn->prepare("INSERT INTO forum_groups (group_name, description, created_by) VALUES (?, ?, ?)");
            $stmt->execute([$group_name, $description, $user_id]);
            $message = "Group created successfully!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Handle user joining a group
if (isset($_GET['join_group_id'])) {
    $group_id = $_GET['join_group_id'];
    try {
        $stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
        $stmt->execute([$group_id, $user_id]);
        $message = "You have successfully joined the group!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle posting a message in a group
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_message'])) {
    $post_text = trim($_POST['post_text']);
    $group_id = $_POST['group_id'];

    if (!empty($post_text) && !empty($group_id)) {
        try {
            $stmt = $conn->prepare("INSERT INTO posts (user_id, group_id, post_text) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $group_id, $post_text]);
            $message = "Message posted successfully!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Handle liking a post
if (isset($_POST['like_post'])) {
    $post_id = $_POST['post_id'];
    try {
        // Check if the user has already liked the post
        $stmt = $conn->prepare("SELECT * FROM post_likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);
        $existing_like = $stmt->fetch();

        if (!$existing_like) {
            // Add like
            $stmt = $conn->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)");
            $stmt->execute([$post_id, $user_id]);
            $message = "You liked the post!";
        } else {
            $message = "You have already liked this post.";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle posting a comment on a post
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'])) {
    $comment_text = trim($_POST['comment_text']);
    $post_id = $_POST['post_id'];

    if (!empty($comment_text)) {
        try {
            $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
            $stmt->execute([$post_id, $user_id, $comment_text]);
            $message = "Comment posted successfully!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch all forum groups
try {
    $stmt = $conn->prepare("SELECT * FROM forum_groups");
    $stmt->execute();
    $groups = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Error: " . $e->getMessage();
}

// Fetch posts for a specific group if the group_id is provided
$posts = [];
if (isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'];

    try {
        $stmt = $conn->prepare("SELECT posts.*, users.full_name 
                                FROM posts 
                                JOIN users ON posts.user_id = users.id 
                                WHERE posts.group_id = ? 
                                ORDER BY posts.created_at DESC");
        $stmt->execute([$group_id]);
        $posts = $stmt->fetchAll();
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Community Forum | Maternal Health</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav>
    <a href="home.php">home</a>
    <a href="Services.php">Services</a>
    <a href="logout.php" class="logout-button">Logout</a>
    <a href="notification.php">🔔</a>
</nav>

<div class="container">
    <h2>Community Support Forum</h2>
    <p>Welcome, <?= htmlspecialchars($_SESSION['email']) ?>!</p>

    <?php if (!empty($message)): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['group_id'])): ?>
        <h3>Discussion for Group: <?= htmlspecialchars($groups[array_search($_GET['group_id'], array_column($groups, 'id'))]['group_name']) ?></h3>
        <form method="POST">
            <textarea name="post_text" placeholder="Write your message..." required></textarea>
            <input type="hidden" name="group_id" value="<?= $_GET['group_id'] ?>">
            <button type="submit" name="post_message">Post Message</button>
        </form>

        <h4>Posts:</h4>
        <div class="posts">
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <strong><?= htmlspecialchars($post['full_name']) ?></strong>
                    <p><?= nl2br(htmlspecialchars($post['post_text'])) ?></p>
                    <small>Posted on: <?= $post['created_at'] ?></small>
                    <form method="POST" style="display:inline;">
                        <button type="submit" name="like_post">Like</button>
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    </form>
                    <small>Likes: <?= $conn->query("SELECT COUNT(*) FROM post_likes WHERE post_id = {$post['id']}")->fetchColumn() ?></small>

                    <h5>Comments:</h5>
                    <form method="POST">
                        <textarea name="comment_text" placeholder="Write a comment..." required></textarea>
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <button type="submit" name="comment">Post Comment</button>
                    </form>

                    <div class="comments">
                        <?php
                        $comment_stmt = $conn->prepare("SELECT comments.*, users.full_name FROM comments 
                                                        JOIN users ON comments.user_id = users.id 
                                                        WHERE comments.post_id = ? ORDER BY comments.created_at DESC");
                        $comment_stmt->execute([$post['id']]);
                        $comments = $comment_stmt->fetchAll();
                        ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                                <strong><?= htmlspecialchars($comment['full_name']) ?></strong>
                                <p><?= nl2br(htmlspecialchars($comment['comment_text'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <p><a href="community_support.php">← Back to all groups</a></p>
    <?php else: ?>
        <h3>Available Forum Groups</h3>
        <?php foreach ($groups as $group): ?>
            <div class="forum-group">
                <h4><?= htmlspecialchars($group['group_name']) ?></h4>
                <p><?= htmlspecialchars($group['description']) ?></p>
                <a href="?join_group_id=<?= $group['id'] ?>">Join this group</a> | 
                <a href="?group_id=<?= $group['id'] ?>">View Discussion</a>
            </div>
        <?php endforeach; ?>

        <h3>Create a New Group</h3>
        <form method="POST">
            <input type="text" name="group_name" placeholder="Group Name" required>
            <textarea name="description" placeholder="Group Description" required></textarea>
            <button type="submit" name="create_group">Create Group</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
