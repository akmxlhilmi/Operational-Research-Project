<?php
// Database Setup — visit: http://localhost/or-project/setup.php
// Runs api/setup.sql and displays results

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'mysql'; // connect to system DB first

$conn = @new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('<p style="color:#e05252;font-family:monospace;">Connection failed: ' . htmlspecialchars($conn->connect_error) . '</p>');
}

$sqlFile = __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'setup.sql';
if (!file_exists($sqlFile)) {
    die('<p style="color:#e05252;font-family:monospace;">Cannot find api/setup.sql</p>');
}

$sql = file_get_contents($sqlFile);
$conn->multi_query($sql);

$results = [];
$conn->next_result();
while ($conn->more_results()) {
    try { $conn->next_result(); } catch (Exception $e) {}
}

$error = $conn->error;
$conn->close();

if ($error) {
    $showError = htmlspecialchars($error);
} else {
    $showError = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup — OR Optimizer</title>
    <style>
        body { background:#0d0f12; color:#e8eaed; font-family:'DM Sans',system-ui,sans-serif; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .card { background:#1a1e24; border:1px solid rgba(255,255,255,0.1); border-radius:10px; padding:40px; text-align:center; max-width:480px; }
        h1 { font-family:'DM Serif Display',Georgia,serif; font-size:1.8rem; margin:0 0 12px; }
        .success { color:#4ecb71; }
        .error { color:#e05252; font-size:0.85rem; word-break:break-all; }
        .links { margin-top:24px; display:flex; gap:12px; justify-content:center; }
        .links a { color:#d4a853; text-decoration:none; font-weight:600; font-size:0.9rem; }
        .links a:hover { text-decoration:underline; }
        p { color:#9aa3b0; font-size:0.95rem; margin:8px 0; }
    </style>
</head>
<body>
    <div class="card">
        <?php if ($showError): ?>
            <h1 style="color:#e05252;">Setup Failed</h1>
            <p class="error"><?= $showError ?></p>
            <p>Try running from command line:</p>
            <p style="font-family:monospace;color:#606878;">mysql -u root &lt; api/setup.sql</p>
        <?php else: ?>
            <h1 class="success">Database Ready</h1>
            <p>Tables created and example data seeded.</p>
        <?php endif; ?>
        <div class="links">
            <a href="index.php">Homepage</a>
            <a href="optimizer.php">Open Optimizer</a>
            <a href="saved.php">Saved Problems</a>
        </div>
    </div>
</body>
</html>
