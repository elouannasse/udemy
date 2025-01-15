<?php
// Inclure les fichiers nécessaires
include_once 'classes/database.php';
include_once 'classes/UserRepo.php';
// include_once 'classes/CourseRepo.php';

// Démarrer la session
// session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     header('Location: login.php');
//     exit();
// }

// Initialiser les repositories
$userRepo = new UserRepo();
// $courseRepo = new CourseRepo();

// Récupérer les données pour le dashboard
$pendingTeachers = $userRepo->getPendingTeachers(); // Enseignants en attente de validation 
$allUsers = $userRepo->getAllUsers(); // Tous les utilisateurs
$allCourses = $courseRepo->getAllCourses(); // Tous les cours
$categories = $courseRepo->getAllCategories(); // Toutes les catégories
$tags = $courseRepo->getAllTags(); // Tous les tags
$stats = $courseRepo->getGlobalStats(); // Statistiques globales
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur - Youdemy</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <nav class="bg-white shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div class="flex items-center space-x-4">
                <a href="index.php" class="text-gray-700 hover:text-green-600">Accueil</a>
                <a href="logout.php" class="text-gray-700 hover:text-green-600">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto py-12 px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Dashboard Administrateur</h1>

        <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Validation des comptes enseignants</h2>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Nom</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingTeachers as $teacher) : ?>
                        <tr class="border-b">
                            <td class="px-4 py-2"><?= htmlspecialchars($teacher['nom']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($teacher['email']) ?></td>
                            <td class="px-4 py-2">
                                <a href="validate_teacher.php?id=<?= $teacher['id'] ?>&action=approve" class="text-green-600 hover:underline">Approuver</a>
                                <a href="validate_teacher.php?id=<?= $teacher['id'] ?>&action=reject" class="text-red-600 hover:underline ml-4">Rejeter</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Gestion des utilisateurs</h2>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Nom</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Rôle</th>
                        <th class="px-4 py-2">Statut</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allUsers as $user) : ?>
                        <tr class="border-b">
                            <td class="px-4 py-2"><?= htmlspecialchars($user['nom']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($user['role']) ?></td>
                            <td class="px-4 py-2"><?= $user['is_active'] ? 'Actif' : 'Suspendu' ?></td>
                            <td class="px-4 py-2">
                                <a href="manage_user.php?id=<?= $user['id'] ?>&action=activate" class="text-green-600 hover:underline">Activer</a>
                                <a href="manage_user.php?id=<?= $user['id'] ?>&action=suspend" class="text-yellow-600 hover:underline ml-4">Suspendre</a>
                                <a href="manage_user.php?id=<?= $user['id'] ?>&action=delete" class="text-red-600 hover:underline ml-4">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Gestion des contenus</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Cours</h3>
                    <ul>
                        <?php foreach ($allCourses as $course) : ?>
                            <li class="mb-2">
                                <a href="edit_course.php?id=<?= $course['id'] ?>" class="text-blue-600 hover:underline"><?= htmlspecialchars($course['title']) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="add_course.php" class="mt-4 inline-block bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700">Ajouter un cours</a>
                </div>

                
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Catégories</h3>
                    <ul>
                        <?php foreach ($categories as $category) : ?>
                            <li class="mb-2"><?= htmlspecialchars($category['name']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="add_category.php" class="mt-4 inline-block bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700">Ajouter une catégorie</a>
                </div>

        
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Tags</h3>
                    <ul>
                        <?php foreach ($tags as $tag) : ?>
                            <li class="mb-2"><?= htmlspecialchars($tag['name']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="add_tags.php" class="mt-4 inline-block bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700">Ajouter des tags en masse</a>
                </div>
            </div>
        </div>

    
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Statistiques globales</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
                <div class="bg-green-100 p-4 rounded-lg">
                    <h3 class="text-lg font-bold text-gray-800">Nombre total de cours</h3>
                    <p class="text-2xl font-bold text-green-600"><?= $stats['total_courses'] ?></p> 
                </div>

            
                <div class="bg-blue-100 p-4 rounded-lg">
                    <h3 class="text-lg font-bold text-gray-800">Répartition par catégorie</h3>
                    <ul>
                        <?php foreach ($stats['courses_by_category'] as $category) : ?>
                            <li><?= htmlspecialchars($category['name']) ?> : <?= $category['count'] ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                
                <div class="bg-yellow-100 p-4 rounded-lg">
                    <h3 class="text-lg font-bold text-gray-800">Cours le plus populaire</h3>
                    <p class="text-xl font-bold text-yellow-600"><?= htmlspecialchars($stats['most_popular_course']) ?></p>
                </div>

                
                <div class="bg-purple-100 p-4 rounded-lg">
                    <h3 class="text-lg font-bold text-gray-800">Top 3 enseignants</h3>
                    <ol>
                        <?php foreach ($stats['top_teachers'] as $teacher) : ?>
                            <li><?= htmlspecialchars($teacher['nom']) ?> (<?= $teacher['course_count'] ?> cours)</li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>

</body>
</html>