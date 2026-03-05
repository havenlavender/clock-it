<?php session_start(); $user = $_SESSION['user'] ?? null; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Clock-It</title>
</head>
<body>
    <nav class="header">
        <a href="index.php">dashboard</a>
        <a href="alarm.php">alarm</a>
        <a href="calendar.php">calendar</a>
        <a href="stopwatch.php">stopwatch</a>
        <?php if ($user): ?>
            <span class="muted" style="margin-left:12px">Hello, <?php echo htmlspecialchars($user['name']); ?></span>
            <a href="logout.php" style="margin-left:8px">logout</a>
        <?php else: ?>
            <a href="login.php" style="margin-left:12px">sign in</a>
        <?php endif; ?>
    </nav>
</body>
</html>