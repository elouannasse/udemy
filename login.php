<?php
// Inclure les fichiers nécessaires
include_once 'classes/database.php';
include_once 'classes/UserRepo.php';

// Démarrer la session
session_start();

// Vérifier si le formulaire de connexion a été soumis
if (isset($_POST['submit'])) { 
    // Récupérer les données du formulaire
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Valider les champs
    if (empty($email) || empty($password)) {
        $errorMessage = 'Veuillez remplir tous les champs.';
    } else {
        // Tenter de connecter l'utilisateur
        $userRepo = new UserRepo(); 
        $user = $userRepo->login($email, $password);

        if ($user) {
            // Stocker les informations de l'utilisateur dans la session
            $_SESSION['user'] = $user;

            // Rediriger l'utilisateur en fonction de son rôle
            if ($user['role'] === 'admin') {
                header('Location: admin_dashboard.php'); 
            } elseif ($user['role'] === 'teacher') {
                header('Location: dashboard.php');
            } else {
                header('Location: dashboard.php');
            }
            exit();
        } else {
            $errorMessage = 'Email ou mot de passe incorrect.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Youdemy</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-white shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div class="flex items-center space-x-4">
                <a href="index.php" class="text-gray-700 hover:text-green-600">Accueil</a>
                <a href="registre.php" class="text-gray-700 hover:text-green-600">S'inscrire</a>
            </div>
        </div>
    </nav>

    <!-- Formulaire de connexion -->
    <div class="container mx-auto py-12 px-4">
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Connexion</h2>
            <form method="POST">
                <!-- Champ Email -->
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" placeholder="Entrez votre email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <!-- Champ Mot de passe -->
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 mb-2">Mot de passe</label>
                    <input type="password" name="password" id="password" placeholder="Entrez votre mot de passe" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Bouton Soumettre -->
                <button type="submit" name="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700">Se connecter</button>

                <!-- Lien vers la page d'inscription -->
                <p class="text-center text-gray-600 mt-4">
                    Vous n'avez pas de compte ? <a href="registre.php" class="text-green-600 hover:underline">S'inscrire</a>
                </p>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>

    <!-- Script pour afficher les messages SweetAlert2 -->
    <script>
        <?php if (isset($errorMessage)) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: '<?= $errorMessage ?>'
            });
        <?php endif; ?>
    </script>

</body>
</html>