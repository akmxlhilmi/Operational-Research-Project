<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$username = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Problems — OR Optimizer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@400;500&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="noise-overlay"></div>

    <header class="site-header">
        <nav class="navbar">
            <a class="brand" href="index.php">
                <span class="brand-name">Production Optimizer</span>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="optimizer.php">Optimizer</a></li>
                <li><span class="nav-user"><?php echo htmlspecialchars($username); ?></span></li>
                <li><a href="api/logout.php" class="nav-logout">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="saved-page">
        <h1 class="saved-title">Saved Problems</h1>
        <div id="saved-grid" class="saved-grid">
            <div class="empty-state">Loading...</div>
        </div>
    </main>

    <div id="toast-container" class="toast-container"></div>

    <script>
    var API_BASE = 'api';

    function esc(str) {
        var d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML;
    }

    function toast(msg, type) {
        type = type || 'success';
        var c = document.getElementById('toast-container');
        if (!c) return;
        var t = document.createElement('div');
        t.className = 'toast toast-' + type;
        t.textContent = msg;
        c.appendChild(t);
        requestAnimationFrame(function(){ t.classList.add('show'); });
        setTimeout(function(){
            t.classList.remove('show');
            setTimeout(function(){ t.remove(); }, 300);
        }, 3000);
    }

    function load() {
        fetch(API_BASE + '/get_problems.php')
            .then(function(r){ if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(function(problems){
                var grid = document.getElementById('saved-grid');
                if (!problems.length) {
                    grid.innerHTML = '<div class="empty-state">No saved problems yet. <a href="index.php">Create one now</a>.</div>';
                    return;
                }
                grid.innerHTML = problems.map(function(p){
                    var cCount = (p.constraints || []).length;
                    var rCount = parseInt(p.result_count || 0);
                    var aSale = parseFloat(p.prod_a_sale || 0), aCost = parseFloat(p.prod_a_cost || 0), aTime = parseFloat(p.prod_a_time || 0), aUnit = p.prod_a_time_unit || 'hrs';
                    var bSale = parseFloat(p.prod_b_sale || 0), bCost = parseFloat(p.prod_b_cost || 0), bTime = parseFloat(p.prod_b_time || 0), bUnit = p.prod_b_time_unit || 'hrs';
                    var budget = parseFloat(p.budget || 0), hours = parseFloat(p.work_hours || 0);
                    var period = p.budget_period || 'week';
                    return '<div class="saved-card">' +
                        '<div class="saved-card-header">' +
                        '<h2>' + esc(p.name) + '</h2>' +
                        '<span class="saved-date">' + new Date(p.created_at).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' }) + '</span>' +
                        '</div>' +
                        '<div class="saved-card-details">' +
                        '<div class="saved-detail"><span class="saved-detail-label">' + esc(p.prod_a_name || 'Product 1') + '</span><span class="saved-detail-value">Sale RM ' + aSale.toFixed(2) + ' &middot; Cost RM ' + aCost.toFixed(2) + ' &middot; ' + aTime + ' ' + aUnit + '</span></div>' +
                        '<div class="saved-detail"><span class="saved-detail-label">' + esc(p.prod_b_name || 'Product 2') + '</span><span class="saved-detail-value">Sale RM ' + bSale.toFixed(2) + ' &middot; Cost RM ' + bCost.toFixed(2) + ' &middot; ' + bTime + ' ' + bUnit + '</span></div>' +
                        '<div class="saved-detail"><span class="saved-detail-label">Budget</span><span class="saved-detail-value">RM ' + budget.toFixed(2) + ' &middot; ' + hours + ' hrs per ' + period + '</span></div>' +
                        '<div class="saved-detail"><span class="saved-detail-label">Results saved</span><span class="saved-detail-value">' + rCount + '</span></div>' +
                        '</div>' +
                        '<div class="saved-card-actions">' +
                        '<a class="btn btn-primary" href="optimizer.php?load=' + p.id + '">Load into Optimizer</a>' +
                        '<button class="btn btn-delete" data-id="' + p.id + '">Delete</button>' +
                        '</div>' +
                        '</div>';
                    }).join('');

                    grid.querySelectorAll('.btn-delete').forEach(function(btn){
                        btn.addEventListener('click', function(){
                            del(parseInt(this.dataset.id));
                        });
                    });
            })
            .catch(function(e){
                document.getElementById('saved-grid').innerHTML = '<div class="empty-state">Failed to load problems. Check your database connection.</div>';
            });
    }

    function del(id) {
        if (!confirm('Delete this problem and all its saved results?')) return;
        fetch(API_BASE + '/delete_problem.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(function(r){ if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(function(r){
            if (r.success) { toast('Problem deleted'); load(); }
            else alert(r.error);
        })
        .catch(function(e){ alert('Failed to delete: ' + e.message); });
    }

    load();
    </script>
</body>
</html>
