CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL
);

-- Create Administrateur table
CREATE TABLE administrateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT UNIQUE NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
);

-- Create Enseignant table
CREATE TABLE enseignant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT UNIQUE NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
);

-- Create Etudiant table
CREATE TABLE etudiant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT UNIQUE NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
);

-- Create Categorie table
CREATE TABLE categorie (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,   
    nom VARCHAR(100) NOT NULL
);

-- Create Tags table
CREATE TABLE tags (
    id_tag INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

-- Create Cours table
CREATE TABLE cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    contenu TEXT,
    categorie_id INT,
    enseignant_id INT NOT NULL,
    FOREIGN KEY (categorie_id) REFERENCES categorie(id_categorie),
    FOREIGN KEY (enseignant_id) REFERENCES enseignant(id)
);

-- Create Cours_Tags junction table for many-to-many relationship
CREATE TABLE cours_tags (
    cours_id INT,
    tag_id INT,
    PRIMARY KEY (cours_id, tag_id),
    FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id_tag) ON DELETE CASCADE
);


-- Create Inscription table
CREATE TABLE inscription (
    etudiant_id INT,
    cours_id INT,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (etudiant_id, cours_id),
    FOREIGN KEY (etudiant_id) REFERENCES etudiant(id) ON DELETE CASCADE,
    FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE
);
