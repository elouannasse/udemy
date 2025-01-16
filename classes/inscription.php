<?php

class Inscription {
    private $etudiant;
    private $cours;
    private $dateInscription;

    public function __construct($etudiant, $cours) {
        $this->etudiant = $etudiant;
        $this->cours = $cours;
        $this->dateInscription = new DateTime();
    }

    public function getDateInscription() {
        return $this->dateInscription; 
    }
}