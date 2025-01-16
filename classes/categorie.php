<?php

class Categorie {
    // Encapsulation - propriétés privées
    private $id;
    private $nom;
    private $description;
    private $db;

    public function __construct($nom, $description = '') {
        $this->nom = $nom;
        $this->description = $description;
        $this->db = Database::getInstance()->getPDO();
    }

    // Méthode pour sauvegarder la catégorie
    public function save() {
        try {
            $sql = "INSERT INTO categories (nom, description) 
                    VALUES (:nom, :description)";
            
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                ':nom' => $this->nom,
                ':description' => $this->description
            ]);

            if ($success) {
                $this->id = $this->db->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Méthode pour mettre à jour la catégorie
    public function update() {
        try {
            $sql = "UPDATE categories 
                    SET nom = :nom, description = :description 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nom' => $this->nom,
                ':description' => $this->description,
                ':id' => $this->id
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Méthode pour obtenir les cours de la catégorie
    public function getCours() {
        try {
            $sql = "SELECT * FROM cours WHERE categorie_id = :categorie_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':categorie_id' => $this->id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Méthode statique pour récupérer toutes les catégories
    public static function getAll() {
        try {
            $db = Database::getInstance()->getPDO();
            $sql = "SELECT * FROM categories ORDER BY nom";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Getters et Setters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getDescription() { return $this->description; }

    public function setNom($nom) { $this->nom = $nom; }
    public function setDescription($description) { $this->description = $description; } 
}