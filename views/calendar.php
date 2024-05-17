<?php
// Variables
$days = [1=>'LUN', 2=>'MAR', 3=>'MER', 4=>'JEU', 5=>'VEN', 6=>'SAM', 7=>'DIM'];
$months = [1=>'Janvier', 2=>'Février', 3=>'Mars', 4=>'Avril', 5=>'Mai', 6=>'Juin', 7=>'Juillet', 8=>'Août', 9=>'Septembre', 10=>'Octobre', 11=>'Novembre', 12=>'Décembre'] ;
$actualYear = date('Y'); 
$actualMonth = date('n'); 
$actualDay = date('j'); 
$divCount = 1;
$eventsString = file_get_contents('../events.json');
$eventsList = json_decode($eventsString, true);
$events = $eventsList['evenements'];
$eventsSort = $events;
usort($eventsSort, function($a, $b) {
    $dateA = strtotime($a['date']);
    $dateB = strtotime($b['date']);
    return $dateA - $dateB;
});
$eventDay = false;

// Condition selon choix utilisateur
if (!empty($_GET['month']) && !empty($_GET['year'])) {
    $chosenMonth = $_GET['month'];
    $chosenYear = $_GET['year'];
} else {
    $chosenMonth = $actualMonth;
    $chosenYear = $actualYear;
}

// Création des variables premier jour du mois (chiffre), nombre de jour dans le mois
$firstDay = new DateTime("$chosenYear-$chosenMonth-01");
$firstDayWeek = $firstDay->format('N');
$daysNumberInMonth = cal_days_in_month(CAL_GREGORIAN, $chosenMonth, $chosenYear);

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="./../lib/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="./../public/assets/css/style.css">
    <script src="https://kit.fontawesome.com/e0df362f98.js" crossorigin="anonymous"></script>
    <title>Seeds Circle</title>
</head>
<body class="back-dark">
    <!-- ######################################## Header ######################################## -->
    <header>
        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg back-pale amatic border-bottom border-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="../index.html"><img class="w-50 h-50" src="../public/assets/img/logoo-removebg.png" alt="logo"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse " id="navbarNav">
                    <ul class="navbar-nav ms-auto me-4 text-center w-100 justify-content-around">
                        <li class="nav-item">
                            <a class="nav-link fs-2 text-dark" href="../views/calendar.php">Évènements</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-2 text-dark" href="../views/product_list.html">Liste de produits</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-2 text-dark" href="../views/add_product.html">Créer une annonce</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-2 text-dark" href="../views/profil.html" >Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-2 text-dark" href="#">Se déconnecter</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- FIN NAVBAR -->
    </header>
    <!-- ######################################## Fin Header ######################################## -->

    <!-- ######################################## Main ######################################## -->
    <main>
        <!-- CONTAINER PRINCIPAL -->
        <div class="container-fluid">
            <div class="row justify-content-center">
                <!-- CONTAINER GAUCHE : CALENDRIER ET PROCHAIN EVENT -->
                <div class="col-11 col-lg-7 back-dark p-0 pb-4 ps-lg-3">
                    <!-- FORMULAIRE MOIS / ANNÉE -->
                    <form action="" method="get" class="py-3">
                        <div class="row justify-content-center">
                            <!-- SÉLECTEUR MOIS -->
                            <div class="col-4 col-sm-3 d-flex flex-column">
                                <label for="month" class="text-white">Mois :</label>
                                <select name="month" id="month">
                                    <?php
                                    foreach ($months as $key => $month) {
                                        if ($key == $chosenMonth) {?>
                                            <option value="<?=$key?>" selected><?=$month?></option><?php 
                                        } else {?>
                                            <option value="<?=$key?>"><?=$month?></option><?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- FIN SÉLECTEUR MOIS -->
                            
                            <!-- SÉLECTEUR ANNÉE -->
                            <div class="col-4 col-sm-3 d-flex flex-column">
                                <label for="year" class="text-white">Année :</label>
                                <select name="year" id="year">
                                    <?php
                                    for ($year=$actualYear-10; $year <= $actualYear+10; $year++) {
                                        if ($year == $chosenYear) {?>
                                            <option value="<?=$year?>" selected><?=$year?></option><?php 
                                        } else {?>
                                            <option value="<?=$year?>"><?=$year?></option><?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- FIN SÉLECTEUR ANNÉE -->

                            <!-- BOUTON SUBMIT -->
                            <div class="col-4 col-sm-3 d-flex justify-content-center align-items-center">
                                <button type="submit" class="shadow back-green rounded-3 amatic-bold fs-3 py-1 px-3 border-0">VALIDER</button>
                            </div>
                            <!-- FIN BOUTON SUBMIT -->
                        </div>
                    </form>
                    <!-- FIN FORMULAIRE MOIS / ANNÉE -->

                    <!-- TITRE CALENDRIER -->
                    <div class="row justify-content-center">
                        <div class="col-3 d-flex align-items-center justify-content-end">
                            <a href="calendar.php?<?=($chosenMonth != 1) ? 'month='. $chosenMonth-1 .'&year='. $chosenYear : 'month=12&year='. $chosenYear-1?>"><i class="fa-solid fa-arrow-left fa-xl" style="color: #ffffff;"></i></a>
                        </div>
                        <h2 class="amatic text-white text-center col-5"><?=$months[$chosenMonth]?> <?=$chosenYear?></h2>
                        <div class="col-3 d-flex align-items-center">
                            <a href="calendar.php?<?=($chosenMonth != 12) ? 'month='. $chosenMonth+1 .'&year='. $chosenYear : 'month=1&year='. $chosenYear+1?>"><i class="fa-solid fa-arrow-right fa-xl" style="color: #ffffff;"></i></a>
                        </div>
                    </div>
                    <!-- FIN TITRE CALENDRIER -->

                    <!-- JOURS DE LA SEMAINE -->
                    <div class="d-flex justify-content-center bg-white rounded-top-5 overflow-hidden">
                        <?php
                        foreach ($days as $day) {?>
                            <div class="calendar-case d-flex justify-content-center align-items-center text-center montserrat-bold back-light"><?=$day?></div>
                        <?php }
                        ?>
                    </div>
                    <!-- FIN JOURS DE LA SEMAINE -->

                    <!-- CASES CALENDRIER -->
                    <?php 
                    for ($i=0; $i < 6; $i++) {?>
                        <div class="d-flex justify-content-center bg-white"><?php
                            for ($j=0; $j < 7; $j++) {
                                // Condition pour griser les premiers jours si le mois n'a pas commencé
                                if ($divCount < $firstDayWeek) {?>
                                    <div class="calendar-case d-flex justify-content-center bg-secondary border"></div>
                                    <?php $divCount ++;
                                // Condition pour remplier les case si le mois a commencé
                                } elseif ($divCount <= $daysNumberInMonth + $firstDayWeek - 1) {
                                    $dayCount = $divCount - $firstDayWeek + 1;
                                    $actualDate = new DateTime("$chosenYear-$chosenMonth-$dayCount");
                                    ($j >= 5) ? $styleNb = 'text-green fw-bold' : $styleNb = 'fw-bold' ;
                                    ($actualDate->format('Y-n-j') == date('Y-n-j')) ? $styleNb = 'text-danger fw-bold' : null ;
                                    // Boucle à la création de chaque case pour vérifier si un événement à lieu à cette date
                                    foreach ($events as $key => $event) {
                                        if ($event['date'] == $actualDate->format('Y-m-d')) {
                                            $eventDay = true;
                                            $style = 'text-primary fw-bold event-case';
                                        }
                                    }?>
                                    <div class="calendar-case d-flex flex-column justify-content-center align-items-center text-center border overflow-hidden">
                                        <?php
                                        // Condition si un événement a lieu à cette date
                                        if ($eventDay == true) {
                                            $secondEvent = false;
                                            // Boucle pour vérifier quel(s) événement(s) a/ont lieu à cette date
                                            foreach ($events as $key => $event) {
                                                if ($event['date'] == $actualDate->format('Y-m-d')) {
                                                    (!empty($key)) ? $data = $key : $data = 'zero';
                                                    // Condition pour vérifier si un deuxième événement est inscrit le même jour
                                                    if (!$secondEvent) {?>
                                                        <span class="<?=$styleNb?>"><?=$dayCount?></span><br><span data-id="<?=$data?>" class="<?=$style?>"><?=$event['titre']?></span><?php
                                                        $secondEvent = true;
                                                    } else {?>
                                                        <hr class="w-100 m-0">
                                                        <span data-id="<?=$data?>" class="<?=$style?>"><?=$event['titre']?></span><?php
                                                    }
                                                    ?>
                                                <?php }
                                            }
                                        // Condition si un événement n'a pas lieu à cette date
                                        } else {?>
                                            <span class="<?=$styleNb?>"><?=$dayCount?></span><?php 
                                        }?>
                                    </div><?php 
                                    $eventDay = false;
                                    $data = null;
                                    $divCount ++;
                                // Condition pour griser les derniers jours si le mois est terminé
                                } else {?>
                                    <div class="calendar-case d-flex justify-content-center bg-secondary border"></div>
                                    <?php $divCount ++;
                                }
                            }?>
                        </div><?php 
                    }?>
                    <!-- FIN CASES CALENDRIER -->

                    <!-- PROCHAIN EVENT -->
                    <div class="row g-0 justify-content-center align-items-center flex-column">
                        <!-- PROCHAIN EVENT : TEXTE -->
                        <div class="col-11 amatic text-center text-grey pt-3">
                            <div class="row justify-content-center">
                                <!-- PROCHAIN EVENT : TITRE -->
                                <div class="col-12">
                                    <h2 id="dateEvent"></h2>
                                    <h1 id="titleEvent"></h1>
                                </div>
                                <!-- FIN PROCHAIN EVENT : TITRE -->

                                <!-- PROCHAIN EVENT : DESCRIPTION -->
                                <div id="descriptionDiv" class="col-11 back-light mb-3 rounded-4 d-none">
                                    <div class="row">
                                        <div class="col-12 montserrat text-black">
                                            <p id="cityEvent" class="mt-4 fs-4 text-decoration-underline"></p>
                                            <p id="descriptionEvent" class="mt-4"></p>
                                        </div>
                                        <div class="col-12 pb-3">
                                            <button class="btn back-pale amatic-bold shadow "><a href="../views/event.html" class="text-decoration-none text-reset">EN SAVOIR PLUS</a></button>
                                        </div>
                                    </div>
                                </div>
                                <!-- FIN PROCHAIN EVENT : DESCRIPTION -->
                            </div>
                        </div>
                        <!-- FIN PROCHAIN EVENT : TEXTE -->

                        <!-- PROCHAIN EVENT : IMAGE -->
                        <div id="mapDiv" class="col-11 pt-2 d-flex align-items-center d-none">
                            <div id="map"></div>
                        </div>
                        <!-- FIN PROCHAIN EVENT : IMAGE -->
                    </div>
                    <!-- FIN PROCHAIN EVENT -->
                </div>
                <!-- FIN CONTAINER GAUCHE : CALENDRIER ET PROCHAIN EVENT -->

                <!-- CONTAINER DROIT : 3 EVENTS SUIVANTS -->                
                <div id="left-div" class="container-fluid d-none d-lg-flex flex-column col-5 align-items-center justify-content-around"> <!--DIV2-->
                    <h2 class="amatic text-grey">Les prochains évènements</h2>
                    <?php
                    foreach ($eventsSort as $key => $event) {
                        if ($event['date'] >= date('Y-m-d')) {
                            $date1 = new DateTime($event['date']);
                            $date2 = new DateTime($eventsSort[$key+1]['date']);
                            $date3 = new DateTime($eventsSort[$key+2]['date']);                  
                            ?>
                            <!-- PREMIER EVENT -->
                            <div class="row w-100">
                                <div class="col-12 back-pale container-fluid pb-4 rounded-4">
                                    <h2 class="montserrat pt-3 text-center text-decoration-underline"><?=$event['titre']?></h2>
                                    <h3 class="montserrat text-center fs-4"><?=$date1->format('j').' '.$months[$date1->format('n')].' '.$date1->format('Y')?></h3>
                                    <h3 class="montserrat text-center fs-4"><?=$event['ville']?></h3>
                                    <div class="row d-flex align-items-center justify-content-center">
                                        <div class="col-10 text-center">
                                            <p><?=$event['description']?></p>
                                            <button class="btn back-dark amatic-bold text-grey shadow"><a href="../views/event.html" class="text-decoration-none text-reset">EN SAVOIR PLUS</a></button>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                            <!-- FIN PREMIER EVENT -->
                        
                            <!-- DEUXIEME EVENT -->
                            <div class="row w-100 py-3">
                                <div class="col-12 back-pale container-fluid pb-4 rounded-4">
                                    <h2 class="montserrat pt-3 text-center text-decoration-underline"><?=$eventsSort[$key+1]['titre']?></h2>
                                    <h3 class="montserrat text-center fs-4"><?=$date2->format('j').' '.$months[$date2->format('n')].' '.$date2->format('Y')?></h3>
                                    <h3 class="montserrat text-center fs-4"><?=$eventsSort[$key+1]['ville']?></h3>
                                    <div class="row d-flex align-items-center justify-content-center">
                                        <div class="col-10 text-center">
                                            <p><?=$eventsSort[$key+1]['description']?></p>
                                            <button class="btn back-dark amatic-bold text-grey shadow"><a href="../views/event.html" class="text-decoration-none text-reset">EN SAVOIR PLUS</a></button>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                            <!-- FIN DEUXIEME EVENT -->
                        
                            <!-- TROISIEME EVENT -->
                            <div class="row w-100 pb-3">
                                <div class="col-12 back-pale container-fluid pb-4 rounded-4">
                                    <h2 class="montserrat pt-3 text-center text-decoration-underline"><?=$eventsSort[$key+2]['titre']?></h2>
                                    <h3 class="montserrat text-center fs-4"><?=$eventsSort[$key+2]['date']?></h3>
                                    <h3 class="montserrat text-center fs-4"><?=$eventsSort[$key+2]['ville']?></h3>
                                    <div class="row d-flex align-items-center justify-content-center">
                                        <div class="col-10 text-center">
                                            <p><?=$eventsSort[$key+2]['description']?></p>
                                            <button class="btn back-dark amatic-bold text-grey shadow"><a href="../views/event.html" class="text-decoration-none text-reset">EN SAVOIR PLUS</a></button>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                            <!-- FIN TROISIEME EVENT --><?php
                            break;
                        }
                    }?>
                </div>
                <!-- FIN CONTAINER DROIT : 3 EVENTS SUIVANTS -->                
            </div>
        </div>
        <!-- FIN CONTAINER PRINCIPAL -->
    </main>
    <!-- ######################################## Fin Main ######################################## -->

    <!-- ######################################## Footer ######################################## -->
    <footer>
        <div class="container-fluid back-pale amatic">
            <div class="row">
                <div class="col-2">
                    <div class="row">
                        <div class="col-4">
                            <a class="nav-link fs-2 text-dark" data-bs-toggle="modal" data-bs-target="#exampleModal" href="#">Contact</a>
                        </div>
                        <!-- ######################################## MODAL ######################################## -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                            <div class="modal-content montserrat back-pale">
                                <div class="modal-header">
                                <h1 class="modal-title fw-bold fs-5" id="exampleModalLabel">Contact</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                <p class="fw-semibold">LaManuEcology</p>
                                <p>238 Earls Ct Rd, London SW 9AA</p>
                                <p>+44 20 7123 4567</p>
                                <p>contact@lamanuecology.com</p>
                                </div>
                                <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                            </div>
                        </div>
                        <!-- ######################################## MODAL END ######################################## -->
                    </div>
                </div>
                <div class="col-8 text-center">
                    <div class="row">
                        <div class="col-12">
                            <a href="#"><img src="../public/assets/img/logoo-removebg.png" alt="Logo" width="70" height="80"></a>
                        </div>
                        <div class="col-12 d-flex">
                            <div class="col-4">
                                <hr class="decorative-line">
                            </div>
                            <div class="col-4">
                                <a class="nav-link fs-2 text-dark" href="./views/form.html">Rejoignez-nous</a>
                            </div>
                            <div class="col-4">
                                <hr class="decorative-line">
                            </div>
                        </div>
                        <div class="col-12">
                            <!-- Icônes des réseaux sociaux -->
                            <a href="https://www.facebook.com/LaManuFormation/?locale=fr_FR" class="text-decoration-none text-reset" target="_blank"><i class="fab fa-facebook-f fa-2x me-5"></i></a>
                            <a href="https://www.instagram.com/lamanuformation/?hl=fr" class="text-decoration-none text-reset" target="_blank"><i class="fab fa-instagram fa-2x me-5"></i></a>
                            <a href="https://twitter.com/lamanuformation?lang=fr" class="text-decoration-none text-reset" target="_blank"><i class="fa-brands fa-x-twitter fa-2x"></i></a>
                            <p class="montserrat">Copyright © 2024 - Tous droits réservés / Mentions légales</p>
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="row">
                        <div class="col-12 align-items-end d-flex flex-column">
                            <a class="nav-link fs-2 text-dark" href="./index.html">Accueil</a>
                            <a class="nav-link fs-2 text-dark" href="#">FAQ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- ######################################## Fin Footer ######################################## -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="../lib/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="../public/assets/js/calendar.js"></script>
</body>
</html>