<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Accès interdit.");
}

// Récupération et nettoyage des données
$nom            = trim($_POST['nom'] ?? '');
$prenom         = trim($_POST['prenom'] ?? '');
$nom_pere       = trim($_POST['nom_pere'] ?? '');
$nom_mere       = trim($_POST['nom_mere'] ?? '');
$date_naissance = $_POST['date_naissance'] ?? '';
$lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
$genre          = $_POST['genre'] ?? '';

// ====================== VALIDATION ======================
$erreurs = [];

if (empty($nom)) $erreurs[] = "Le nom est obligatoire.";
if (empty($prenom)) $erreurs[] = "Le prénom est obligatoire.";
if (empty($date_naissance)) $erreurs[] = "La date de naissance est obligatoire.";
if (empty($lieu_naissance)) $erreurs[] = "Le lieu de naissance est obligatoire.";
if (empty($genre) || !in_array($genre, ['Homme', 'Femme', 'Autre'])) {
    $erreurs[] = "Le genre est obligatoire.";
}

// Validation âge - Version plus précise
if (!empty($date_naissance)) {
    $dateNaiss = new DateTime($date_naissance);
    $aujourdhui = new DateTime();
    
    $interval = $dateNaiss->diff($aujourdhui);
    $age = $interval->y;   // nombre d'années

    if ($age < 18) {
        $erreurs[] = "La personne doit avoir au moins 18 ans. Âge calculé : $age ans.";
    }
}

// Gestion de la photo
$photo_path = null;

if (!empty($_FILES['photo']['name'])) {
    $allowed_types = ['image/jpeg', 'image/png'];
    $max_size = 2 * 1024 * 1024; // 2 Mo

    $file = $_FILES['photo'];
    $file_type = mime_content_type($file['tmp_name']);
    $file_size = $file['size'];

    if (!in_array($file_type, $allowed_types)) {
        $erreurs[] = "Seuls les fichiers JPG et PNG sont autorisés.";
    } elseif ($file_size > $max_size) {
        $erreurs[] = "La photo est trop lourde (maximum 2 Mo).";
    } else {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nouveau_nom = 'photo_' . uniqid() . '.' . $extension;
        $destination = 'uploads/' . $nouveau_nom;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $photo_path = $destination;
        } else {
            $erreurs[] = "Erreur lors de l'upload de la photo.";
        }
    }
}

if (!empty($erreurs)) {
    echo "<h2>Erreurs détectées :</h2><ul>";
    foreach ($erreurs as $err) {
        echo "<li>$err</li>";
    }
    echo "</ul>";
    echo '<p><a href="index.php">← Retour au formulaire</a></p>';
    exit;
}

// ====================== ENREGISTREMENT DANS LA BASE ======================
try {
    $sql_insert = "INSERT INTO personne 
                   (nom, prenom, nom_pere, nom_mere, genre, date_naissance, lieu_naissance, photo_path) 
                   VALUES 
                   (:nom, :prenom, :nom_pere, :nom_mere, :genre, :date_naissance, :lieu_naissance, :photo_path)";
    
    $stmt = $pdo->prepare($sql_insert);
    $stmt->execute([
        ':nom'            => $nom,
        ':prenom'         => $prenom,
        ':nom_pere'       => $nom_pere,
        ':nom_mere'       => $nom_mere,
        ':genre'          => $genre,
        ':date_naissance' => $date_naissance,
        ':lieu_naissance' => $lieu_naissance,
        ':photo_path'     => $photo_path
    ]);

    $nouveau_id = $pdo->lastInsertId();

    // Génération de l'identifiant
    $annee = date('Y');
    $numero = str_pad($nouveau_id, 5, '0', STR_PAD_LEFT);
    $identifiant = "ID-$annee-$numero";

    // Mise à jour de l'identifiant
    $stmt = $pdo->prepare("UPDATE personne SET identifiant = ? WHERE id = ?");
    $stmt->execute([$identifiant, $nouveau_id]);

    // Redirection vers la liste avec succès
    header("Location: liste.php?success=1");
    exit;

} catch (PDOException $e) {
    die("Erreur lors de l'enregistrement : " . $e->getMessage());
}
?>
