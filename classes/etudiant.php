

<?php
class Etudiant extends Utilisateur {
    public function __construct($nom, $email, $password) {
        parent::__construct($nom, $email, $password, 'etudiant');
    }

    public function inscriptionCours($coursId) {
        // Logique d'inscription à un cours
    }
}

