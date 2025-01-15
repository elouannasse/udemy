<?php
class Cours {
    private $id;
    private $title;
    private $description;
    private $contenu;
    private $tags;
    private $categorie;
    private $enseignant;

    public function __construct($title, $description, $contenu, $categorie, $enseignant) {
        $this->title = $title;
        $this->description = $description;
        $this->contenu = $contenu;
        $this->categorie = $categorie;
        $this->enseignant = $enseignant;
        $this->tags = array(); 
    }

    // Getters and Setters
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getDescription() { return $this->description; }
    public function getContenu() { return $this->contenu; }
    public function getTags() { return $this->tags; }
    public function getCategorie() { return $this->categorie; }
}