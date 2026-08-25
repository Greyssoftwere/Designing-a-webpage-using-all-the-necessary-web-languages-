-- Create this table inside the MySQL database you created in InfinityFree.
-- Do NOT run CREATE DATABASE here; InfinityFree creates the database from its control panel.

CREATE TABLE IF NOT EXISTS people (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    age TINYINT UNSIGNED NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
