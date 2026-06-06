# OR Production Optimizer

A linear programming tool for production planning. Define products, set resource constraints, and instantly calculate the optimal production mix to maximize profit. Built with PHP, MySQL, and vanilla JavaScript.


## Quick Start (XAMPP on Windows)

### 1. Clone the Repository

open CMD inside your htdocs file

```bash
git clone https://github.com/akmxlhilmi/or-project.git
cd or-project
```

> **Note:** Replace `YOUR-USERNAME` with your actual GitHub username. The project must live inside your web server's document root (e.g., `htdocs` for XAMPP).

### 2. Start XAMPP Services

Open **XAMPP Control Panel** and start:
- **Apache**
- **MySQL**

### 3. Set Up the Database

**Option A: use setup.php**
1. Copy 
```bash 
http://localhost/Your-project-name/Operational-Research-Project/setup.php 
```
2. Paste the URL inside your browser.


### 4. Configure Database Credentials (if needed)

Edit `api/db.php` if your MySQL uses non-default credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');        // XAMPP default: empty string
define('DB_NAME', 'optimizer_db');
```

### 5. Open in Browser

- **Homepage:** `http://localhost/or-project/`
- **Optimizer Tool:** `http://localhost/or-project/optimizer.php`
- **Saved Problems:** `http://localhost/or-project/saved.php`

---

## Project Structure

```
or-project/
├── index.php           # Product homepage (landing page)
├── optimizer.php       # LP optimizer tool
├── saved.php           # Saved problems browser
├── style.css           # Unified stylesheet
├── optimizer.js        # LP solver + DOM + API client
├── api/
│   ├── db.php          # MySQLi connection + CORS headers
│   ├── setup.sql       # Database schema + seed data
│   ├── get_problems.php
│   ├── save_problem.php
│   ├── save_result.php
│   └── delete_problem.php
```

---

## How It Works

1. **Define your scenario** — Enter budget, time period, and available work hours
2. **Enter product details** — Name, sale price, cost to make, and production time per unit
3. **Calculate** — The solver auto-derives constraints (budget + time) and finds the optimal production mix
4. **Compare corner points** — See all feasible vertices evaluated side-by-side in the comparison table
5. **Visualize** — Feasible region graph with constraint lines, isoprofit line, and optimal vertex
6. **Save &amp; Load** — Persist problems and results to MySQL; revisit from the Saved Problems page

---

## Requirements

- PHP 7.4+ with MySQLi extension
- MySQL 5.7+ / MariaDB 10.3+
- Web server (Apache/Nginx) — tested with XAMPP

---

## License

MIT — free to use, modify, and distribute.