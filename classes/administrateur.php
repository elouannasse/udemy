<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/administrateur.php';

session_start();

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: login.php');
    exit();
}

$admin = new Administrateur($_SESSION['nom'], $_SESSION['email'], '');
$stats = $admin->getStatistiquesGlobales();
$enseignantsEnAttente = $admin->getEnseignantsEnAttente();
$tags = $admin->getAllTags();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur - Youdemy</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Menu latéral -->
    <div class="fixed inset-y-0 left-0 w-64 bg-green-600 text-white">
        <div class="flex items-center justify-center h-16 border-b border-green-500">
            <span class="text-2xl font-bold">Youdemy Admin</span>
        </div>
        <nav class="mt-6">
            <a href="#dashboard" class="block py-3 px-4 text-white hover:bg-green-700 active-nav">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="#enseignants" class="block py-3 px-4 text-white hover:bg-green-700">
                <i class="fas fa-chalkboard-teacher mr-2"></i> Enseignants
            </a>
            <a href="#cours" class="block py-3 px-4 text-white hover:bg-green-700">
                <i class="fas fa-book mr-2"></i> Cours
            </a>
            <a href="#categories" class="block py-3 px-4 text-white hover:bg-green-700">
                <i class="fas fa-list mr-2"></i> Catégories
            </a>
            <a href="#tags" class="block py-3 px-4 text-white hover:bg-green-700">
                <i class="fas fa-tags mr-2"></i> Tags
            </a>
            <a href="#statistiques" class="block py-3 px-4 text-white hover:bg-green-700">
                <i class="fas fa-chart-bar mr-2"></i> Statistiques
            </a>
        </nav>
    </div>

    <!-- Contenu principal -->
    <div class="ml-64">
        <!-- En-tête -->
        <header class="bg-white shadow h-16 flex items-center justify-between px-6">
            <div class="flex items-center">
                <span class="text-xl font-semibold text-gray-700">Dashboard</span>
            </div>
            <div class="flex items-center">
                <span class="mr-4 text-gray-600"><?php echo htmlspecialchars($_SESSION['nom']); ?></span>
                <a href="logout.php" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </header>

        <!-- Contenu -->
        <main class="p-6">
            <!-- Statistiques générales -->
            <div id="dashboard" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500">
                            <i class="fas fa-graduation-cap fa-2x"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Total Cours</p>
                            <p class="text-2xl font-bold"><?php echo $stats['total_cours']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Total Étudiants</p>
                            <p class="text-2xl font-bold"><?php echo $stats['total_etudiants']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Total Enseignants</p>
                            <p class="text-2xl font-bold"><?php echo $stats['total_enseignants']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                            <i class="fas fa-tag fa-2x"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Total Tags</p>
                            <p class="text-2xl font-bold"><?php echo count($tags); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Les sections suivantes seront chargées dynamiquement via AJAX -->
            <div id="content-area">
                <!-- Le contenu sera chargé ici -->
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="js/admin-dashboard.js"></script>
</body>
</html>