<?php

  abstract class Cours {
    // Encapsulation - propriétés privées
    private $id;
    private $titre;
    private $description;
    private $contenu;
    private $tags = [];
    private $categorie;
    
    

    public function __construct($titre, $description, $contenu, $categorie, $enseignant) {
        $this->titre = $titre;
        $this->description = $description;
        $this->contenu = $contenu;
        $this->categorie = $categorie;
        $this->enseignant = $enseignant;
        $this->db = Database::getInstance()->getPDO();
    }

    // Méthode pour sauvegarder le cours
    public function save() {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO cours (titre, description, contenu, categorie_id, enseignant_id) 
                    VALUES (:titre, :description, :contenu, :categorie_id, :enseignant_id)";
            
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                ':titre' => $this->titre,
                ':description' => $this->description,
                ':contenu' => $this->contenu,
                ':categorie_id' => $this->categorie->getId(),
                ':enseignant_id' => $this->enseignant->getId()
            ]);

            if ($success) {
                $this->id = $this->db->lastInsertId();
                $this->sauvegarderTags();
                $this->db->commit();
                return true;
            }

            $this->db->rollBack();
            return false;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    // Méthode pour ajouter un tag
    public function ajouterTag(Tag $tag) {
        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
    }

    // Méthode pour sauvegarder les tags
    private function sauvegarderTags() {
        if (empty($this->tags)) return;

        $sql = "INSERT INTO cours_tags (cours_id, tag_id) VALUES (:cours_id, :tag_id)";
        $stmt = $this->db->prepare($sql);

        foreach ($this->tags as $tag) {
            $stmt->execute([
                ':cours_id' => $this->id,
                ':tag_id' => $tag->getId()
            ]);
        }
    }

    // Méthode pour obtenir les inscrits
    public function getInscrits() {
        try {
            $sql = "SELECT u.* FROM users u 
                    JOIN inscriptions i ON u.id = i.etudiant_id 
                    WHERE i.cours_id = :cours_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cours_id' => $this->id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Méthode pour obtenir les statistiques du cours
    public function getStatistiques() {
        try {
            $stats = [];

            // Nombre d'inscrits
            $sql1 = "SELECT COUNT(*) as total FROM inscriptions WHERE cours_id = :id";
            $stmt = $this->db->prepare($sql1);
            $stmt->execute([':id' => $this->id]);
            $stats['nombre_inscrits'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Autres statistiques...

            return $stats;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getContenu() { return $this->contenu; }
    public function getTags() { return $this->tags; }
    public function getCategorie() { return $this->categorie; }
    public function getEnseignant() { return $this->enseignant; }

    
    // public function setTitre($titre) { $this->titre = $titre; }
    public function setDescription($description) { $this->description = $description; }
    public function setContenu($contenu) { $this->contenu = $contenu; }
    public function setCategorie(Categorie $categorie) { $this->categorie = $categorie; }
}