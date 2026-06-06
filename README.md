# Operational Research
### Production Optimizer for Small Factory
---

## Team members

| Name | Age | City |
|------|-----|------|
| Alice | 25 | London |
| Bob | 30 | Paris |

---

## Quick Start (XAMPP on Windows)

### 1. Clone the Repository

open CMD inside your htdocs file

```bash
git clone https://github.com/akmxlhilmi/Operational-Research-Project.git
cd Operational-Research-Project
```


### 2. Start XAMPP Services

Open **XAMPP Control Panel** and start:
- **Apache**
- **MySQL**

### 3. Set Up the Database

**Option A: use setup.php**
1. Copy 
```bash 
http://localhost/Operational-Research-Project/setup.php 
```
2. Paste the URL inside your browser.

### 4. Open in Browser

- **Homepage:** `http://localhost/Operational-Research-Project/`
- **Optimizer Tool:** `http://localhost/Operational-Research-Project/optimizer.php`
- **Saved Problems:** `http://localhost/Operational-Research-Project/saved.php`

---

## Project Structure

```
Operational-Research-Project/
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


## License

MIT — free to use, modify, and distribute.