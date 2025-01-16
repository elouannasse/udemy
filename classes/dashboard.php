<?php
// dashboard-admin.php

// Inclure les fichiers nécessaires




// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: login.php');
    exit();
}

// Initialiser l'administrateur
$admin = new Administrateur($_SESSION['nom'], $_SESSION['email'], '');

// Récupérer les données pour le dashboard
$enseignantsEnAttente = $admin->getEnseignantsEnAttente();
$utilisateurs = $admin->getAllUsers();
$cours = $admin->afficherCours();
$categories = $admin->getAllCategories();
$tags = $admin->getAllTags();
$stats = $admin->getStatistiquesGlobales();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur - Youdemy</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-indigo-600">Youdemy Admin</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700"><?php echo $_SESSION['nom']; ?></span>
                    <a href="logout.php" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Statistiques globales -->
        <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-book fa-2x text-indigo-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Cours</dt>
                                <dd class="text-lg font-bold text-gray-900"><?php echo $stats['total_cours']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users fa-2x text-green-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Étudiants</dt>
                                <dd class="text-lg font-bold text-gray-900"><?php echo $stats['total_etudiants']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chalkboard-teacher fa-2x text-blue-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Enseignants</dt>
                                <dd class="text-lg font-bold text-gray-900"><?php echo $stats['total_enseignants']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-tags fa-2x text-purple-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Tags</dt>
                                <dd class="text-lg font-bold text-gray-900"><?php echo count($tags); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Validation des enseignants en attente -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Enseignants en attente de validation</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date d'inscription</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($enseignantsEnAttente as $enseignant): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($enseignant['nom']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($enseignant['email']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo date('d/m/Y', strtotime($enseignant['date_creation'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="actions/valider_enseignant.php?id=<?php echo $enseignant['id']; ?>" 
                                       class="text-green-600 hover:text-green-900 mr-3">Valider</a>
                                    <a href="actions/refuser_enseignant.php?id=<?php echo $enseignant['id']; ?>" 
                                       class="text-red-600 hover:text-red-900">Refuser</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Gestion des Tags -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Gestion des Tags</h2>
                    <button onclick="document.getElementById('modal-tags').classList.remove('hidden')"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Ajouter des tags
                    </button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($tags as $tag): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100">
                        <?php echo htmlspecialchars($tag['nom']); ?>
                        <a href="actions/supprimer_tag.php?id=<?php echo $tag['id_tag']; ?>" 
                           class="ml-2 text-gray-400 hover:text-red-600">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Modal Ajout Tags -->
        <div id="modal-tags" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Ajouter des tags</h3>
                    <button onclick="document.getElementById('modal-tags').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="actions/ajouter_tags.php" method="POST">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Tags (séparés par des virgules)
                        </label>
                        <textarea name="tags" rows="4" 
                                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                  required></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Ajouter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Top 3 Enseignants -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Top 3 Enseignants</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php foreach ($stats['top_enseignants'] as $index => $enseignant): ?>
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                                <?php echo $index + 1; ?>
                            </div>
                            <div class="ml-4">
                                <div class="text-lg font-medium text-gray-900">
                                    <?php echo htmlspecialchars($enseignant['nom']); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo $enseignant['nombre_cours']; ?> cours
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Fonctions JavaScript pour la gestion dynamique
        function confirmerAction(message) {
            return confirm(message);
        }

        // Gestion des messages flash
        <?php if (isset($_SESSION['message'])): ?>
        alert("<?php echo $_SESSION['message']; ?>");
        <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    </script>
</body>
</html>