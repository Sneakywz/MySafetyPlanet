-- Création de la table users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    roles JSON NOT NULL DEFAULT '["user"]'
)

-- Suppression de la table users
DROP TABLE users;

DELETE FROM users WHERE id = 4;

-- Mise à jour d'un utilisateur
UPDATE users SET roles = '["user", "admin", "superadmin"]' WHERE id = 2;

ALTER TABLE users ADD remember_me VARCHAR(255) NULL;

-- Création de la table articles
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    user_id INT,
    content TEXT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)

-- Suppression de la table article
DROP TABLE articles;

SELECT * FROM articles as a INNER JOIN users as u ON a.user_id = u.id WHERE a.id =2
SELECT * FROM articles WHERE id = 2;
