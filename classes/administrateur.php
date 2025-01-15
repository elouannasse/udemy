<?php
class Administrateur extends Utilisateur {
    public function __construct($nom, $email, $password) {
        parent::__construct($nom, $email, $password, 'administrateur');
    }

    public function validationCompteEnseignants() {
        // Logique de validation des comptes enseignants
    }

    public function activationUtilisateurs() {
        // Logique d'activation des utilisateurs
    }

    public function insertionMasseTags() {
        // Logique d'insertion en masse des tags 
    }
}
?>