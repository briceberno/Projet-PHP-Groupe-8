<?php
// 1. Charger la connexion à la base
require_once 'config.php';

// 2. Vérifier que le formulaire a été soumis via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Accès interdit. Veuillez utiliser le formulaire.");
}

// 3. Récupérer et nettoyer les données texte
$nom            = trim($_POST['nom'] ?? '');
$prenom         = trim($_POST['prenom'] ?? '');
$date_naissance = $_POST['date_naissance'] ?? '';

// 4. Validation minimale côté serveur
$erreurs = [];

if (empty($nom)) {
    $erreurs[] = "Le nom est obligatoire.";
}
if (empty($prenom)) {
    $erreurs[] = "Le prénom est obligatoire.";
}
if (empty($date_naissance) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_naissance)) {
    $erreurs[] = "La date de naissance est invalide (format YYYY-MM-DD attendu).";
}

// Si erreurs → on les affiche et on arrête
if (!empty($erreurs)) {
    echo "<h2>Erreurs détectées :</h2><ul>";
    foreach ($erreurs as $err) {
        echo "<li>$err</li>";
    }
    echo "</ul>";
    echo '<p><a href="index.php">← Retour au formulaire</a></p>';
    exit;
}

// 5. Gestion de la photo (upload)
$photo_path = null;

if (!empty($_FILES['photo']['name'])) {
    $allowed_types = ['image/jpeg', 'image/png'];
    $max_size = 2 * 1024 * 1024; // 2 Mo

    $file = $_FILES['photo'];
    $file_type = mime_content_type($file['tmp_name']);
    $file_size = $file['size'];

    if (!in_array($file_type, $allowed_types)) {
        die("Seuls les fichiers JPG et PNG sont autorisés.");
    }
    if ($file_size > $max_size) {
        die("La photo est trop lourde (max 2 Mo).");
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("Erreur lors de l'upload de la photo.");
    }

    // Générer un nom de fichier unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nouveau_nom = 'photo_' . uniqid() . '.' . $extension;
    $destination = 'uploads/' . $nouveau_nom;

    // Déplacer le fichier temporaire vers uploads/
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $photo_path = $destination;
    } else {
        die("Échec du déplacement de la photo.");
    }
}

try {
    // 1. Insertion temporaire sans identifiant
    $sql_insert = "INSERT INTO personne (nom, prenom, date_naissance, photo_path) 
                   VALUES (:nom, :prenom, :date_naissance, :photo_path)";
    
    $stmt = $pdo->prepare($sql_insert);
    $stmt->execute([
        ':nom'            => $nom,
        ':prenom'         => $prenom,
        ':date_naissance' => $date_naissance,
        ':photo_path'     => $photo_path
    ]);

    // 2. Récupérer l'id auto-incrémenté
    $nouveau_id = $pdo->lastInsertId();

    // 3. Générer l'identifiant unique
    $annee = date('Y');
    $numero = str_pad($nouveau_id, 5, '0', STR_PAD_LEFT);
    $identifiant = "ID-$annee-$numero";

    // 4. Mettre à jour avec l'identifiant
    $stmt = $pdo->prepare("UPDATE personne SET identifiant = ? WHERE id = ?");
    $stmt->execute([$identifiant, $nouveau_id]);

    // Redirection vers la liste avec message de succès
    header("Location: liste.php?success=1");
    exit;

} catch (PDOException $e) {
    die("Erreur lors de l'enregistrement : " . $e->getMessage());
}

?>
