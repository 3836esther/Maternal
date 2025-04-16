<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=Please log in to view your reminders");
    exit();
}

$userId = $_SESSION['user_id'];
$reminders = [];
$filterKeyword = $_GET['search'] ?? '';
$filterDate = $_GET['date'] ?? '';
$preferences = ['medication' => true, 'appointment' => true, 'general' => true]; // default all true

try {
    $conn = new PDO("mysql:host=localhost;dbname=maternal", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Load user preferences if exist
    $prefStmt = $conn->prepare("SELECT pref_type FROM user_preferences WHERE user_id = ?");
    $prefStmt->execute([$userId]);
    $prefs = $prefStmt->fetchAll(PDO::FETCH_COLUMN);
    if ($prefs) {
        // Set all to false, then update based on database
        $preferences = ['medication' => false, 'appointment' => false, 'general' => false];
        foreach ($prefs as $pref) {
            if (isset($preferences[$pref])) {
                $preferences[$pref] = true;
            }
        }
    }

    // Delete old reminders
    $conn->prepare("DELETE FROM reminders WHERE created_at < NOW() - INTERVAL 30 DAY")->execute();

    // Count unread
    $unreadStmt = $conn->prepare("SELECT COUNT(*) FROM reminders WHERE user_id = ? AND status = 'unread'");
    $unreadStmt->execute([$userId]);
    $unreadCount = $unreadStmt->fetchColumn();

    // Build reminders query with filters
    $query = "SELECT * FROM reminders WHERE user_id = :user_id";
    if (!empty($filterKeyword)) {
        $query .= " AND message LIKE :keyword";
    }
    if (!empty($filterDate)) {
        $query .= " AND DATE(created_at) = :filterDate";
    }

    // Apply preferences filtering
    $prefTypes = array_keys(array_filter($preferences));
    if (count($prefTypes) > 0) {
        $inQuery = implode(',', array_fill(0, count($prefTypes), '?'));
        $query .= " AND type IN ($inQuery)";
    }

    $query .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':user_id', $userId);
    $i = 1;
    if (!empty($filterKeyword)) $stmt->bindValue(':keyword', '%' . $filterKeyword . '%');
    if (!empty($filterDate)) $stmt->bindValue(':filterDate', $filterDate);
    foreach ($prefTypes as $type) {
        $stmt->bindValue($i++, $type);
    }
    $stmt->execute();
    $reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark all as read
    $conn->prepare("UPDATE reminders SET status = 'read' WHERE user_id = ?")->execute([$userId]);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Notifications</title>
    <style>
        /* same styles as before */
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bell {
            font-size: 24px;
            position: relative;
        }

        .count {
            position: absolute;
            top: -8px;
            right: -10px;
            background: red;
            color: white;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 50%;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
        }

        .controls {
            margin: 20px 0;
            text-align: center;
        }

        input[type="text"],
        input[type="date"] {
            padding: 8px;
            margin: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .reminder {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            position: relative;
            border-left: 5px solid #007bff;
        }

        .timestamp {
            font-size: 12px;
            color: #666;
            text-align: right;
        }

        .delete-btn {
            float: right;
            background: #dc3545;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }

        .delete-btn:hover {
            background: #c82333;
        }

        .none {
            text-align: center;
            font-style: italic;
            color: #999;
        }

        .preferences {
            margin-top: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .preferences h4 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="header">
    <h3>🔔 Notifications</h3>
    <div class="bell">
        <?php if ($unreadCount > 0): ?>
            <span class="count"><?= $unreadCount ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <h2>Your Medication & Health Reminders</h2>

    <div class="controls">
        <form method="GET" style="margin-bottom: 10px;">
            <input type="text" name="search" placeholder="Search by keyword..." value="<?= htmlspecialchars($filterKeyword) ?>">
            <input type="date" name="date" value="<?= htmlspecialchars($filterDate) ?>">
            <button type="submit">Filter</button>
        </form>
    </div>

    <?php if (count($reminders) > 0): ?>
        <?php foreach ($reminders as $r): ?>
            <?php
                $type = $r['type'] ?? 'general';
                $emoji = '🔔'; $bgColor = '#fff3cd';
                if ($type === 'medication') { $emoji = '💊'; $bgColor = '#d1e7dd'; }
                elseif ($type === 'appointment') { $emoji = '📅'; $bgColor = '#cff4fc'; }
            ?>
            <div class="reminder" style="background: <?= $bgColor ?>;">
                <form method="POST" action="delete_reminder.php" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <button type="submit" class="delete-btn">Delete</button>
                </form>
                <p><strong><?= $emoji ?> [<?= ucfirst($type) ?>]</strong> <?= htmlspecialchars($r['message']) ?></p>
                <div class="timestamp"><?= date("F j, Y, g:i a", strtotime($r['created_at'])) ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="none">You have no reminders at the moment.</p>
    <?php endif; ?>

    <div class="preferences">
        <h4>Notification Preferences</h4>
        <form method="post" action="update_preferences.php">
            <label><input type="checkbox" name="pref_types[]" value="medication" <?= $preferences['medication'] ? 'checked' : '' ?>> Medication reminders</label><br>
            <label><input type="checkbox" name="pref_types[]" value="appointment" <?= $preferences['appointment'] ? 'checked' : '' ?>> Appointment updates</label><br>
            <label><input type="checkbox" name="pref_types[]" value="general" <?= $preferences['general'] ? 'checked' : '' ?>> General alerts</label><br>
            <button type="submit" style="margin-top: 10px;">Save Preferences</button>
        </form>
    </div>
</div>

</body>
</html>
