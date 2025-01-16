<?php
// Inclure les fichiers nécessaires
include_once 'classes/database.php';
include_once 'classes/UserRepo.php';

// Initialiser la variable d'erreur
$errorMessage = '';

// Vérifier si le formulaire a été soumis
if (isset($_POST['submit'])) {
    // Récupérer les données du formulaire
    $nom = htmlspecialchars($_POST['nom']); // Sécuriser les entrées
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Nettoyer l'email
    $password = $_POST['pass'];
    $role = isset($_POST['role']) ? $_POST['role'] : null; // Vérifier si le rôle est défini

    // Valider les champs
    if (empty($nom) || empty($email) || empty($password) || empty($role)) {
        $errorMessage = 'Veuillez remplir tous les champs, y compris le rôle.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Veuillez entrer une adresse email valide.';
    } elseif (strlen($password) < 8) {
        $errorMessage = 'Le mot de passe doit contenir au moins 8 caractères.';
    } else {
        // Vérifier si l'email existe déjà
        $database = new Database();
        $userRepo = new UserRepo($database->getConnection());
        if ($userRepo->emailExists($email)) {
            $errorMessage = 'Cet email est déjà utilisé.';
        } else {
            // Enregistrer l'utilisateur
            $result = $userRepo->register($nom, $email, $password, $role);

            if ($result === 'Inscription réussie. Vous pouvez maintenant vous connecter.') {
                // Rediriger vers la page de connexion après une inscription réussie
                header('Location: login.php');
                exit();
            } else {
                $errorMessage = $result; // Afficher l'erreur retournée par la méthode register
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Youdemy</title>
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
                <a href="login.php" class="text-gray-700 hover:text-green-600">Se connecter</a>
            </div>
        </div>
    </nav>

    <!-- Formulaire d'inscription -->
    <div class="container mx-auto py-12 px-4">
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Créer un compte</h2>
            <form method="POST">
                <!-- Champ Nom -->
                <div class="mb-6">
                    <label for="nom" class="block text-gray-700 mb-2">Nom complet</label>
                    <input type="text" name="nom" id="nom" placeholder="Entrez votre nom complet" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>

                <!-- Champ Email -->
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" placeholder="Entrez votre email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                
                <!-- Champ Mot de passe -->
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 mb-2">Mot de passe</label>
                    <input type="password" name="pass" id="password" placeholder="Entrez votre mot de passe" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>

                <!-- Choix du rôle -->
                <div class="mb-6">
                    <label class="block text-gray-700 mb-2">Vous êtes :</label>
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center">
                            <input type="radio" name="role" value="student" class="form-radio h-5 w-5 text-green-600 focus:ring-green-500" required>
                            <span class="ml-2 text-gray-700">Étudiant</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="role" value="teacher" class="form-radio h-5 w-5 text-green-600 focus:ring-green-500" required>
                            <span class="ml-2 text-gray-700">Enseignant</span>
                        </label>
                    </div>
                </div>

                <!-- Bouton Soumettre -->
                <button type="submit" name="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700">S'inscrire</button>

                <!-- Lien vers la page de connexion -->
                <p class="text-center text-gray-600 mt-4">
                    Vous avez déjà un compte ? <a href="login.php" class="text-green-600 hover:underline">Se connecter</a>
                </p>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>

    <!-- Script pour afficher les messages SweetAlert2 -->
    <script>
        <?php if (!empty($errorMessage)) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: '<?= $errorMessage ?>'
            });
        <?php endif; ?>
    </script>

</body>
</html>