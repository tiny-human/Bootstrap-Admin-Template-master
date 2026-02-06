
drop database boot;
CREATE DATABASE IF NOT EXISTS boot;
USE boot;

CREATE TABLE IF NOT EXISTS user(
    id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(75) NOT NULL
);

CREATE TABLE IF NOT EXISTS etat_message(
    id_etat INT PRIMARY KEY AUTO_INCREMENT,
    etat VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS message(
    id_message INT PRIMARY KEY AUTO_INCREMENT,
    id_user_envoyeur INT NOT NULL,
    id_user_recepteur INT NOT NULL,
    contenu TEXT NOT NULL,
    id_etat INT NOT NULL DEFAULT 1,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_lecture TIMESTAMP NULL,
    
    FOREIGN KEY (id_user_envoyeur) REFERENCES user(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_user_recepteur) REFERENCES user(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_etat) REFERENCES etat_message(id_etat)
);


INSERT INTO etat_message (etat) VALUES 
('non_lu'),
('lu');