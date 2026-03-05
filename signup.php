<?php
// Simple signup handling that stores users in users.json
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($password !== $password2) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please provide a valid email address.';
    } elseif (!preg_match('/^[A-Za-z0-9_\-]{3,30}$/', $username)) {
        $error = 'Username must be 3-30 characters and contain only letters, numbers, _ or -.';
    } else {
        $usersFile = __DIR__ . '/users.json';
        $users = [];
        if (file_exists($usersFile)) {
            $json = file_get_contents($usersFile);
            $users = json_decode($json, true) ?: [];
        }

        foreach ($users as $u) {
            if (strtolower($u['email']) === strtolower($email)) {
                $error = 'An account with that email already exists.';
                break;
            }
            if (!empty($u['username']) && strtolower($u['username']) === strtolower($username)) {
                $error = 'That username is already taken.';
                break;
            }
            if (strtolower($u['name']) === strtolower($username)) {
                $error = 'That username is already taken.';
                break;
            }
            // also avoid username colliding with existing emails
            if (strtolower($u['email']) === strtolower($username)) {
                $error = 'That username is already taken.';
                break;
            }
        }

        if (!$error) {
            $users[] = [
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'created' => time(),
            ];
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
            header('Location: login.php?registered=1');
            exit;
        }
    }
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
            <h1 class="auth-title">Create your Clock‑It account</h1>
            <?php if ($error): ?>
                <div class="auth-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" action="signup.php" class="auth-form">
                <label for="name">Full name</label>
                <input id="name" name="name" type="text" required autocomplete="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">

                <label for="username">Username</label>
                <input id="username" name="username" type="text" required autocomplete="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">

                <label for="email">Email</label>
                <input id="email" name="email" type="email" required autocomplete="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

                <label for="password">Password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" minlength="6">

                <label for="password2">Confirm password</label>
                <input id="password2" name="password2" type="password" required autocomplete="new-password" minlength="6">

                <button type="submit" class="btn">Create account</button>
            </form>

            <div class="auth-footer">Already have an account? <a href="login.php">Sign in</a></div>
        </div>
    </div>
</body>
</html>