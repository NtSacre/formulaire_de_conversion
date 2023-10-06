<?php
session_start();

// Vérifie si l'historique n'existe pas encore en session
if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

// Vérifie si le formulaire a été soumis
if (isset($_POST['nombre_fcfa']) && is_numeric($_POST['nombre_fcfa'])) {
    $nombre_fcfa = floatval($_POST['nombre_fcfa']);
    $timestamp = time(); // Obtient le timestamp actuel

    // Taux de change pour la conversion en euros
    $exchange_rate_euro = 0.001525;
    // vérification si le nombre est négatif
    if ($nombre_fcfa >= 0) {
        // Conversion en euros
        $euro = round($nombre_fcfa * $exchange_rate_euro, 2);

        // Obtient la date au format 'Y-m-d'
        $date = date('d-m-Y', $timestamp);


        // Stocke l'historique avec le montant en FCFA, le résultat en euros, et la date
        $_SESSION['history'][] = [
            'nombre_fcfa' => $nombre_fcfa,
            'euro' => $euro,
            'date' => $date,

        ];
    } else {
        $erreur = "<p>le montant ne doit pas être négatif</p>";
    }
}

// Supprimer l'historique si le bouton est cliqué
if (isset($_POST['clear_history'])) {
    $_SESSION['history'] = [];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversion FCFA en Euro</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <form action="" method="post" class="form1">
            <h1>Conversion FCFA en Euro</h1>
            <span class="sous-titre">Montant en FCFA:</span>
            <input type="number" name="nombre_fcfa">
            <!--on vérifie si la variable $euro existe lors du redémarrage de la page avant de l'afficher -->

            <?php echo (isset($euro)) ? '<input type="text" readonly  value="' . $euro . '">' : '<input type="text" readonly  value="0">'; ?>

            <!--on vérifie si la variable $erreur existe lors du redémarrage de la page -->

            <?php echo (!empty($erreur)) ? $erreur : ""; ?>
            <input type="submit" value="Convertir" class="btn_nombre_fcfa">
            <?php if (!empty($_SESSION['history'])) : ?>

                <h2>Historique des conversions en Euro</h2>
                <?php
                // Regrouper l'historique par date
                $grouped_history = [];
                foreach ($_SESSION['history'] as $entry) {
                    $date = $entry['date'];
                    $grouped_history[$date][] = $entry;
                }
                ?>
                <?php foreach ($grouped_history as $date => $histories) :  ?>

                    <h3> Historique du <?= $date  ?></h3>

                    <table border="1" cellspacing="0" cellpadding="25">
                        <tr>
                            <th>Montant (FCFA)</th>
                            <th>Résultat en Euro</th>
                            <!-- <th>Heure</th> -->
                        </tr>
                        <?php foreach ($histories as $historie) :  ?>
                            <tr>
                                <td><?php echo $historie['nombre_fcfa']; ?></td>
                                <td><?php echo $historie['euro']; ?> Euro</td>
                                <!-- <td><?php //echo $historie['date']; 
                                            ?></td>-->
                            </tr>
                        <?php endforeach; ?>
                    </table>

                <?php endforeach; ?>
                <div>
                    <input type="submit" name="clear_history" value="Supprimer l'historique" class="btn-reset">
                </div>
            <?php endif; ?>
        </form>
    </div>
</body>

</html>