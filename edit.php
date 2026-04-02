<?php
require_once 'config.php';

// Récupérer l'ID depuis l'URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID invalide.");
}

// Récupérer les données de la personne
$stmt = $pdo->prepare("SELECT * FROM personne WHERE id = ?");
$stmt->execute([$id]);
$personne = $stmt->fetch();

if (!$personne) {
    die("Personne non trouvée.");
}

// Traitement du formulaire de modification (quand on clique sur Enregistrer)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom            = trim($_POST['nom'] ?? '');
    $prenom         = trim($_POST['prenom'] ?? '');
    $date_naissance = $_POST['date_naissance'] ?? '';
    $photo_path     = $personne['photo_path']; // par défaut on garde l'ancienne photo

    $erreurs = [];
    if (empty($nom)) $erreurs[] = "Le nom est obligatoire.";
    if (empty($prenom)) $erreurs[] = "Le prénom est obligatoire.";
    if (empty($date_naissance)) $erreurs[] = "La date de naissance est obligatoire.";

    // Gestion de la nouvelle photo (si l'utilisateur en envoie une)
    if (!empty($_FILES['photo']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024;

        $file = $_FILES['photo'];
        $file_type = mime_content_type($file['tmp_name']);
        $file_size = $file['size'];

        if (!in_array($file_type, $allowed_types)) {
            $erreurs[] = "Seuls les fichiers JPG et PNG sont autorisés.";
        } elseif ($file_size > $max_size) {
            $erreurs[] = "La photo est trop lourde (max 2 Mo).";
        } else {
            // Supprimer l'ancienne photo si elle existe
            if (!empty($personne['photo_path']) && file_exists($personne['photo_path'])) {
                unlink($personne['photo_path']);
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $nouveau_nom = 'photo_' . uniqid() . '.' . $extension;
            $destination = 'uploads/' . $nouveau_nom;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $photo_path = $destination;
            } else {
                $erreurs[] = "Erreur lors de l'upload de la nouvelle photo.";
            }
        }
    }

    if (empty($erreurs)) {
        try {
            $sql = "UPDATE personne 
                    SET nom = :nom, 
                        prenom = :prenom, 
                        date_naissance = :date_naissance, 
                        photo_path = :photo_path,
                        updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom'            => $nom,
                ':prenom'         => $prenom,
                ':date_naissance' => $date_naissance,
                ':photo_path'     => $photo_path,
                ':id'             => $id
            ]);

            echo "<h2>Modification réussie !</h2>";
            echo '<p><a href="index.php">← Retour à la liste</a></p>';
            exit;
        } catch (PDOException $e) {
            $erreurs[] = "Erreur lors de la mise à jour : " . $e->getMessage();
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
</head>
<body>

<div class="container">
    <h1>Modifier les informations</h1>
    <p><strong>Identifiant :</strong> <?= htmlspecialchars($personne['identifiant']) ?></p>

    <?php if (!empty($erreurs)): ?>
        <div style="color:red; margin-bottom:20px;">
            <?php foreach ($erreurs as $err): ?>
                <p><?= $err ?></p>
            <?php endforeach; ?>
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
            <label for="date_naissance">Date de naissance *</label>
            <input type="date" id="date_naissance" name="date_naissance" 
                   value="<?= htmlspecialchars($personne['date_naissance']) ?>" required>
        </div>

        <div class="form-group">
            <label>Photo actuelle :</label><br>
            <?php if (!empty($personne['photo_path'])): ?>
                <img src="<?= htmlspecialchars($personne['photo_path']) ?>" width="120" style="border:1px solid #ddd; margin-bottom:10px;"><br>
            <?php else: ?>
                <p>Aucune photo</p>
            <?php endif; ?>
            
            <label for="photo">Changer la photo (optionnel) :</label>
            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png">
            <small>Laisser vide pour garder la photo actuelle</small>
        </div>

        <button type="submit" class="btn-submit">Enregistrer les modifications</button>
    </form>

    <p style="margin-top:30px;">
        <a href="index.php">← Annuler et retourner à la liste</a>
    </p>
</div>

</body>
</html>