<?php
class Utilisateur {
    protected $id;
    protected $nom;
    protected $email;
    protected $password;
    protected $role;
    protected $db;

    public function __construct($nom, $email, $password, $role) {
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

        $this->nom = $nom;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->role = $role;
        
        $this->createUser();
    }

    private function createUser() {
        try {
            $sql = "INSERT INTO utilisateur (nom, email, password, role) 
                    VALUES (:nom, :email, :password, :role)";
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute([
                ':nom' => $this->nom,
                ':email' => $this->email,
                ':password' => $this->password,
                ':role' => $this->role
            ])) {
                $this->id = $this->db->lastInsertId();
            }
        } catch (PDOException $e) {
            die("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
        }
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getRole() {
        return $this->role;
    }

    // Setters
    public function setNom($nom) {
        $this->nom = $nom;
        $this->updateUser();
    }

    public function setEmail($email) {
        $this->email = $email;
        $this->updateUser();
    }

    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->updateUser();
    }

    // Méthode de mise à jour
    protected function updateUser() {
        try {
            $sql = "UPDATE utilisateur 
                    SET nom = :nom, 
                        email = :email, 
                        password = :password 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nom' => $this->nom,
                ':email' => $this->email,
                ':password' => $this->password,
                ':id' => $this->id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteUser() {
        try {
            $sql = "DELETE FROM utilisateur WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $this->id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }

    public static function login($email, $password) {
        try {
            $db = new PDO(
                "mysql:host=localhost;dbname=youdemy", 
                "root", 
                ""
            );
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT * FROM utilisateur WHERE email = :email";
            $stmt = $db->prepare($sql);
            $stmt->execute([':email' => $email]);
            
            if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($password, $user['password'])) {
                    return $user;
                }
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>