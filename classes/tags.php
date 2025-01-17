<?php
require_once 'Database.php';

class Tag {
    private $id;
    private $nom;
    private $db;

    public function __construct($nom) {
        $this->nom = $nom;
        $this->db = (new Database())->getConnection(); 
    }

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

    public static function getAll() {
        try {
            $db = (new Database())->getConnection(); 
            $sql = "SELECT * FROM tags ORDER BY nom";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public static function insertionMasse($tags) {
        try {
            $db = (new Database())->getConnection(); 
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

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function setNom($nom) { $this->nom = $nom; }
}

$tag2 = new Tag('#mathimatique') ;
 echo $tag2->save() ;
?>
