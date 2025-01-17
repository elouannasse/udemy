<?php
// Inclure les fichiers nécessaires
include_once 'classes/database.php';
include_once 'classes/UserRepo.php';

session_start();
if (isset($_SESSION['register_success'])) {
   $successMessage = 'Inscription réussie. Vous pouvez maintenant vous connecter.';
   unset($_SESSION['register_success']);
}

// Si l'utilisateur est déjà connecté, rediriger vers la page d'accueil
  
// Initialiser la variable d'erreur
$errorMessage = '';

// Vérifier si le formulaire a été soumis
if (isset($_POST['submit'])) {
   // Récupérer et nettoyer les données du formulaire
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);     
   $password = $_POST['password'];

   // Valider les champs
   if (empty($email) || empty($password)) {
       $errorMessage = 'Veuillez remplir tous les champs.';
   } else {
       // Connexion à la base de données et vérification des identifiants
       try {
           $database = new Database();
           $userRepo = new UserRepo($database->getConnection());
           
           $user = $userRepo->login($email, $password); 
           
           if ($user) {
               // Créer la session
               $_SESSION['user_id'] = $user['id'];
               $_SESSION['user_name'] = $user['nom'];
               $_SESSION['user_role'] = $user['role'];
               
               // Rediriger selon le rôle
               if ($user['role'] === 'teacher') {
                   header('Location: teacher/dashboard.php');
               } elseif ($user['role'] === 'student')  {
                   header('Location: student/dashboard.php');
               }
               
               exit();
           } else {
               $errorMessage = 'Email ou mot de passe incorrect.';
           }
       } catch (Exception $e) {
           $errorMessage = 'Une erreur est survenue. Veuillez réessayer plus tard.';
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
           <a href="index.php" class="flex items-center space-x-2">
               <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center">
                   <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                   </svg>
               </div>
               <span class="text-xl font-bold text-green-600">Youdemy</span>
           </a>
           <div class="flex items-center space-x-4">
               <a href="index.php.php" class="text-gray-700 hover:text-green-600">Accueil</a>
               <a href="register.php" class="text-gray-700 hover:text-green-600">S'inscrire</a>
           </div>
       </div>
   </nav>

   <!-- Formulaire de connexion -->
   <div class="container mx-auto py-12 px-4">
       <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8">
           <h2 class="text-3xl font-bold text-gray-800 mb-6">Connexion</h2>
           <form method="POST">
               <!-- Email -->
               <div class="mb-6">
                   <label for="email" class="block text-gray-700 mb-2">Email</label>
                   <input type="email" id="email" name="email" placeholder="Entrez votre email" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
               </div>

               <!-- Mot de passe -->
               <div class="mb-6">
                   <label for="password" class="block text-gray-700 mb-2">Mot de passe</label>
                   <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
               </div>

               <!-- Bouton de connexion -->
               <button type="submit" name="submit" 
                       class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition duration-200">
                   Se connecter
               </button>

               <!-- Lien d'inscription -->
               <p class="text-center text-gray-600 mt-4">
                   Pas encore de compte ? 
                   <a href="register.php" class="text-green-600 hover:underline">S'inscrire</a>
               </p>
           </form>
       </div>
   </div>

   <!-- SweetAlert2 -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
   
   <!-- Scripts pour les messages -->
   <script>
       <?php if (!empty($errorMessage)) : ?>
           Swal.fire({
               icon: 'error',
               title: 'Erreur',
               text: '<?= $errorMessage ?>'
           });
       <?php endif; ?>

       <?php if (isset($successMessage)) : ?>
           Swal.fire({
               icon: 'success',
               title: 'Succès',
               text: '<?= $successMessage ?>'
           });
       <?php endif; ?>
   </script>

</body>
</html>