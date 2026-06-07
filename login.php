<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    header('Location: optimizer.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — OR Production Optimizer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@400;500&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="page-auth">
    <div class="noise-overlay"></div>

    <header class="site-header">
        <nav class="navbar">
            <a class="brand" href="index.php">
                <span class="brand-name">Production Optimizer</span>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <main class="auth-main">
        <div class="auth-card">
            <h1 class="auth-title">Sign In</h1>
            <p class="auth-subtitle">Enter your credentials to access the optimizer</p>
            <form id="login-form" class="auth-form" novalidate>
                <div class="auth-field">
                    <label class="auth-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="auth-input" placeholder="Enter username" required autofocus>
                </div>
                <div class="auth-field">
                    <label class="auth-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="auth-input" placeholder="Enter password (min 6 characters)" required minlength="6">
                    <span class="auth-error" id="password-error"></span>
                </div>
                <div class="auth-error" id="form-error"></div>
                <button type="submit" class="auth-btn">Sign In</button>
            </form>
            <p class="auth-footer-text">
                Don't have an account? <a href="register.php">Create one</a>
            </p>
        </div>
    </main>

    <div id="toast-container" class="toast-container"></div>

    <script>
    var form = document.getElementById('login-form');
    var formError = document.getElementById('form-error');
    var passwordError = document.getElementById('password-error');
    var passwordInput = document.getElementById('password');
    var submitBtn = form.querySelector('.auth-btn');

    passwordInput.addEventListener('input', function() {
        if (passwordInput.value.length > 0 && passwordInput.value.length < 6) {
            passwordError.textContent = 'Password must be at least 6 characters';
        } else {
            passwordError.textContent = '';
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        formError.textContent = '';
        passwordError.textContent = '';

        var username = document.getElementById('username').value.trim();
        var password = passwordInput.value;

        if (!username) {
            formError.textContent = 'Username is required';
            return;
        }

        if (password.length < 6) {
            passwordError.textContent = 'Password must be at least 6 characters';
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Signing in...';

        fetch('api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: username, password: password })
        })
        .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
        .then(function(result) {
            if (result.ok && result.data.success) {
                window.location.href = 'optimizer.php';
            } else {
                formError.textContent = result.data.error || 'Login failed';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Sign In';
            }
        })
        .catch(function() {
            formError.textContent = 'Connection failed. Please try again.';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Sign In';
        });
    });
    </script>
</body>
</html>
