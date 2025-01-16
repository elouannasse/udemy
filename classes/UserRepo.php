<?php
class UserRepo {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function emailExists($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() !== false;
    }

    public function register($nom, $email, $password, $role) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO users (nom, email, password, role) VALUES (:nom, :email, :password, :role)");
        $stmt->execute([
            'nom' => $nom,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role
        ]);
        return 'Inscription réussie. Vous pouvez maintenant vous connecter.';
    }
}
?>