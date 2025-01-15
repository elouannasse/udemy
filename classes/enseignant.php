<?php
class Enseignant extends Utilisateur {
    public function __construct($nom, $email, $password) {
        parent::__construct($nom, $email, $password, 'enseignant');
    }

    public function ajouterCours($titre, $description, $contenu, $categorie) {
        // Logique d'ajout de cours
    }

    public function modificationCours($coursId, $data) {
        // Logique de modification de cours
    }

    public function suppressionCours($coursId) {
        // Logique de suppression de cours
    }
}
