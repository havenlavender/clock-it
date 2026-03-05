<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clock-It</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1 class="auth-title">Sign in to Clock‑It</h1>
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $user = htmlspecialchars($_POST['user'] ?? '');
                echo "<div class=\"auth-message\">Attempting sign in for: " . $user . "</div>";
            } ?>
            <form method="post" action="login.php" class="auth-form">
                <label for="user">Email or Username</label>
                <input id="user" name="user" type="text" required autocomplete="username">

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