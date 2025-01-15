<?php
// src/Models/User/Utilisateur.php

abstract class Utilisateur {
    // Propriétés protégées pour l'encapsulation
    protected $id;
    protected $nom;
    protected $email;
    protected $password;
    protected $role;
    protected $isActive;
    protected $db;

    public function __construct($nom, $email, $password, $role) {
        $this->nom = $nom;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->role = $role;
        $this->isActive = false;
        $this->db = Database::getInstance()->getPDO();
    }

    // Méthode abstraite pour le polymorphisme
    abstract public function getPermissions();

    // Méthode d'authentification
    public static function authentifier($email, $password) {
        $db = Database::getInstance()->getPDO();
        try {
            $sql = "SELECT * FROM users WHERE email = :email AND is_active = 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Retourner l'instance appropriée selon le rôle
                switch ($user['role']) {
                    case 'etudiant':
                        return new Etudiant($user['nom'], $user['email'], '');
                    case 'enseignant':
                        return new Enseignant($user['nom'], $user['email'], '');
                    case 'administrateur':
                        return new Administrateur($user['nom'], $user['email'], '');
                }
            }
            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // Méthode pour sauvegarder l'utilisateur
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

    // Méthode pour mettre à jour le profil
    public function updateProfile($data) {
        try {
            $sql = "UPDATE users 
                    SET nom = :nom, email = :email 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nom' => $data['nom'] ?? $this->nom,
                ':email' => $data['email'] ?? $this->email,
                ':id' => $this->id
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Getters and Setters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
    public function isActive() { return $this->isActive; }
    
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