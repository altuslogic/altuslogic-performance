<?php

    header("Content-type: text/html; charset=ISO-8859-1");
    $search = $_GET['search']; 
    $nomBase = $_GET['base'];
    $nomTable = $_GET['table'];
    $nomColonne = $_GET['colonne'];                        
    $mode = $_GET['mode'];
    $methode = $_GET['methode'];
    $visuel = $_GET['visuel'];
    $ordreMax=3;
    
    include "../proto2/config/db.php";
    include "../proto2/time_function.php";
    include "../proto2/controller.php";                 
                                             
    $tab = recherche($search,$mode,$methode,$visuel,null); 
    $result = $tab['resultats'];                                         
    for ($i=0; $i<sizeof($result); $i++){
        echo $result[$i][$nomColonne],"|";
    }
    echo $tab['temps'];                    

?>
