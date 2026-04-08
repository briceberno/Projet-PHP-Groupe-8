<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une personne</title>
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

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 18px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 6px solid #e74c3c;
        }

        .btn {
            display: inline-block;
            padding: 14px 28px;
            margin: 10px 5px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-3px);
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Carte Nationale d'Identité du Burundi (CNI)</h1>

    <form action="traitement.php" method="POST" enctype="multipart/form-data" id="formIdentite">

        <?php if (!empty($erreurs)): ?>
            <div class="error-message">
                <strong>⚠️ Erreurs détectées :</strong>
                <ul style="margin: 10px 0 0 20px;">
                    <?php foreach ($erreurs as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" required>
        </div>

        <div class="form-group">
            <label for="prenom">Prénom *</label>
            <input type="text" id="prenom" name="prenom" required>
        </div>

        <div class="form-group">
            <label for="nom_pere">Nom du père</label>
            <input type="text" id="nom_pere" name="nom_pere">
        </div>

        <div class="form-group">
            <label for="nom_mere">Nom de la mère</label>
            <input type="text" id="nom_mere" name="nom_mere">
        </div>

        <div class="form-group">
            <label for="date_naissance">Date de naissance * (jj/mm/aaaa - minimum 18 ans)</label>
            <input type="date" id="date_naissance" name="date_naissance" required>
        </div>

        <div class="form-group">
            <label for="lieu_naissance">Lieu de naissance *</label>
            <input type="text" id="lieu_naissance" name="lieu_naissance" required>
        </div>

        <div class="form-group">
            <label for="photo">Photo passeport (JPG/PNG, max 2Mo)</label>
            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png">
        </div>

        <div class="form-group">
            <label for="genre">Genre *</label>
            <select id="genre" name="genre" required>
                <option value="">-- Sélectionnez --</option>
                <option value="Homme">Homme</option>
                <option value="Femme">Femme</option>
            </select>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Enregistrer la personne</button>
            <a href="liste.php" class="btn btn-secondary">Voir la liste des personnes</a>
        </div>
    </form>
</div>

<script>
// Validation âge côté client
document.getElementById('formIdentite').addEventListener('submit', function(e) {
    const dateInput = document.getElementById('date_naissance').value;
    if (!dateInput) return;

    const dateNaissance = new Date(dateInput);
    const aujourd’hui = new Date();
    
    let age = aujourd’hui.getFullYear() - dateNaissance.getFullYear();
    const mois = aujourd’hui.getMonth() - dateNaissance.getMonth();
    
    if (mois < 0 || (mois === 0 && aujourd’hui.getDate() < dateNaissance.getDate())) {
        age--;
    }

    if (age < 18) {
        e.preventDefault();
        alert(`La personne doit avoir au moins 18 ans.\nÂge calculé : ${age} ans`);
    }
});
</script>

</body>
</html>
