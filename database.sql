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