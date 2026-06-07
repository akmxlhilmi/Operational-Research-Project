<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OR Production Optimizer — Linear Programming for Operations</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@400;500&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="page-home">
    <div class="noise-overlay"></div>

    <header class="site-header">
        <nav class="navbar">
            <a class="brand" href="index.php">
                <span class="brand-mark">OR</span>
                <span class="brand-name">Production Optimizer</span>
            </a>
            <ul class="nav-links">
                <li><a href="optimizer.php">Optimizer</a></li>
                <li><a href="saved.php">Saved Problems</a></li>
            </ul>
        </nav>
    </header>

    <main class="home-main">
        <div class="home-hero">
            <div class="home-content">
                <div class="home-eyebrow">
                    <span class="eyebrow-dot"></span>
                    PRODUCTION PLANNING TOOL
                </div>
                <h1 class="home-title">
                    Find the most profitable<br>production mix in seconds.
                </h1>
                <p class="home-text">
                    A practical linear programming tool for operations teams.
                    Define products, set resource constraints and instantly
                    calculate optimal quantities to maximize profit.
                </p>
                <div class="home-actions">
                    <a class="btn-primary-lg" href="optimizer.php">Launch Optimizer &rarr;</a>
                    <a class="btn-ghost-lg" href="saved.php">View Saved Problems</a>
                </div>
            </div>

            <div class="home-visual" aria-hidden="true">
                <svg viewBox="0 0 280 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="60,160 60,60 140,140 190,60 220,100 220,160" fill="rgba(212,168,83,0.1)" stroke="rgba(212,168,83,0.5)" stroke-width="1.5" stroke-linejoin="round"/>
                    <line x1="60" y1="160" x2="220" y2="160" stroke="rgba(255,255,255,0.15)" stroke-width="1.5"/>
                    <line x1="60" y1="160" x2="60" y2="20" stroke="rgba(255,255,255,0.15)" stroke-width="1.5"/>
                    <line x1="0" y1="120" x2="240" y2="180" stroke="#8b6ef5" stroke-width="1.5" stroke-dasharray="5,4" opacity="0.5"/>
                    <circle cx="140" cy="140" r="5" fill="#d4a853" stroke="#0d0f12" stroke-width="2"/>
                    <circle cx="60" cy="60" r="3" fill="none" stroke="rgba(212,168,83,0.4)" stroke-width="1.5"/>
                    <circle cx="190" cy="60" r="3" fill="none" stroke="rgba(212,168,83,0.4)" stroke-width="1.5"/>
                    <circle cx="220" cy="100" r="3" fill="none" stroke="rgba(212,168,83,0.4)" stroke-width="1.5"/>
                    <circle cx="60" cy="160" r="3" fill="none" stroke="rgba(212,168,83,0.4)" stroke-width="1.5"/>
                    <circle cx="220" cy="160" r="3" fill="none" stroke="rgba(212,168,83,0.4)" stroke-width="1.5"/>
                    <text x="15" y="175" fill="#606878" font-size="9" font-family="DM Mono, monospace">0</text>
                    <text x="50" y="175" fill="#606878" font-size="9" font-family="DM Mono, monospace">10</text>
                    <text x="105" y="175" fill="#606878" font-size="9" font-family="DM Mono, monospace">20</text>
                    <text x="160" y="175" fill="#606878" font-size="9" font-family="DM Mono, monospace">30</text>
                    <text x="220" y="175" fill="#606878" font-size="9" font-family="DM Mono, monospace">40</text>
                </svg>
                <div class="home-stat">
                    <span class="home-stat-val">Max Z</span>
                    <span class="home-stat-lbl">Profit optimization</span>
                </div>
                <div class="home-stat">
                    <span class="home-stat-val">2D</span>
                    <span class="home-stat-lbl">Graphical visualization</span>
                </div>
                <div class="home-stat">
                    <span class="home-stat-val">SQL</span>
                    <span class="home-stat-lbl">Persistent storage</span>
                </div>
            </div>
        </div>
    </main>

    <footer class="home-footer">
        <span>&copy; 2026 OR Production Optimizer</span>
    </footer>
</body>
</html>
