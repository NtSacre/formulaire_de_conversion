<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $temperature = floatval($_POST['temperature']);
    $tranche_age = $_POST['tranche_age'];
    $maux_de_tete = ($_POST['maux_de_tete'] == "oui") ? true : false;
    $diarrhee = ($_POST['diarrhee'] == "oui") ? true : false;
    $toux = ($_POST['toux'] == "oui") ? true : false;
    $perte_odorat = ($_POST['perte_odorat'] == "oui") ? true : false;

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
    $historique = isset($_SESSION['historique']) ? $_SESSION['historique'] : array();
    $historique[] = [
        'nom' => $nom,
        'prenom' => $prenom,
        'temperature' => $temperature,
        'tranche_age' => $tranche_age,
        'maux_de_tete' => $maux_de_tete,
        'diarrhee' => $diarrhee,
        'toux' => $toux,
        'perte_odorat' => $perte_odorat,
        'points' => $points
    ];
    $_SESSION['historique'] = $historique;

    // Affichage des résultats
    header("Location: autre_formulaire.php");
    exit;
}
