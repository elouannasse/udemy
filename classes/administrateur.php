<?php

class Administrateur extends Utilisateur {
    // ID spécifique de la table administrateur
    private $admin_id;

    public function __construct($nom, $email, $password) {
        parent::__construct($nom, $email, $password, 'administrateur');
        $this->createAdminRecord();
    }

    private function createAdminRecord() {
        try {
            if ($this->id) {
                $sql = "INSERT INTO administrateur (utilisateur_id) VALUES (:utilisateur_id)";
                $stmt = $this->db->prepare($sql);
                if ($stmt->execute([':utilisateur_id' => $this->id])) {
                    $this->admin_id = $this->db->lastInsertId();
                }
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    // Validation des comptes enseignants
    public function validerCompteEnseignant($enseignantId) {
        try {
            $sql = "UPDATE utilisateur u 
                    JOIN enseignant e ON u.id = e.utilisateur_id 
                    SET u.is_active = 1 
                    WHERE e.id = :enseignant_id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':enseignant_id' => $enseignantId]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Gestion des utilisateurs
    public function suspendreUtilisateur($utilisateurId) {
        try {
            $sql = "UPDATE utilisateur SET is_active = 0 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $utilisateurId]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function activerUtilisateur($utilisateurId) {
        try {
            $sql = "UPDATE utilisateur SET is_active = 1 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $utilisateurId]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function supprimerUtilisateur($utilisateurId) {
        try {
            $this->db->beginTransaction();

            // Récupérer le rôle de l'utilisateur
            $sql = "SELECT role FROM utilisateur WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $utilisateurId]);
            $role = $stmt->fetchColumn();

            // Supprimer de la table spécifique selon le rôle
            switch ($role) {
                case 'enseignant':
                    $sql = "DELETE FROM enseignant WHERE utilisateur_id = :id";
                    break;
                case 'etudiant':
                    $sql = "DELETE FROM etudiant WHERE utilisateur_id = :id";
                    break;
                case 'administrateur':
                    if ($utilisateurId == $this->id) {
                        throw new Exception("Un administrateur ne peut pas se supprimer lui-même");
                    }
                    $sql = "DELETE FROM administrateur WHERE utilisateur_id = :id";
                    break;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $utilisateurId]);

            // Supprimer de la table utilisateur
            $sql = "DELETE FROM utilisateur WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $utilisateurId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    // Gestion des catégories
    public function ajouterCategorie($nom) {
        try {
            $sql = "INSERT INTO categorie (nom) VALUES (:nom)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':nom' => $nom]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function modifierCategorie($categorieId, $nouveauNom) {
        try {
            $sql = "UPDATE categorie SET nom = :nom WHERE id_categorie = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nom' => $nouveauNom,
                ':id' => $categorieId
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function supprimerCategorie($categorieId) {
        try {
            $sql = "DELETE FROM categorie WHERE id_categorie = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $categorieId]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Gestion des tags
    public function insertionMasseTags($tags) {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO tags (nom) VALUES (:nom)";
            $stmt = $this->db->prepare($sql);

            foreach ($tags as $nom) {
                $stmt->execute([':nom' => $nom]);
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    // Statistiques globales
    public function getStatistiquesGlobales() {
        try {
            $stats = [];
            
            // Nombre total de cours
            $sql1 = "SELECT COUNT(*) as total FROM cours";
            $stmt = $this->db->query($sql1);
            $stats['total_cours'] = $stmt->fetchColumn();
            
            // Répartition des cours par catégorie
            $sql2 = "SELECT c.nom, COUNT(co.id) as count 
                     FROM categorie c 
                     LEFT JOIN cours co ON c.id_categorie = co.categorie_id 
                     GROUP BY c.id_categorie";
            $stmt = $this->db->query($sql2);
            $stats['cours_par_categorie'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Cours avec le plus d'étudiants
            $sql3 = "SELECT c.title, COUNT(i.etudiant_id) as nombre_etudiants 
                     FROM cours c 
                     LEFT JOIN inscription i ON c.id = i.cours_id 
                     GROUP BY c.id 
                     ORDER BY nombre_etudiants DESC 
                     LIMIT 1";
            $stmt = $this->db->query($sql3);
            $stats['cours_plus_populaire'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Top 3 des enseignants
            $sql4 = "SELECT u.nom, COUNT(c.id) as nombre_cours 
                     FROM utilisateur u 
                     JOIN enseignant e ON u.id = e.utilisateur_id 
                     JOIN cours c ON e.id = c.enseignant_id 
                     GROUP BY e.id 
                     ORDER BY nombre_cours DESC 
                     LIMIT 3";
            $stmt = $this->db->query($sql4);
            $stats['top_enseignants'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Implementation des méthodes abstraites
    public function getPermissions() {
        return [
            'gerer_utilisateurs',
            'gerer_cours',
            'gerer_categories',
            'gerer_tags',
            'voir_statistiques'
        ];
    }

    public function afficherCours() {
        try {
            $sql = "SELECT c.*, u.nom as enseignant_nom, cat.nom as categorie_nom 
                    FROM cours c 
                    JOIN enseignant e ON c.enseignant_id = e.id 
                    JOIN utilisateur u ON e.utilisateur_id = u.id 
                    LEFT JOIN categorie cat ON c.categorie_id = cat.id_categorie";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}