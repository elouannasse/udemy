<?php

class Enseignant extends Utilisateur {
    
    private $enseignant_id;

    

    public function __construct($nom, $email, $password) {
        parent::__construct($nom, $email, $password, 'enseignant'); 
        $this->createEnseignantRecord();
    }

    
    private function createEnseignantRecord() { 
        try {
            if ($this->id) {
                $sql = "INSERT INTO enseignant (utilisateur_id) VALUES (:utilisateur_id)";
                $stmt = $this->db->prepare($sql);
                if ($stmt->execute([':utilisateur_id' => $this->id])) {
                    $this->enseignant_id = $this->db->lastInsertId();
                }
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function afficherCours() {
        try {
            $sql = "SELECT c.*, COUNT(i.etudiant_id) as nombre_inscrits 
                    FROM cours c 
                    LEFT JOIN inscription i ON c.id = i.cours_id 
                    WHERE c.enseignant_id = :enseignant_id 
                    GROUP BY c.id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':enseignant_id' => $this->enseignant_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    
    public function creerCours($donnees) {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO cours (title, description, contenu, categorie_id, enseignant_id) 
                    VALUES (:titre, :description, :contenu, :categorie_id, :enseignant_id)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':titre' => $donnees['titre'],
                ':description' => $donnees['description'],
                ':contenu' => $donnees['contenu'],
                ':categorie_id' => $donnees['categorie_id'],
                ':enseignant_id' => $this->enseignant_id
            ]);

            $coursId = $this->db->lastInsertId();

            if (!empty($donnees['tags'])) {
                $this->ajouterTagsCours($coursId, $donnees['tags']);
            }

            $this->db->commit();
            return $coursId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    
    public function modifierCours($coursId, $donnees) {
        try {
            if (!$this->estProprietaireCours($coursId)) {
                return false;
            }

            $this->db->beginTransaction();

            $sql = "UPDATE cours 
                    SET title = :titre, 
                        description = :description, 
                        contenu = :contenu,
                        categorie_id = :categorie_id 
                    WHERE id = :id AND enseignant_id = :enseignant_id";
            
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                ':titre' => $donnees['titre'],
                ':description' => $donnees['description'],
                ':contenu' => $donnees['contenu'],
                ':categorie_id' => $donnees['categorie_id'],
                ':id' => $coursId,
                ':enseignant_id' => $this->enseignant_id
            ]);

            if ($success && isset($donnees['tags'])) {
                $this->mettreAJourTags($coursId, $donnees['tags']);
            }

            $this->db->commit();
            return $success;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }


    public function supprimerCours($coursId) {
        try {
            if (!$this->estProprietaireCours($coursId)) {
                return false;
            }

            $this->db->beginTransaction();

            
            $sql1 = "DELETE FROM inscription WHERE cours_id = :cours_id";
            $stmt = $this->db->prepare($sql1);
            $stmt->execute([':cours_id' => $coursId]);

        
            $sql2 = "DELETE FROM cours_tags WHERE cours_id = :cours_id";
            $stmt = $this->db->prepare($sql2);
            $stmt->execute([':cours_id' => $coursId]);

            
            $sql3 = "DELETE FROM cours WHERE id = :id AND enseignant_id = :enseignant_id";
            $stmt = $this->db->prepare($sql3);
            $success = $stmt->execute([
                ':id' => $coursId,
                ':enseignant_id' => $this->enseignant_id
            ]);

            $this->db->commit();
            return $success;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    
    private function estProprietaireCours($coursId) {
        $sql = "SELECT COUNT(*) FROM cours 
                WHERE id = :id AND enseignant_id = :enseignant_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $coursId,
            ':enseignant_id' => $this->enseignant_id
        ]);
        
        return $stmt->fetchColumn() > 0;
    }

    
    private function ajouterTagsCours($coursId, $tags) {
        $sql = "INSERT INTO cours_tags (cours_id, tag_id) VALUES (:cours_id, :tag_id)";
        $stmt = $this->db->prepare($sql);

        foreach ($tags as $tagId) {
            $stmt->execute([
                ':cours_id' => $coursId,
                ':tag_id' => $tagId
            ]);
        }
    }

    
    public function getStatistiques() {
        try {
            $stats = [];
            
            // Nombre total de cours
            $sql1 = "SELECT COUNT(*) as total FROM cours WHERE enseignant_id = :id";
            $stmt = $this->db->prepare($sql1);
            $stmt->execute([':id' => $this->enseignant_id]);
            $stats['total_cours'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Nombre total d'étudiants inscrits
            $sql2 = "SELECT COUNT(DISTINCT i.etudiant_id) as total 
                     FROM cours c 
                     JOIN inscription i ON c.id = i.cours_id 
                     WHERE c.enseignant_id = :id";
            $stmt = $this->db->prepare($sql2);
            $stmt->execute([':id' => $this->enseignant_id]);
            $stats['total_etudiants'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return $stats;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Getters et Setters
    public function getEnseignantId() {
        return $this->enseignant_id;
    }
    
    public function getSpecialite() {
        return $this->specialite;
    }

    public function getBiographie() {
        return $this->biographie;
    }

    public function setSpecialite($specialite) {
        $this->specialite = $specialite;
    }

    public function setBiographie($biographie) {
        $this->biographie = $biographie;
    }
}