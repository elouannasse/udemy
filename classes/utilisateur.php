<?php
class Utilisateur {
    protected $id;
    protected $nom;
    protected $email;
    protected $password;
    protected $role;

    public function __construct($nom, $email, $password, $role) {
        $this->nom = $nom;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public function authentification() {
        // Logique d'authentification  
    }

    public function registre() {
        // Logique d'enregistrement 
    }

    // Getters and Setters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
}