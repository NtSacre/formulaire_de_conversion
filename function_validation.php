<?php
session_start();

$erreur = ""; // Initialisation de la variable d'erreur

// Initialisation des variables pour conserver les valeurs saisies par l'utilisateur
$nom = "";
$prenom = "";
$temperature = "";
$poids = "";
$tranche_age = "";
$maux_de_tete = false;
$diarrhee = false;
$toux = false;
$perte_odorat = false;

function is_alpha_string($str)
{
    // Vérifie si la chaîne contient uniquement des lettres (majuscules ou minuscules)
    return ctype_alpha($str);
}

function is_positive_number($num)
{
    // Vérifie si le nombre est positif ou nul
    return is_numeric($num) && $num >= 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification des champs du formulaire
    if (empty($_POST["nom"]) || empty($_POST["prenom"]) || empty($_POST["temperature"]) || empty($_POST["poids"]) || empty($_POST["tranche_age"]) || !isset($_POST["maux_de_tete"]) || !isset($_POST["diarrhee"]) || !isset($_POST["toux"]) || !isset($_POST["perte_odorat"])) {
        $erreur = "<p class='erreur'>Tous les champs du formulaire doivent être remplis.</p>";
    } else {
        $nom = $_POST["nom"];
        $prenom = $_POST["prenom"];
        $temperature = floatval($_POST["temperature"]);
        $poids = floatval($_POST["poids"]);
        $tranche_age = $_POST["tranche_age"];
        $maux_de_tete = ($_POST["maux_de_tete"] == "oui");
        $diarrhee = ($_POST["diarrhee"] == "oui");
        $toux = ($_POST["toux"] == "oui");
        $perte_odorat = ($_POST["perte_odorat"] == "oui");

        // Validation personnalisée pour les champs texte (nom et prénom)
        if (!is_alpha_string($nom) || !is_alpha_string($prenom)) {
            $erreur = "<p class='erreur'>Le nom et le prénom doivent contenir uniquement des lettres.</p>";
        } elseif (strlen($nom) > 25 || strlen($prenom) > 50) {
            $erreur = "<p class='erreur'>Le nom doit avoir une longueur maximale de 25 caractères et le prénom de 50 caractères.</p>";
        } elseif ($poids < 20 || $poids > 210 || $temperature < 30 || $temperature > 45) {
            $erreur = "<p class='erreur'>Le poids doit être entre 20 et 210 kg, et la température entre 30 et 45 °C.</p>";
        } else {
            // Calcul des points en fonction des réponses
            $points = 0;
            if ($temperature > 37) {
                $points += 10;
            }
            if ($maux_de_tete) {
                $points += 20;
            }
            if ($diarrhee) {
                $points += 20;
            }
            if ($toux) {
                $points += 20;
            }
            if ($perte_odorat) {
                $points += 20;
            }
            if ($tranche_age == "2-10") {
                $points += 10;
            } elseif ($tranche_age == "19-30") {
                $points += 5;
            } elseif ($tranche_age == "45-100") {
                $points += 10;
            }

            // Enregistrement des données dans l'historique de la session
            $historique = isset($_SESSION["historique"]) ? $_SESSION["historique"] : array();
            $historique[] = [
                "nom" => $nom,
                "prenom" => $prenom,
                "temperature" => $temperature,
                "poids" => $poids,
                "tranche_age" => $tranche_age,
                "maux_de_tete" => $maux_de_tete,
                "diarrhee" => $diarrhee,
                "toux" => $toux,
                "perte_odorat" => $perte_odorat,
                "points" => $points,
            ];
            $_SESSION["historique"] = $historique;

            // Redirection vers la même page après soumission
            header("Location: function_validation.php");
            exit;
        }
    }
}
// Réinitialiser tout l'historique
if (isset($_POST['reset_historique'])) {
    unset($_SESSION['historique']);
    header("Location: function_validation.php");
    exit;
}

// Supprimer une entrée spécifique
if (isset($_POST['supprimer_entree'])) {
    $indice_entree_a_supprimer = $_POST['indice_entree'];
    if (isset($_SESSION['historique'][$indice_entree_a_supprimer])) {
        unset($_SESSION['historique'][$indice_entree_a_supprimer]);
        $_SESSION['historique'] = array_values($_SESSION['historique']); // Réindexer le tableau
    }
    header("Location: function_validation.php");
    exit;
}
// Le reste du code HTML
?>
<!DOCTYPE html>
<html lang="fr">

<head>

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Formulaire de test COVID-19</title>
        <link rel="stylesheet" href="styles.css">
    </head>
</head>

<body>
    <div class="container">
        <div class="formulaire">
            <h2>Formulaire de test COVID-19</h2>
            <hr />
            <!-- Votre formulaire ici -->
            <form method="POST" action="function_validation.php">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" value="<?php echo $nom; ?>" /><br />

                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo $prenom; ?>" /><br />

                <label for="poids">Poids (en Kg) :</label>
                <input type="number" id="poids" name="poids" value="<?php echo $poids; ?>" /><br />

                <label for="temperature">Température (en °C) :</label>
                <input type="number" id="temperature" name="temperature" value="<?php echo $temperature; ?>" /><br />
                <label for="tranche_age">Tranche d'âge :</label>
                <input type="radio" id="tranche_age_1" name="tranche_age" value="2-10" />2 - 10 ans
                <input type="radio" id="tranche_age_2" name="tranche_age" value="19-30" />19 - 30 ans
                <input type="radio" id="tranche_age_3" name="tranche_age" value="45-100" />45 - 100 ans<br />

                <label for="maux_de_tete">Avez-vous des maux de tête ?</label>
                <input type="radio" id="maux_de_tete_oui" name="maux_de_tete" value="oui" <?php if ($maux_de_tete) echo "checked"; ?> />Oui
                <input type="radio" id="maux_de_tete_non" name="maux_de_tete" value="non" <?php if (!$maux_de_tete) echo "checked"; ?> />Non<br />

                <label for="diarrhee">Avez-vous de la diarrhée ?</label>
                <input type="radio" id="diarrhee_oui" name="diarrhee" value="oui" <?php if ($diarrhee) echo "checked"; ?> />Oui
                <input type="radio" id="diarrhee_non" name="diarrhee" value="non" <?php if (!$diarrhee) echo "checked"; ?> />Non<br />

                <label for="toux">Avez-vous de la toux ?</label>
                <input type="radio" id="toux_oui" name="toux" value="oui" <?php if ($toux) echo "checked"; ?> />Oui
                <input type="radio" id="toux_non" name="toux" value="non" <?php if (!$toux) echo "checked"; ?> />Non<br />

                <label for="perte_odorat">Avez-vous perdu l'odorat ?</label>
                <input type="radio" id="perte_odorat_oui" name="perte_odorat" value="oui" <?php if ($perte_odorat) echo "checked"; ?> />Oui
                <input type="radio" id="perte_odorat_non" name="perte_odorat" value="non" <?php if (!$perte_odorat) echo "checked"; ?> />Non<br />

                <!-- ... Autres champs du formulaire ... -->

                <input type="submit" name="submit" value="Envoyer" />
            </form>
            <?php echo (isset($erreur)) ? $erreur : ""; ?>
        </div>
        <div class="resultat">
            <h2>Liste des Historiques des données</h2>

            <!-- Affichage de l'historique ici -->
            <?php if (isset($_SESSION['historique']) && count($_SESSION['historique']) > 0) : ?>
                <table border="1">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Température</th>
                        <th>Poids</th>

                        <th>Tranche d'âge</th>
                        <th>Maux de tête</th>
                        <th>Diarrhée</th>
                        <th>Toux</th>
                        <th>Perte d'odorat</th>
                        <th>Points</th>
                        <th>Action</th>
                        <!-- ... Autres en-têtes de colonnes ... -->
                    </tr>
                    <?php foreach ($_SESSION['historique'] as $key => $valeur) : ?>
                        <tr>
                            <td><?php echo $valeur['nom']; ?></td>
                            <td><?php echo $valeur['prenom']; ?></td>
                            <td><?php echo $valeur['temperature']; ?></td>

                            <td><?php echo $valeur['poids']; ?></td>

                            <td><?php echo $valeur['tranche_age']; ?></td>
                            <td><?php echo ($valeur['maux_de_tete']) ? 'Oui' : 'Non'; ?></td>
                            <td><?php echo ($valeur['diarrhee']) ? 'Oui' : 'Non'; ?></td>
                            <td><?php echo ($valeur['toux']) ? 'Oui' : 'Non'; ?></td>
                            <td><?php echo ($valeur['perte_odorat']) ? 'Oui' : 'Non'; ?></td>
                            <td><?php echo $valeur['points']; ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="indice_entree" value="<?php echo $key; ?>">
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
        $fin_tableau = end($_SESSION['historique']);

        $points += $fin_tableau['points']; // Ajoutez les points de la dernière entrée à la variable $points
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