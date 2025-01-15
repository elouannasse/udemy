<?php
class Tags {
    private $id_tag;
    private $nom;
    private $db; 

    public function __construct($db, $nom = null) {
        $this->db = $db; 
        $this->nom = $nom;
    }

    
    public function ajouterTag() {
        $query = "INSERT INTO tags (nom) VALUES (:nom)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':nom' => $this->nom]);

        
        $this->id_tag = $this->db->lastInsertId();
        return $this->id_tag;
    }

    // Modifier un tag
    public function modificationTag($id_tag, $nouveauNom) {
        $query = "UPDATE tags SET nom = :nom WHERE id_tag = :id_tag";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':nom' => $nouveauNom, ':id_tag' => $id_tag]);

        return $stmt->rowCount(); // Retourne le nombre de lignes affectées
    }

    // Supprimer un tag
    public function suppressionTag($id_tag) {
        $query = "DELETE FROM tags WHERE id_tag = :id_tag";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_tag' => $id_tag]);

        return $stmt->rowCount(); 
    }

    // Récupérer un tag par son ID
    public function getTagById($id_tag) {
        $query = "SELECT * FROM tags WHERE id_tag = :id_tag";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_tag' => $id_tag]);

        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne les données du tag
    }

    // Récupérer tous les tags
    public function getAllTags() {
        $query = "SELECT * FROM tags";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne tous les tags
    }

    // Getters and Setters
    public function getId() { return $this->id_tag; }
    public function getNom() { return $this->nom; }
    public function setNom($nom) { $this->nom = $nom; }   
}
?>