<?php

    header("Content-type: text/html; charset=ISO-8859-1");
    $search = $_GET['search']; 
    $nomBase = $_GET['base'];
    $nomTable = $_GET['table'];
    $nomColonne = $_GET['colonne'];                        
    $mode = $_GET['mode'];
    $methode = $_GET['methode'];
    $limite = $_GET['limite'];  
    $ordreMax=3; // à changer : recherche dans stats
              
    include "../proto2/config/db.php";
    include "../proto2/time_function.php";
    include "../proto2/controller.php";                 
                  
    $tab = recherche($search,$mode,$methode,$limite,null); 
    $result = $tab['resultats'];                                         
    for ($i=0; $i<sizeof($result); $i++){
        echo $result[$i][$nomColonne],"|";
    }
    echo $tab['temps'];                    

?>
