-- ============================================================
--  Optimizer DB — Setup Script
--  Drops existing database and recreates from scratch
-- ============================================================

DROP DATABASE IF EXISTS optimizer_db;
CREATE DATABASE optimizer_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE optimizer_db;

CREATE TABLE IF NOT EXISTS problems (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(150)   NOT NULL,
  prod_a_name   VARCHAR(100)   NOT NULL DEFAULT 'Product A',
  prod_a_sale   DECIMAL(10,2)  NOT NULL DEFAULT 0,
  prod_a_cost   DECIMAL(10,2)  NOT NULL DEFAULT 0,
  prod_a_time   DECIMAL(10,4)  NOT NULL DEFAULT 0,
  prod_a_time_unit VARCHAR(10) NOT NULL DEFAULT 'hrs',
  prod_b_name   VARCHAR(100)   NOT NULL DEFAULT 'Product B',
  prod_b_sale   DECIMAL(10,2)  NOT NULL DEFAULT 0,
  prod_b_cost   DECIMAL(10,2)  NOT NULL DEFAULT 0,
  prod_b_time   DECIMAL(10,4)  NOT NULL DEFAULT 0,
  prod_b_time_unit VARCHAR(10) NOT NULL DEFAULT 'hrs',
  budget        DECIMAL(10,2)  NOT NULL DEFAULT 0,
  budget_period VARCHAR(10)    NOT NULL DEFAULT 'week',
  work_hours    DECIMAL(10,2)  NOT NULL DEFAULT 0,
  work_hours_unit VARCHAR(10) NOT NULL DEFAULT 'hrs',
  created_at    TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS constraints (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  problem_id INT           NOT NULL,
  name       VARCHAR(100)  NOT NULL,
  coef_a     DECIMAL(10,4) NOT NULL DEFAULT 0,
  coef_b     DECIMAL(10,4) NOT NULL DEFAULT 0,
  max_val    DECIMAL(10,4) NOT NULL DEFAULT 0,
  unit       VARCHAR(50)   NOT NULL DEFAULT 'units',
  FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS results (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  problem_id     INT            NOT NULL,
  optimal_profit DECIMAL(14,4)  NOT NULL,
  qty_a          DECIMAL(14,4)  NOT NULL,
  qty_b          DECIMAL(14,4)  NOT NULL,
  solved_at      TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO problems (name, prod_a_name, prod_a_sale, prod_a_cost, prod_a_time, prod_a_time_unit,
                             prod_b_name, prod_b_sale, prod_b_cost, prod_b_time, prod_b_time_unit,
                             budget, budget_period, work_hours, work_hours_unit) VALUES
  ('Furniture Workshop (Example)', 'Wooden Table', 90, 15, 2, 'hrs', 'Wooden Chair', 180, 45, 5, 'hrs', 315, 'week', 40, 'hrs'),
  ('Bakery (Example)',             'Cake',        160, 30, 3, 'hrs', 'Bread',      60, 10, 1, 'hrs', 240, 'week', 48, 'hrs'),
  ('Electronics (Example)',        'Smart Speaker', 85, 25, 1.5,'hrs', 'Headphones', 130, 40, 2, 'hrs', 400, 'week', 50, 'hrs');

INSERT INTO constraints (problem_id, name, coef_a, coef_b, max_val, unit) VALUES
  (1, 'Budget Constraint',  15, 45, 315, '$'),
  (1, 'Time Constraint',    2,   5,  40, 'hrs');

INSERT INTO constraints (problem_id, name, coef_a, coef_b, max_val, unit) VALUES
  (2, 'Budget Constraint', 30, 10,  240, '$'),
  (2, 'Time Constraint',   3,   1,   48, 'hrs');

INSERT INTO constraints (problem_id, name, coef_a, coef_b, max_val, unit) VALUES
  (3, 'Budget Constraint', 25, 40, 400, '$'),
  (3, 'Time Constraint',   1.5, 2,  50, 'hrs');
