<?php

class Etudiant extends Utilisateur {
    // Encapsulation
    private $coursInscrits = [];

    public function __construct($nom, $email, $password) {
        parent::__construct($nom, $email, $password, 'etudiant');
    }

    // Implementation de la méthode abstraite (Polymorphisme)
    public function getPermissions() {
        return [
            'consulter_cours',
            'sinscrire_cours',
            'voir_profil'
        ];
    }

    // Implementation de la méthode abstraite (Polymorphisme)
    public function afficherCours() {
        try {
            $sql = "SELECT c.*, u.nom as enseignant_nom 
                    FROM cours c 
                    JOIN inscriptions i ON c.id = i.cours_id 
                    JOIN users u ON c.enseignant_id = u.id
                    WHERE i.etudiant_id = :etudiant_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':etudiant_id' => $this->id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Méthode d'inscription à un cours
    public function inscriptionCours($coursId) {
        try {
            // Vérifier si déjà inscrit
            if ($this->estDejaInscrit($coursId)) {
                return false;
            }

            $sql = "INSERT INTO inscriptions (etudiant_id, cours_id, date_inscription) 
                    VALUES (:etudiant_id, :cours_id, NOW())";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':etudiant_id' => $this->id,
                ':cours_id' => $coursId
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Méthode de désinscription
    public function desinscriptionCours($coursId) {
        try {
            $sql = "DELETE FROM inscriptions 
                    WHERE etudiant_id = :etudiant_id AND cours_id = :cours_id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':etudiant_id' => $this->id,
                ':cours_id' => $coursId
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Vérification d'inscription (méthode privée - encapsulation)
    private function estDejaInscrit($coursId) {
        $sql = "SELECT COUNT(*) FROM inscriptions 
                WHERE etudiant_id = :etudiant_id AND cours_id = :cours_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':etudiant_id' => $this->id,
            ':cours_id' => $coursId
        ]);
        
        return $stmt->fetchColumn() > 0;
    }

    // Méthode pour obtenir les statistiques
    public function getStatistiques() {
        try {
            $stats = [];
            
            // Nombre total de cours suivis
            $sql1 = "SELECT COUNT(*) as total FROM inscriptions WHERE etudiant_id = :id";
            $stmt = $this->db->prepare($sql1);
            $stmt->execute([':id' => $this->id]);
            $stats['total_cours'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Cours par catégorie
            $sql2 = "SELECT cat.nom, COUNT(*) as count 
                     FROM inscriptions i 
                     JOIN cours c ON i.cours_id = c.id 
                     JOIN categories cat ON c.categorie_id = cat.id 
                     WHERE i.etudiant_id = :id 
                     GROUP BY cat.id";
            $stmt = $this->db->prepare($sql2);
            $stmt->execute([':id' => $this->id]);
            $stats['cours_par_categorie'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}