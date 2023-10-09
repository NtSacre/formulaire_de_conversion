<?php
session_start();

// Réinitialiser tout l'historique
if (isset($_POST['reset_historique'])) {
    unset($_SESSION['historique']);
    header("Location: autre_formulaire.php");
    exit;
}

// Supprimer une entrée spécifique
if (isset($_POST['supprimer_entree'])) {
    $indice_entree_a_supprimer = $_POST['indice_entree'];
    if (isset($_SESSION['historique'][$indice_entree_a_supprimer])) {
        unset($_SESSION['historique'][$indice_entree_a_supprimer]);
        $_SESSION['historique'] = array_values($_SESSION['historique']); // Réindexer le tableau
    }
    header("Location: autre_formulaire.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Formulaire de test COVID-19</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="formulaire">
            <h2>Formulaire de test COVID-19</h2>
            <hr />
            <!-- Votre formulaire ici -->
            <form method="POST" action="traitement_formulaire.php">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required /><br />

                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" required /><br />

                <label for="temperature">Température (en °C) :</label>
                <input type="number" id="temperature" name="temperature" required /><br />

                <label for="tranche_age">Tranche d'âge :</label>
                <input type="radio" id="tranche_age_1" name="tranche_age" value="2-10" />2 - 10 ans
                <input type="radio" id="tranche_age_2" name="tranche_age" value="19-30" />19 - 30 ans
                <input type="radio" id="tranche_age_3" name="tranche_age" value="45-100" />45 - 100 ans<br />

                <label for="maux_de_tete">Avez-vous des maux de tête ?</label>
                <input type="radio" id="maux_de_tete_oui" name="maux_de_tete" value="oui" />Oui
                <input type="radio" id="maux_de_tete_non" name="maux_de_tete" value="non" />Non<br />

                <label for="diarrhee">Avez-vous de la diarrhée ?</label>
                <input type="radio" id="diarrhee_oui" name="diarrhee" value="oui" />Oui
                <input type="radio" id="diarrhee_non" name="diarrhee" value="non" />Non<br />

                <label for="toux">Avez-vous de la toux ?</label>
                <input type="radio" id="toux_oui" name="toux" value="oui" />Oui
                <input type="radio" id="toux_non" name="toux" value="non" />Non<br />

                <label for="perte_odorat">Avez-vous perdu l'odorat ?</label>
                <input type="radio" id="perte_odorat_oui" name="perte_odorat" value="oui" />Oui
                <input type="radio" id="perte_odorat_non" name="perte_odorat" value="non" />Non<br />

                <input type="submit" name="submit" value="Envoyer" />
            </form>
        </div>
        <div class="resultat">
            <h2>Historique des données</h2>

            <?php if (isset($_SESSION['historique']) && count($_SESSION['historique']) > 0) : ?>
                <form method="POST" action="">
                    <table border="1">
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Température</th>
                            <th>Tranche d'âge</th>
                            <th>Maux de tête</th>
                            <th>Diarrhée</th>
                            <th>Toux</th>
                            <th>Perte d'odorat</th>
                            <th>Points</th>
                            <th>Action</th>
                        </tr>
                        <!-- Les données de l'historique seront générées dynamiquement ici -->
                        <?php foreach ($_SESSION['historique'] as $index => $donnees) : ?>
                            <tr>
                                <td><?php echo $donnees['nom']; ?></td>
                                <td><?php echo $donnees['prenom']; ?></td>
                                <td><?php echo $donnees['temperature']; ?></td>
                                <td><?php echo $donnees['tranche_age']; ?></td>
                                <td><?php echo ($donnees['maux_de_tete']) ? 'Oui' : 'Non'; ?></td>
                                <td><?php echo ($donnees['diarrhee']) ? 'Oui' : 'Non'; ?></td>
                                <td><?php echo ($donnees['toux']) ? 'Oui' : 'Non'; ?></td>
                                <td><?php echo ($donnees['perte_odorat']) ? 'Oui' : 'Non'; ?></td>
                                <td><?php echo $donnees['points']; ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="indice_entree" value="<?php echo $index; ?>">
                                        <input type="submit" name="supprimer_entree" value="Supprimer">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <br>
                    <form method="POST" action="">
                        <input type="submit" name="reset_historique" value="Réinitialiser l'historique">
                    </form>
                </form>
            <?php else : ?>
                <p>Aucun enregistrement dans l'historique.</p>
            <?php endif; ?>
        </div>

        <!-- Pop-up pour les différents statuts -->
        <div id="popup-sain" class="popup">
            <p> 🥵🥵ouf Vous êtes sain et sauf.</p>
        </div>
        <div id="popup-susceptible" class="popup">
            <p>😒😒Hum toi là on dirait que tu es susceptible d'avoir le covid.</p>
        </div>
        <div id="popup-critique" class="popup">
            <p>😱😱Votre état est critique allez vous faire soigner.</p>
        </div>
        <?php
        $points = 0; // Initialisation de la variable des points
        if (isset($_SESSION['historique']) && count($_SESSION['historique']) > 0) {
            $fin_tableau[] = end($_SESSION['historique']);

            foreach ($fin_tableau as $donnees) {
                $points += $donnees['points']; // Ajoutez les points de chaque entrée à la variable $points
            }
        }
        ?>
        <script>
            // JavaScript pour afficher les pop-ups en fonction du résultat
            // Cette logique peut être adaptée à votre script de calcul

            // Obtenez le résultat de votre calcul (à remplacer)
            const resultat = <?php echo $points; ?>; // Exemple

            if (resultat >= 80 && resultat <= 100) {
                // Statut critique
                document.getElementById("popup-critique").style.display = "block";
            } else if (resultat >= 50 && resultat < 80) {
                // Statut susceptible
                document.getElementById("popup-susceptible").style.display = "block";
            } else if (resultat > 0 && resultat < 50) {
                // Statut sain et sauf
                document.getElementById("popup-sain").style.display = "block";
            }
        </script>
</body>

</html>