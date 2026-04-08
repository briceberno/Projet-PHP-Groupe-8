<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des personnes</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 1200px;     /* ← On élargit la fenêtre */
            margin: 40px auto;
            padding: 30px;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
            border-radius: 12px;
            overflow: hidden;
        }

        th {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 16px 12px;
            text-align: left;
            font-weight: 600;
            white-space: nowrap;
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        tr:hover {
            background-color: #f8fbff;
        }

        .photo-cell img {
            width: 70px;
            height: 85px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #ecf0f1;
        }

        .actions a {
            margin-right: 12px;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 14px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .actions a:hover {
            transform: translateY(-2px);
        }

        .btn-modifier { color: #2980b9; }
        .btn-supprimer { color: #e74c3c; }
        .btn-details { 
            color: #27ae60; 
            font-weight: 600;
        }

        .btn-modifier:hover, .btn-supprimer:hover, .btn-details:hover {
            background: #ecf0f1;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 500;
            border: 1px solid #c3e6cb;
        }

        h1 {
            color: #2c3e50;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Liste des personnes enregistrées</h1>
    
    <p style="margin: 25px 0 15px;">
        <a href="index.php" class="btn-submit" style="display:inline-block; width:auto; padding:14px 32px; text-decoration:none; font-size:1.1rem;">
            + Ajouter une nouvelle personne
        </a>
    </p>

    <?php if (isset($_GET['success'])): ?>
        <div class="success-message">
            ✓ Personne ajoutée avec succès !
        </div>
    <?php endif; ?>

    <div class="table-container">
        <?php
        $stmt = $pdo->query("SELECT id, identifiant, nom, prenom, genre, date_naissance, lieu_naissance, photo_path 
                             FROM personne 
                             ORDER BY created_at DESC");
        $personnes = $stmt->fetchAll();

        if (empty($personnes)) {
            echo "<p>Aucune personne enregistrée pour le moment.</p>";
        } else {
            echo '<table>';
            echo '<thead>';
            echo '<tr>
                    <th>Photo</th>
                    <th>Identifiant</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Genre</th>
                    <th>Date de naissance</th>
                    <th>Lieu de naissance</th>
                    <th>Actions</th>
                  </tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($personnes as $p) {
                $photo = !empty($p['photo_path']) 
                    ? htmlspecialchars($p['photo_path']) 
                    : 'https://via.placeholder.com/70x85?text=Pas+de+photo';

                echo '<tr>';
                echo '<td class="photo-cell"><img src="' . $photo . '" alt="Photo"></td>';
                echo '<td><strong>' . htmlspecialchars($p['identifiant']) . '</strong></td>';
                echo '<td>' . htmlspecialchars($p['nom']) . '</td>';
                echo '<td>' . htmlspecialchars($p['prenom']) . '</td>';
                echo '<td>' . htmlspecialchars($p['genre']) . '</td>';
                echo '<td>' . htmlspecialchars($p['date_naissance']) . '</td>';
                echo '<td>' . htmlspecialchars($p['lieu_naissance']) . '</td>';
                echo '<td class="actions">';
                echo '<a href="edit.php?id=' . $p['id'] . '" class="btn-modifier">Modifier</a>';
                echo '<a href="delete.php?id=' . $p['id'] . '" class="btn-supprimer" onclick="return confirm(\'Voulez-vous vraiment supprimer cette personne ?\');">Supprimer</a>';
                echo '<a href="details.php?id=' . $p['id'] . '" class="btn-details">Voir détails</a>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        }
        ?>
    </div>
</div>

</body>
</html>
