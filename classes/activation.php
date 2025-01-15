<?php
include_once 'classes/database.php';
include_once 'classes/UserRepo.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $userRepo = new UserRepo();
    $result = $userRepo->activateUser($token);

    if ($result) {
        echo "Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.";
    } else {
        echo "Le token d'activation est invalide ou a expiré.";
    }
}
?>
