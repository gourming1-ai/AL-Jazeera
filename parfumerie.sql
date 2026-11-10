CREATE DATABASE IF NOT EXISTS parfumerie
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE parfumerie;

CREATE TABLE MARQUE (
    idMarque INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE FAMILLE (
    idFamille INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL
);

CREATE TABLE EAU (
    idEau INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL
);

CREATE TABLE CONTENANCE (
    idContenance INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL
);

CREATE TABLE FRAGRANCE (
    idFragrance INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL
);

-- Ajout : champ role (admin / client) pour le back-office
CREATE TABLE USER (
    idUser INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(100) NOT NULL UNIQUE,
    pwd VARCHAR(255) NOT NULL,            -- hash via password_hash()
    pseudo VARCHAR(100) NOT NULL,
    role ENUM('admin','client') NOT NULL DEFAULT 'client'
);

-- Modifs : suppression de PARFUM.prix (le prix dépend de la contenance),
-- idEau devient une clé étrangère (un parfum = un seul type d'eau)
CREATE TABLE PARFUM (
    idParfum INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    photo VARCHAR(255),
    idMarque INT NOT NULL,
    idFamille INT NOT NULL,
    idEau INT NOT NULL,
    FOREIGN KEY (idMarque)  REFERENCES MARQUE(idMarque),
    FOREIGN KEY (idFamille) REFERENCES FAMILLE(idFamille),
    FOREIGN KEY (idEau)     REFERENCES EAU(idEau)
);

CREATE TABLE REGROUPE (
    idFamille INT NOT NULL,
    idFragrance INT NOT NULL,
    PRIMARY KEY (idFamille, idFragrance),
    FOREIGN KEY (idFamille)   REFERENCES FAMILLE(idFamille),
    FOREIGN KEY (idFragrance) REFERENCES FRAGRANCE(idFragrance)
);

CREATE TABLE A_POUR_CONTENANCE (
    idParfum INT NOT NULL,
    idContenance INT NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (idParfum, idContenance),
    FOREIGN KEY (idParfum)     REFERENCES PARFUM(idParfum),
    FOREIGN KEY (idContenance) REFERENCES CONTENANCE(idContenance),
    CHECK (prix >= 0)
);

-- Ajout : note, commentaire, dateAvis
CREATE TABLE DONNE_UN_AVIS (
    idParfum INT NOT NULL,
    idUser INT NOT NULL,
    note TINYINT NOT NULL,
    commentaire TEXT,
    dateAvis DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idParfum, idUser),
    FOREIGN KEY (idParfum) REFERENCES PARFUM(idParfum),
    FOREIGN KEY (idUser)   REFERENCES USER(idUser),
    CHECK (note BETWEEN 1 AND 5)
);

CREATE TABLE POSSEDE (
    idParfum INT NOT NULL,
    idFragrance INT NOT NULL,
    PRIMARY KEY (idParfum, idFragrance),
    FOREIGN KEY (idParfum)    REFERENCES PARFUM(idParfum),
    FOREIGN KEY (idFragrance) REFERENCES FRAGRANCE(idFragrance)
);
