<?php
require_once 'config.php';

// Récupérer l'ID
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

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom            = trim($_POST['nom'] ?? '');
    $prenom         = trim($_POST['prenom'] ?? '');
    $nom_pere       = trim($_POST['nom_pere'] ?? '');
    $nom_mere       = trim($_POST['nom_mere'] ?? '');
    $date_naissance = $_POST['date_naissance'] ?? '';
    $lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
    $genre          = $_POST['genre'] ?? '';
    $photo_path     = $personne['photo_path'];

    $erreurs = [];

    if (empty($nom)) $erreurs[] = "Le nom est obligatoire.";
    if (empty($prenom)) $erreurs[] = "Le prénom est obligatoire.";
    if (empty($date_naissance)) $erreurs[] = "La date de naissance est obligatoire.";
    if (empty($lieu_naissance)) $erreurs[] = "Le lieu de naissance est obligatoire.";
    if (empty($genre)) $erreurs[] = "Le genre est obligatoire.";

    // Validation âge
    if (!empty($date_naissance)) {
        $dateNaiss = new DateTime($date_naissance);
        $aujourdhui = new DateTime();
        $age = $aujourdhui->diff($dateNaiss)->y;
        if ($age < 18) {
            $erreurs[] = "La personne doit avoir au moins 18 ans.";
        }
    }

    // Gestion nouvelle photo
    if (!empty($_FILES['photo']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024;

        $file = $_FILES['photo'];
        if (in_array(mime_content_type($file['tmp_name']), $allowed_types) && $file['size'] <= $max_size) {
            // Supprimer ancienne photo
            if (!empty($personne['photo_path']) && file_exists($personne['photo_path'])) {
                unlink($personne['photo_path']);
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $nouveau_nom = 'photo_' . uniqid() . '.' . $extension;
            $destination = 'uploads/' . $nouveau_nom;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $photo_path = $destination;
            }
        } else {
            $erreurs[] = "Photo invalide (seuls JPG/PNG ≤ 2Mo sont acceptés).";
        }
    }

    if (empty($erreurs)) {
        try {
            $sql = "UPDATE personne SET 
                        nom = :nom, 
                        prenom = :prenom,
                        nom_pere = :nom_pere,
                        nom_mere = :nom_mere,
                        genre = :genre,
                        date_naissance = :date_naissance,
                        lieu_naissance = :lieu_naissance,
                        photo_path = :photo_path,
                        updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':nom_pere' => $nom_pere,
                ':nom_mere' => $nom_mere,
                ':genre' => $genre,
                ':date_naissance' => $date_naissance,
                ':lieu_naissance' => $lieu_naissance,
                ':photo_path' => $photo_path,
                ':id' => $id
            ]);

            header("Location: liste.php?success=1");
            exit;
        } catch (PDOException $e) {
            $erreurs[] = "Erreur lors de la mise à jour.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier - <?= htmlspecialchars($personne['identifiant']) ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
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
            margin: 0 8px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
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
    <h1>Modifier les informations</h1>
    <p><strong>Identifiant :</strong> <?= htmlspecialchars($personne['identifiant']) ?></p>

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
    
    <form action="edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($personne['nom']) ?>" required>
        </div>

        <div class="form-group">
            <label for="prenom">Prénom *</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($personne['prenom']) ?>" required>
        </div>

        <div class="form-group">
            <label for="nom_pere">Nom du père</label>
            <input type="text" id="nom_pere" name="nom_pere" value="<?= htmlspecialchars($personne['nom_pere'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="nom_mere">Nom de la mère</label>
            <input type="text" id="nom_mere" name="nom_mere" value="<?= htmlspecialchars($personne['nom_mere'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="genre">Genre *</label>
            <select id="genre" name="genre" required>
                <option value="Homme" <?= $personne['genre'] === 'Homme' ? 'selected' : '' ?>>Homme</option>
                <option value="Femme" <?= $personne['genre'] === 'Femme' ? 'selected' : '' ?>>Femme</option>
            </select>
        </div>

        <div class="form-group">
            <label for="date_naissance">Date de naissance * (minimum 18 ans)</label>
            <input type="date" id="date_naissance" name="date_naissance" 
                   value="<?= htmlspecialchars($personne['date_naissance']) ?>" required>
        </div>

        <div class="form-group">
            <label for="lieu_naissance">Lieu de naissance *</label>
            <input type="text" id="lieu_naissance" name="lieu_naissance" 
                   value="<?= htmlspecialchars($personne['lieu_naissance']) ?>" required>
        </div>

        <div class="form-group">
            <label>Photo actuelle :</label><br>
            <?php if (!empty($personne['photo_path'])): ?>
                <img src="<?= htmlspecialchars($personne['photo_path']) ?>" width="180" style="border-radius:8px; margin:10px 0;"><br>
            <?php endif; ?>
            
            <label for="photo">Changer la photo (optionnel) :</label>
            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png">
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            <a href="liste.php" class="btn btn-secondary">Annuler et retourner à la liste</a>
        </div>
    </form>
</div>

</body>
</html>
