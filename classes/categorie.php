<?php
require_once "database.php" ;        
require_once "Database.php";

class Categorie {
    private $id;
    private $nom;
    private $db;

    public function __construct($nom) {
        $this->nom = $nom;
        $this->db = (new Database())->getConnection(); 
    }

    
    public function save() {
        try {
            $sql = "INSERT INTO categories (nom) VALUES (:nom)";  
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                ':nom' => $this->nom
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

    
    public function update() {
        try {
            $sql = "UPDATE categories SET nom = :nom WHERE id = :id";
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

    
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function setNom($nom) { $this->nom = $nom; }
}
 
// $categorie1 = new Categorie('informatique');
//  echo $categorie1->save(); 

 







 ?>