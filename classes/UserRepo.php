<?php
class UserRepo {
    private $db;

    public function __construct() {
        $this->db = new Database(); // Assurez-vous que la classe Database est correctement configurée
    }

    // Vérifie si l'email existe déjà
    public function emailExists($email) {
        $query = "SELECT * FROM utilisateur WHERE email = :email";
        $params = [':email' => $email];
        $result = $this->db->fetch($query, $params);
        return !empty($result);
    }

    // Enregistre un nouvel utilisateur avec vérification par section
    public function register($nom, $email, $password, $role) {
        // Vérifier si la section (rôle) est valide
        $validRoles = ['student', 'teacher', 'admin']; // Rôles autorisés
        if (!in_array($role, $validRoles)) {
            return 'Rôle invalide.';
        }

        // Hasher le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insérer l'utilisateur dans la base de données
        $query = "INSERT INTO utilisateur (nom, email, password, role, is_active) VALUES (:nom, :email, :password, :role, 1)";
        $params = [
            ':nom' => $nom,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role
        ];

        if ($this->db->execute($query, $params)) {
            return 'Inscription réussie. Vous pouvez maintenant vous connecter.';
        } else {
            return 'Erreur lors de l\'inscription.';
        }
    }

    // Méthode pour connecter un utilisateur
    public function login($email, $password) {
        // Récupérer l'utilisateur par email
        $query = "SELECT * FROM utilisateur WHERE email = :email";
        $params = [':email' => $email];
        $user = $this->db->fetch($query, $params);

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if ($user && password_verify($password, $user['password'])) {
            return $user; // Retourne les données de l'utilisateur
        } else {
            return false; // Identifiants incorrects
        }
    }
}
?>