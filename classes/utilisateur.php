<?php

abstract class Utilisateur {
    // Encapsulation avec propriétés protégées
    protected $id;
    protected $nom;
    protected $email;
    protected $password;
    protected $role;
    protected $isActive;
    protected $db;

    // Constructeur
    public function __construct($nom, $email, $password, $role) {
        $this->nom = $nom;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->role = $role;
        $this->isActive = false;
        $this->db = Database::getInstance()->getPDO();
    }

    // Méthodes abstraites pour le polymorphisme
    abstract public function getPermissions();
    abstract public function afficherCours();

    // Méthode d'authentification
    public function authentifier($email, $password) {
        try {
            $sql = "SELECT * FROM users WHERE email = :email AND is_active = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Méthode d'enregistrement
    public function save() {
        try {
            $sql = "INSERT INTO users (nom, email, password, role, is_active) 
                    VALUES (:nom, :email, :password, :role, :is_active)";
            
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                ':nom' => $this->nom,
                ':email' => $this->email,
                ':password' => $this->password,
                ':role' => $this->role,
                ':is_active' => $this->isActive
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

    // Getters et Setters
    public function getId() { 
        return $this->id; 
    }

    public function getNom() { 
        return $this->nom; 
    }

    public function setNom($nom) { 
        $this->nom = $nom; 
    }

    public function getEmail() { 
        return $this->email; 
    }

    public function setEmail($email) { 
        $this->email = $email; 
    }

    public function getRole() { 
        return $this->role; 
    }

    public function isActive() { 
        return $this->isActive; 
    }

    public function setActive($active) {
        $this->isActive = $active;
        try {
            $sql = "UPDATE users SET is_active = :is_active WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':is_active' => $active,
                ':id' => $this->id
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}