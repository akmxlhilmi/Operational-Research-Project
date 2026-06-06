<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optimizer — OR Production Optimizer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@400;500&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="page-wizard">
    <div class="noise-overlay"></div>

    <header class="site-header">
        <nav class="navbar">
            <a class="brand" href="index.php">
                <span class="brand-mark">OR</span>
                <span class="brand-name">Production Optimizer</span>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="saved.php">Saved Problems</a></li>
            </ul>
        </nav>
    </header>

    <main class="optimizer-wizard">
        <h1 class="wiz-title">Production Optimizer</h1>

        <!-- Step 1: Problem Details -->
        <section class="wiz-section">
            <h2 class="wiz-heading">Problem Details</h2>
            <div class="wiz-row">
                <div class="wiz-field wiz-field-wide">
                    <label class="wiz-label">Problem Name</label>
                    <input type="text" id="wiz-name" class="wiz-input" value="Untitled Problem" placeholder="e.g. Furniture Workshop">
                </div>
            </div>
            <div class="wiz-row wiz-row-3">
                <div class="wiz-field">
                    <label class="wiz-label">Manufacturing Budget ($)</label>
                    <input type="number" id="wiz-budget" class="wiz-input" value="315" step="any" min="0">
                </div>
                <div class="wiz-field">
                    <label class="wiz-label">Period</label>
                    <select id="wiz-period" class="wiz-input">
                        <option value="week">Per Week</option>
                        <option value="month">Per Month</option>
                        <option value="year">Per Year</option>
                    </select>
                </div>
                <div class="wiz-field">
                    <label class="wiz-label">Available Work Hours</label>
                    <div class="wiz-time-row">
                        <input type="number" id="wiz-hours" class="wiz-input wiz-time-input" value="40" step="any" min="0">
                        <select id="wiz-hours-unit" class="wiz-input wiz-time-unit">
                            <option value="hrs">hrs</option>
                            <option value="min">min</option>
                            <option value="sec">sec</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <!-- Step 2: Products -->
        <section class="wiz-section">
            <h2 class="wiz-heading">Products</h2>
            <div id="wiz-products" class="wiz-products">
                <div class="wiz-product prod-a">
                    <div class="wiz-prod-header">
                        <span class="wiz-prod-label">Product 1 (X)</span>
                        <span class="color-dot gold"></span>
                    </div>
                    <input type="text" id="prod-a-name" class="wiz-input wiz-prod-name" value="Table (X)" placeholder="Product name">
                    <div class="wiz-prod-fields">
                        <div class="wiz-field">
                            <label class="wiz-label">Sale Price ($)</label>
                            <input type="number" id="prod-a-sale" class="wiz-input" value="90" step="any" min="0">
                        </div>
                        <div class="wiz-field">
                            <label class="wiz-label">Cost to Make ($)</label>
                            <input type="number" id="prod-a-cost" class="wiz-input" value="15" step="any" min="0">
                        </div>
                        <div class="wiz-field">
                            <label class="wiz-label">Time to Make</label>
                            <div class="wiz-time-row">
                                <input type="number" id="prod-a-time" class="wiz-input wiz-time-input" value="2" step="any" min="0">
                                <select id="prod-a-time-unit" class="wiz-input wiz-time-unit">
                                    <option value="hrs">hrs</option>
                                    <option value="min">min</option>
                                    <option value="sec">sec</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wiz-product prod-b">
                    <div class="wiz-prod-header">
                        <span class="wiz-prod-label">Product 2 (Y)</span>
                        <span class="color-dot teal"></span>
                    </div>
                    <input type="text" id="prod-b-name" class="wiz-input wiz-prod-name" value="Chair (Y)" placeholder="Product name">
                    <div class="wiz-prod-fields">
                        <div class="wiz-field">
                            <label class="wiz-label">Sale Price ($)</label>
                            <input type="number" id="prod-b-sale" class="wiz-input" value="180" step="any" min="0">
                        </div>
                        <div class="wiz-field">
                            <label class="wiz-label">Cost to Make ($)</label>
                            <input type="number" id="prod-b-cost" class="wiz-input" value="45" step="any" min="0">
                        </div>
                        <div class="wiz-field">
                            <label class="wiz-label">Time to Make</label>
                            <div class="wiz-time-row">
                                <input type="number" id="prod-b-time" class="wiz-input wiz-time-input" value="5" step="any" min="0">
                                <select id="prod-b-time-unit" class="wiz-input wiz-time-unit">
                                    <option value="hrs">hrs</option>
                                    <option value="min">min</option>
                                    <option value="sec">sec</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Actions -->
        <div class="wiz-actions">
            <button class="btn btn-primary-lg" id="btn-calculate">Calculate &amp; Solve</button>
            <button class="btn btn-new" id="btn-new">New Problem</button>
        </div>

        <!-- ─── Results ─── -->
        <div id="results-area" class="results-area" style="display:none;">

            <section class="wiz-section">
                <h2 class="wiz-heading">Mathematical Formulation</h2>
                <div class="formulation-grid">
                    <div>
                        <span class="eq-label">Objective Function</span>
                        <div class="equation" id="math-objective">Max S = 90X + 180Y</div>
                    </div>
                    <div>
                        <span class="eq-label">Constraints</span>
                        <div id="math-constraints"></div>
                        <div class="equation">X &ge; 0, &ensp;Y &ge; 0</div>
                    </div>
                </div>
            </section>

            <section class="wiz-section">
                <h2 class="wiz-heading">Corner Point Evaluation</h2>
                <p class="wiz-note">The optimal solution always lies at a vertex of the feasible region.</p>
                <div class="table-wrap">
                    <table class="corner-table">
                        <thead>
                            <tr>
                                <th>Corner Point (X, Y)</th>
                                <th>Feasible?</th>
                                <th>S = sale&times;X + sale&times;Y</th>
                                <th>Analysis</th>
                            </tr>
                        </thead>
                        <tbody id="corner-table-body"></tbody>
                    </table>
                </div>
            </section>

            <section class="wiz-section">
                <h2 class="wiz-heading">Feasible Region Graph</h2>
                <div class="graph-wrap wiz-graph" id="graph-container">
                    <svg id="svg-graph" viewBox="0 0 520 360" preserveAspectRatio="xMidYMid meet"></svg>
                    <div class="graph-tooltip" id="graph-tooltip"></div>
                </div>
            </section>

            <section class="wiz-section optimal-section">
                <h2 class="wiz-heading">Optimal Solution</h2>
                <div id="optimal-result" class="optimal-result"></div>
                <div class="wiz-actions" style="margin-top:16px;">
                    <button class="btn btn-save" id="btn-save-problem">Save Problem</button>
                </div>
            </section>
        </div>
    </main>

    <div id="toast-container" class="toast-container"></div>

    <script src="optimizer.js"></script>
</body>
</html>
