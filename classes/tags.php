<?php

class Tag {
    // Encapsulation - propriétés privées
    private $id;
    private $nom;
    private $db;

    public function __construct($nom) {
        $this->nom = $nom;
        $this->db = Database::getInstance()->getPDO(); 
    }

    // Méthode pour sauvegarder le tag
    public function save() {
        try {
            $sql = "INSERT INTO tags (nom) VALUES (:nom)";
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([':nom' => $this->nom]);

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

    // Méthode pour mettre à jour le tag
    public function update() {
        try {
            $sql = "UPDATE tags SET nom = :nom WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nom' => $this->nom,
                ':id' => $this->id
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Méthode pour supprimer le tag
    public function delete() {
        try {
            $sql = "DELETE FROM tags WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $this->id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Méthode pour récupérer les cours associés à ce tag
    public function getCours() {
        try {
            $sql = "SELECT c.* FROM cours c 
                    JOIN cours_tags ct ON c.id = ct.cours_id 
                    WHERE ct.tag_id = :tag_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tag_id' => $this->id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Méthode statique pour récupérer tous les tags
    public static function getAll() {
        try {
            $db = Database::getInstance()->getPDO();
            $sql = "SELECT * FROM tags ORDER BY nom";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Méthode pour insérer en masse des tags (util pour l'admin)
    public static function insertionMasse($tags) {
        try {
            $db = Database::getInstance()->getPDO();
            $db->beginTransaction();

            $sql = "INSERT INTO tags (nom) VALUES (:nom)";
            $stmt = $db->prepare($sql);

            foreach ($tags as $nom) {
                $stmt->execute([':nom' => $nom]);
            }

            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    // Getters et Setters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function setNom($nom) { $this->nom = $nom; }
}
$tag1 = new tag('smc') ;
 echo  $tag1->save() ;


