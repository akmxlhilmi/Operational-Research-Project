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
    <title>Register — OR Production Optimizer</title>
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
                <li><a href="login.php">Sign In</a></li>
            </ul>
        </nav>
    </header>

    <main class="auth-main">
        <div class="auth-card">
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Sign up to save and manage your optimization problems</p>
            <form id="register-form" class="auth-form" novalidate>
                <div class="auth-field">
                    <label class="auth-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="auth-input" placeholder="Choose a username (min 3 characters)" required minlength="3" autofocus>
                    <span class="auth-error" id="username-error"></span>
                </div>
                <div class="auth-field">
                    <label class="auth-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="auth-input" placeholder="Enter password (min 6 characters)" required minlength="6">
                    <span class="auth-error" id="password-error"></span>
                </div>
                <div class="auth-field">
                    <label class="auth-label" for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" class="auth-input" placeholder="Re-enter password" required>
                    <span class="auth-error" id="confirm-error"></span>
                </div>
                <div class="auth-error" id="form-error"></div>
                <button type="submit" class="auth-btn">Create Account</button>
            </form>
            <p class="auth-footer-text">
                Already have an account? <a href="login.php">Sign in</a>
            </p>
        </div>
    </main>

    <div id="toast-container" class="toast-container"></div>

    <script>
    var form = document.getElementById('register-form');
    var formError = document.getElementById('form-error');
    var usernameError = document.getElementById('username-error');
    var passwordError = document.getElementById('password-error');
    var confirmError = document.getElementById('confirm-error');
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
        usernameError.textContent = '';
        passwordError.textContent = '';
        confirmError.textContent = '';

        var username = document.getElementById('username').value.trim();
        var password = passwordInput.value;
        var confirm = document.getElementById('confirm-password').value;

        if (!username || username.length < 3) {
            usernameError.textContent = 'Username must be at least 3 characters';
            return;
        }

        if (password.length < 6) {
            passwordError.textContent = 'Password must be at least 6 characters';
            return;
        }

        if (password !== confirm) {
            confirmError.textContent = 'Passwords do not match';
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating account...';

        fetch('api/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: username, password: password })
        })
        .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
        .then(function(result) {
            if (result.ok && result.data.success) {
                window.location.href = 'login.php?registered=1';
            } else {
                formError.textContent = result.data.error || 'Registration failed';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create Account';
            }
        })
        .catch(function() {
            formError.textContent = 'Connection failed. Please try again.';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Create Account';
        });
    });
    </script>
</body>
</html>
