# Operational Research
### Production Optimizer for Small Factory
---

## Team members

| Name | matric Number |
|:------|:-----:|
| Mia Aishah Solehah binti Mashuri | 2240199 |
| Muhammad Akmal bin Mohd Hilmi | 2240204 |
| Nur Umira Dini Binti Hishammudin | 2240220  |
| Nur Farah Hanim binti Nor Azmi | 2240226 |

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
├── api
│   ├── db.php
│   ├── delete_problem.php
│   ├── get_problems.php
│   ├── save_problem.php
│   ├── save_result.php
│   └── setup.sql
├── index.php
├── optimizer.js
├── optimizer.php
├── README.md
├── saved.php
├── setup.php
└── style.css
```
---


## License

MIT — free to use, modify, and distribute.