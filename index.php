<?php
    require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'identité</title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
    <div class="container">
        <h1>Ajouter une nouvelle personne</h1>
        <form action="traitement.php" method="post" enctype="multipart/form-data">

            <!-- Nom -->
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" required placeholder="Ex: NKURUNZIZA">
            </div>

            <!-- Prenom -->
             <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" required placeholder="Ex: Jean">
            </div>

            <!-- Date de naissance-->
             <div class="form-group">
                <label for="date_naissance">Date de naissance *</label>
                <input type="text" id="date_naissance" name="date_naissance" required placeholder="YYYY-MM-DD">
            </div>

            <!-- Identifiant unique -->
             <div class="form-group">
                <label for="identifiant">Identifiant (généré automatiquement)</label>
                <input type="text" id="identifiant" name="identifiant" readonly placeholder="Sera ajoutée...">
            </div>

            <!-- Photo passeport -->
             <div class="form-group">
                <label for="photo">Photo passeport (format JPG/PNG, max 2Mo) *</label>
                <input type="file" id="photo" name="photo" accept="image/jpeg,image/png">
            </div>

            <!-- Bouton envoie -->
             <button type="submit" class="btn-submit">Enregistrer la personne</button>
        </form>

        <p style="text-align:center; margin:20px 0;">
            <a href="liste.php" style="font-size:1.1rem;">Voir la liste des personnes enregistrées</a>
        </p>
    </div>
</body>
</html>