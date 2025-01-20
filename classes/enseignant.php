<?php 
require_once 'utilisateur.php'; 

class Enseignant extends Utilisateur {
    private $enseignant_id;
    protected $db;

    public function __construct($nom, $email, $password) {
        try {
            $this->db = new PDO(
                "mysql:host=localhost;dbname=youdemy", 
                "root",  
                ""      
            );
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }

        parent::__construct($nom, $email, $password, 'enseignant');
        $this->createEnseignantRecord();
    }

    public function getPermissions() {
        return [
            'creer_cours' => true,
            'modifier_cours' => true,
            'supprimer_cours' => true,
            'voir_statistiques' => true
        ];
    }

    private function createEnseignantRecord() {
        if ($this->id) {
            $sql = "INSERT INTO enseignant (utilisateur_id) VALUES (:utilisateur_id)";
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute([':utilisateur_id' => $this->id])) {
                $this->enseignant_id = $this->db->lastInsertId();
            }
        }
    }

    public function creerCours($donnees) {
        try {
            $sql = "INSERT INTO cours (titre, description, contenu, categorie_id, enseignant_id) 
                    VALUES (:titre, :description, :contenu, :categorie_id, :enseignant_id)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':titre' => $donnees['titre'],
                ':description' => $donnees['description'],
                ':contenu' => $donnees['contenu'],
                ':categorie_id' => $donnees['categorie_id'],
                ':enseignant_id' => $this->enseignant_id
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function modifierCours($coursId, $donnees) {
        try {
            $sql = "UPDATE cours 
                    SET titre = :titre, 
                        description = :description, 
                        contenu = :contenu,
                        categorie_id = :categorie_id 
                    WHERE id = :id AND enseignant_id = :enseignant_id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':titre' => $donnees['titre'],
                ':description' => $donnees['description'],
                ':contenu' => $donnees['contenu'],
                ':categorie_id' => $donnees['categorie_id'],
                ':id' => $coursId,
                ':enseignant_id' => $this->enseignant_id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function supprimerCours($coursId) {
        try {
            $sql1 = "DELETE FROM inscription WHERE cours_id = :cours_id";
            $stmt = $this->db->prepare($sql1);
            $stmt->execute([':cours_id' => $coursId]);

            $sql2 = "DELETE FROM cours WHERE id = :id AND enseignant_id = :enseignant_id";
            $stmt = $this->db->prepare($sql2);
            return $stmt->execute([
                ':id' => $coursId,
                ':enseignant_id' => $this->enseignant_id
            ]);
        } catch (PDOException $e) {
            return false;
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
            return [];
        }
    }

    public function getStatistiques() {
        try {
            $stats = [];
            
            $sql1 = "SELECT COUNT(*) as total FROM cours WHERE enseignant_id = :id";
            $stmt = $this->db->prepare($sql1);
            $stmt->execute([':id' => $this->enseignant_id]);
            $stats['total_cours'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            $sql2 = "SELECT COUNT(DISTINCT i.etudiant_id) as total 
                     FROM cours c 
                     JOIN inscription i ON c.id = i.cours_id 
                     WHERE c.enseignant_id = :id";
            $stmt = $this->db->prepare($sql2);
            $stmt->execute([':id' => $this->enseignant_id]);
            $stats['total_etudiants'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return $stats;
        } catch (PDOException $e) {
            return [];
        }
    }
}


$enseignant = new Enseignant('zakaria', 'zakarianew@gmail.com', 'zakaria123'); 
$donnees = [
    'titre' => 'introduction a php',
    'description' => 'aprener les bases de php',
    'contenu' => 'contenue du cours PHP...',
    'categorie_id' => 1
];

$resultat = $enseignant->creerCours($donnees); 
if ($resultat) {
    echo "Cours créé avec succès. ID: " . $resultat; 
} else {
    echo "Erreur lors de la création du cours"; 
} 

?>