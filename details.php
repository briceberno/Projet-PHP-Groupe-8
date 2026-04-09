<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("ID invalide.");

$stmt = $pdo->prepare("SELECT * FROM personne WHERE id = ?");
$stmt->execute([$id]);
$personne = $stmt->fetch();

if (!$personne) die("Personne non trouvée.");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte d'Identité - <?= htmlspecialchars($personne['identifiant']) ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .id-card {
            max-width: 850px;
            margin: 40px auto;
            background: white;
            border: 12px solid #c8102e; /* Rouge Burundi */
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.25);
            overflow: hidden;
            position: relative;
        }

        .id-header {
            background: linear-gradient(90deg, #00a650, #c8102e);
            color: white;
            padding: 15px 25px;
            text-align: center;
            font-size: 1.8rem;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .id-body {
            display: flex;
            padding: 25px;
            gap: 30px;
            background: #f8f8f8;
        }

        .left-side {
            flex: 1;
        }

        .right-side {
            width: 240px;
            text-align: center;
        }

        .photo {
            width: 210px;
            height: 260px;
            object-fit: cover;
            border: 6px solid #333;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .info-row {
            display: flex;
            margin-bottom: 12px;
        }

        .info-label {
            width: 160px;
            font-weight: 600;
            color: #c8102e;
        }

        .info-value {
            flex: 1;
            font-weight: 500;
        }

        .footer {
            background: #c8102e;
            color: white;
            padding: 12px 25px;
            display: flex;
            justify-content: space-between;
            font-size: 0.95rem;
        }

        .actions {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 30px;
            margin: 0 10px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="id-card">
    <!-- En-tête -->
    <div class="id-header">
        BURUNDI<br>
        <span style="font-size:1.1rem; letter-spacing:4px;">CARTE D'IDENTITÉ</span>
    </div>

    <div class="id-body">
        <!-- Partie gauche -->
        <div class="left-side">
            <!-- Drapeau + puce -->
            <div class="flag"></div>
            <div class="chip"></div>

            <div class="info-row">
                <div class="info-label">Numéro d’identité :</div>
                <div class="info-value"><strong><?= htmlspecialchars($personne['identifiant']) ?></strong></div>
            </div>

            <div class="info-row">
                <div class="info-label">Nom :</div>
                <div class="info-value"><?= htmlspecialchars($personne['nom']) ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">Prénom :</div>
                <div class="info-value"><?= htmlspecialchars($personne['prenom']) ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">Nom du père :</div>
                <div class="info-value"><?= htmlspecialchars($personne['nom_pere'] ?? 'Non renseigné') ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">Nom de la mère :</div>
                <div class="info-value"><?= htmlspecialchars($personne['nom_mere'] ?? 'Non renseigné') ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">Date de naissance :</div>
                <div class="info-value"><?= htmlspecialchars($personne['date_naissance']) ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">Lieu de naissance :</div>
                <div class="info-value"><?= htmlspecialchars($personne['lieu_naissance']) ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">Genre :</div>
                <div class="info-value"><?= htmlspecialchars($personne['genre']) ?></div>
            </div>

        </div>

        <!-- Partie droite : Photo -->
        <div class="right-side">
            <?php if (!empty($personne['photo_path'])): ?>
                <img src="<?= htmlspecialchars($personne['photo_path']) ?>" class="photo" alt="Photo">
            <?php else: ?>
                <img src="https://via.placeholder.com/210x260?text=Photo" class="photo" alt="Photo">
            <?php endif; ?>
        </div>
    </div>

    <!-- Pied de carte -->
    <div class="footer">
        <div>Principal Général</div>
        <div>Date de délivrance : <?= date('d/m/Y') ?></div>
        <div>Signature du titulaire</div>
    </div>
</div>

<div class="actions">
    <a href="edit.php?id=<?= $personne['id'] ?>" class="btn" style="background:#3498db;color:white;">✏️ Modifier</a>
    <a href="liste.php" class="btn" style="background:#95a5a6;color:white;">← Retour à la liste</a>
</div>

</body>
</html>
