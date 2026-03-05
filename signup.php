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
            <h1 class="auth-title">Create your Clock‑It account</h1>
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = htmlspecialchars($_POST['email'] ?? '');
                echo "<div class=\"auth-message\">Registered: " . $email . " (placeholder)</div>";
            } ?>
            <form method="post" action="signup.php" class="auth-form">
                <label for="name">Full name</label>
                <input id="name" name="name" type="text" required autocomplete="name">

                <label for="email">Email</label>
                <input id="email" name="email" type="email" required autocomplete="email">

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