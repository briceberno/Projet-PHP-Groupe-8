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
</head>
<body>

<div class="container">
    <h1>Liste des personnes enregistrées</h1>
    
    <p style="margin-bottom: 20px;">
        <a href="index.php" class="btn-submit" style="display:inline-block; width:auto; padding:10px 20px; text-decoration:none;">
            ← Ajouter une nouvelle personne
        </a>
    </p>

    <?php
    $stmt = $pdo->query("SELECT * FROM personne ORDER BY created_at DESC");
    $personnes = $stmt->fetchAll();

    if (empty($personnes)) {
        echo "<p>Aucune personne enregistrée pour le moment.</p>";
    } else {
        echo '<table border="1" cellpadding="12" cellspacing="0" style="width:100%; border-collapse: collapse; margin-top:20px;">';
        echo '<tr style="background:#3498db; color:white;">
                <th>Photo</th>
                <th>Identifiant</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date de naissance</th>
                <th>Actions</th>
              </tr>';

        foreach ($personnes as $p) {
            $photo = !empty($p['photo_path']) ? htmlspecialchars($p['photo_path']) : 'https://via.placeholder.com/60x80?text=Pas+de+photo';
            echo '<tr>';
            echo '<td><img src="' . $photo . '" width="60" height="80" style="object-fit:cover; border:1px solid #ddd;"></td>';
            echo '<td><strong>' . htmlspecialchars($p['identifiant']) . '</strong></td>';
            echo '<td>' . htmlspecialchars($p['nom']) . '</td>';
            echo '<td>' . htmlspecialchars($p['prenom']) . '</td>';
            echo '<td>' . htmlspecialchars($p['date_naissance']) . '</td>';
            echo '<td>
                    <a href="edit.php?id=' . $p['id'] . '">Modifier</a> | 
                    <a href="delete.php?id=' . $p['id'] . '" 
                       onclick="return confirm(\'Voulez-vous vraiment supprimer cette personne ?\');">
                       Supprimer
                    </a>
                  </td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    ?>
</div>

</body>
</html>