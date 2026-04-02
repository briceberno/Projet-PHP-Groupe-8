<?php
require_once 'config.php';

// Récupérer l'ID depuis l'URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID invalide.");
}

// Récupérer les infos de la personne (surtout pour supprimer la photo)
$stmt = $pdo->prepare("SELECT photo_path FROM personne WHERE id = ?");
$stmt->execute([$id]);
$personne = $stmt->fetch();

if (!$personne) {
    die("Personne non trouvée.");
}

// Supprimer la photo du dossier si elle existe
if (!empty($personne['photo_path']) && file_exists($personne['photo_path'])) {
    unlink($personne['photo_path']);
}

// Supprimer la personne de la base de données
try {
    $stmt = $pdo->prepare("DELETE FROM personne WHERE id = ?");
    $stmt->execute([$id]);

    // Message de succès
    echo "<h2>Suppression réussie !</h2>";
    echo "<p>La personne et sa photo (si elle existait) ont été supprimées.</p>";
    echo '<p><a href="index.php">← Retour à la liste</a></p>';

} catch (PDOException $e) {
    die("Erreur lors de la suppression : " . $e->getMessage());
}
?>