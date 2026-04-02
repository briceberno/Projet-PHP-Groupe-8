// Création de la Base de données

CREATE DATABASE IF NOT EXISTS identite;
USE identite;

CREATE TABLE personnes (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(100) NOT NULL,
    prenom          VARCHAR(100) NOT NULL,
    date_naissance  DATE NOT NULL,
    identifiant     VARCHAR(20) UNIQUE NOT NULL,     // ex: ID-2026-00001
    photo_path      VARCHAR(255) DEFAULT NULL,       // chemin vers la photo : uploads/photo-123.jpg
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
