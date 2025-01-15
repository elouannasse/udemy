<?php
class Database {
    private $pdo;

    public function __construct() {
        // Connexion à la base de données
        $dsn = 'mysql:host=localhost;dbname=youdemy;charset=utf8';
        $username = 'root';
        $password = '';

        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    // Méthode pour exécuter une requête SQL et retourner un seul résultat
    public function fetch($query, $params = []) {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne un tableau associatif
    }

    // Méthode pour exécuter une requête SQL (INSERT, UPDATE, DELETE)
    public function execute($query, $params = []) {
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($params); // Retourne true en cas de succès, false sinon
    }
}
?>