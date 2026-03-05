<?php
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userInput = trim($_POST['user'] ?? '');
    $password = $_POST['password'] ?? '';

    $usersFile = __DIR__ . '/users.json';
    $users = [];
    if (file_exists($usersFile)) {
        $json = file_get_contents($usersFile);
        $users = json_decode($json, true) ?: [];
    }

    $found = null;
    foreach ($users as $u) {
        if (strtolower($u['email']) === strtolower($userInput)) {
            $found = $u;
            break;
        }
    }

    if ($found && password_verify($password, $found['password'])) {
        $_SESSION['user'] = ['name' => $found['name'], 'email' => $found['email']];
        header('Location: index.php');
        exit;
    }

    $error = 'Invalid credentials. Please try again.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clock-It</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <h1 class="auth-title">Sign in to Clock‑It</h1>
            <?php if ($error): ?>
                <div class="auth-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="post" action="login.php" class="auth-form">
                <label for="user">Email</label>
                <input id="user" name="user" type="email" required autocomplete="username">

                <label for="password">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password">

                <div class="auth-row">
                    <label class="checkbox"><input name="remember" type="checkbox"> Remember me</label>
                    <a class="auth-link" href="#">Forgot?</a>
                </div>

                <button type="submit" class="btn">Sign in</button>
            </form>

            <div class="auth-footer">Don't have an account? <a href="signup.php">Sign up</a></div>
        </div>
    </div>
</body>
</html>