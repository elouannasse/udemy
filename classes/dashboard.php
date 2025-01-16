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

// Récupération des données
$stats = $admin->getStatistiquesGlobales();
$enseignantsEnAttente = $admin->getEnseignantsEnAttente();
$cours = $admin->afficherCours();
$categories = $admin->getAllCategories();
$tags = $admin->getAllTags();
$utilisateurs = $admin->getAllUsers();
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
</head>
<body class="bg-gray-100">
    <!-- En-tête -->
    <nav class="bg-white shadow-lg mb-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-green-600">Youdemy Admin</span>
                </div>
                <div class="flex items-center">
                    <span class="mr-4"><?= htmlspecialchars($_SESSION['nom']) ?></span>
                    <a href="logout.php" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="max-w-7xl mx-auto px-4">
        <!-- Messages flash -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-8 p-4 rounded-lg <?= $_SESSION['message_type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                <?php 
                echo $_SESSION['message'];
                unset($_SESSION['message'], $_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques globales -->
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4">Statistiques globales</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Total cours -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-book fa-2x"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Total Cours</p>
                            <p class="text-2xl font-bold"><?= $stats['total_cours'] ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total étudiants -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Total Étudiants</p>
                            <p class="text-2xl font-bold"><?= $stats['total_etudiants'] ?></p>
                        </div>
                    </div>
                </div>

                <!-- Cours le plus populaire -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-crown fa-2x"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Cours le plus populaire</p>
                            <p class="text-lg font-bold truncate">
                                <?= htmlspecialchars($stats['cours_plus_populaire']['title']) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total enseignants -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Total Enseignants</p>
                            <p class="text-2xl font-bold"><?= $stats['total_enseignants'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Répartition par catégorie -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Répartition par catégorie</h3>
                <canvas id="categoriesChart"></canvas>
            </div>

            <!-- Top 3 enseignants -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Top 3 Enseignants</h3>
                <div class="space-y-4">
                    <?php foreach ($stats['top_enseignants'] as $index => $enseignant): ?>
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center mr-3">
                            <?= $index + 1 ?>
                        </div>
                        <div>
                            <p class="font-semibold"><?= htmlspecialchars($enseignant['nom']) ?></p>
                            <p class="text-sm text-gray-600"><?= $enseignant['nombre_cours'] ?> cours</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Validation des enseignants -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6">
                <h2 class="text-xl font-bold mb-4">Validation des enseignants</h2>
                <?php if (empty($enseignantsEnAttente)): ?>
                    <p class="text-gray-500">Aucun enseignant en attente de validation</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($enseignantsEnAttente as $enseignant): ?>
                                <tr>
                                    <td class="px-6 py-4"><?= htmlspecialchars($enseignant['nom']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($enseignant['email']) ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="actions/valider_enseignant.php?id=<?= $enseignant['id'] ?>" 
                                           class="text-green-600 hover:text-green-900 mr-4">Valider</a>
                                        <a href="actions/refuser_enseignant.php?id=<?= $enseignant['id'] ?>" 
                                           class="text-red-600 hover:text-red-900"
                                           onclick="return confirm('Êtes-vous sûr de vouloir refuser cet enseignant ?')">
                                            Refuser
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Gestion des utilisateurs -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6">
                <h2 class="text-xl font-bold mb-4">Gestion des utilisateurs</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($utilisateurs as $user): ?>
                            <?php if ($user['role'] !== 'administrateur'): ?>
                            <tr>
                                <td class="px-6 py-4"><?= htmlspecialchars($user['nom']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($user['role']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        <?= $user['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= $user['is_active'] ? 'Actif' : 'Suspendu' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <?php if ($user['is_active']): ?>
                                        <a href="actions/suspendre_utilisateur.php?id=<?= $user['id'] ?>" 
                                           class="text-yellow-600 hover:text-yellow-900 mr-3">Suspendre</a>
                                    <?php else: ?>
                                        <a href="actions/activer_utilisateur.php?id=<?= $user['id'] ?>" 
                                           class="text-green-600 hover:text-green-900 mr-3">Activer</a>
                                    <?php endif; ?>
                                    <a href="actions/supprimer_utilisateur.php?id=<?= $user['id'] ?>" 
                                       class="text-red-600 hover:text-red-900"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                        Supprimer
                                    </a>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Gestion des tags -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Gestion des tags</h2>
                    <button onclick="showModal('modal-tags')"
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>Ajouter des tags
                    </button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($tags as $tag): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100">
                            <?= htmlspecialchars($tag['nom']) ?>
                            <a href="actions/supprimer_tag.php?id=<?= $tag['id_tag'] ?>" 
                               class="ml-2 text-gray-400 hover:text-red-600"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce tag ?')">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajout Tags -->
    <div id="modal-tags" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50