<?php

class Categorie {
    private $id_categorie;
    private $nom;

    public function __construct($nom) {
        $this->nom = $nom;
    }

    public function ajouterCategorie() {
        // Logique d'ajout de catégorie
    }

    // Getters and Setters
    public function getId() { return $this->id_categorie; }
    public function getNom() { return $this->nom; } 
}