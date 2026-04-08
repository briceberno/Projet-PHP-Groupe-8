<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID invalide.");
}

$stmt = $pdo->prepare("SELECT * FROM personne WHERE id = ?");
$stmt->execute([$id]);
$personne = $stmt->fetch();

if (!$personne) {
    die("Personne non trouvée.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails - <?= htmlspecialchars($personne['identifiant']) ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .photo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .photo-container img {
            width: 220px;
            height: 280px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            border: 4px solid #f8f9fa;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            background: white;
        }

        .info-table th {
            background: #f1f3f5;
            color: #2c3e50;
            padding: 16px 20px;
            text-align: left;
            width: 280px;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
        }

        .info-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #dee2e6;
            color: #34495e;
        }

        .actions {
            text-align: center;
            margin-top: 40px;
        }

        .btn {
            display: inline-block;
            padding: 14px 28px;
            margin: 0 10px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.05rem;
            transition: all 0.3s ease;
        }

        .btn-modifier {
            background: #3498db;
            color: white;
        }

        .btn-modifier:hover {
            background: #2980b9;
            transform: translateY(-3px);
        }

        .btn-retour {
            background: #95a5a6;
            color: white;
        }

        .btn-retour:hover {
            background: #7f8c8d;
            transform: translateY(-3px);
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .identifiant {
            font-size: 1.3rem;
            color: #27ae60;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Détails de la personne</h1>
        <p class="identifiant"><?= htmlspecialchars($personne['identifiant']) ?></p>
    </div>

    <!-- Photo -->
    <div class="photo-container">
        <?php if (!empty($personne['photo_path'])): ?>
            <img src="<?= htmlspecialchars($personne['photo_path']) ?>" alt="Photo passeport">
        <?php else: ?>
            <img src="https://via.placeholder.com/220x280?text=Pas+de+photo" alt="Pas de photo">
        <?php endif; ?>
    </div>

    <!-- Informations détaillées -->
    <table class="info-table">
        <tr>
            <th>Nom</th>
            <td><?= htmlspecialchars($personne['nom']) ?></td>
        </tr>
        <tr>
            <th>Prénom</th>
            <td><?= htmlspecialchars($personne['prenom']) ?></td>
        </tr>
        <tr>
            <th>Genre</th>
            <td><?= htmlspecialchars($personne['genre']) ?></td>
        </tr>
        <tr>
            <th>Date de naissance</th>
            <td><?= htmlspecialchars($personne['date_naissance']) ?></td>
        </tr>
        <tr>
            <th>Lieu de naissance</th>
            <td><?= htmlspecialchars($personne['lieu_naissance']) ?></td>
        </tr>
        <tr>
            <th>Nom du père</th>
            <td><?= htmlspecialchars($personne['nom_pere'] ?? 'Non renseigné') ?></td>
        </tr>
        <tr>
            <th>Nom de la mère</th>
            <td><?= htmlspecialchars($personne['nom_mere'] ?? 'Non renseigné') ?></td>
        </tr>
    </table>

    <!-- Boutons d'action -->
    <div class="actions">
        <a href="edit.php?id=<?= $personne['id'] ?>" class="btn btn-modifier">
            ✏️ Modifier les informations
        </a>
        <a href="liste.php" class="btn btn-retour">
            ← Retour à la liste
        </a>
    </div>
</div>

</body>
</html>