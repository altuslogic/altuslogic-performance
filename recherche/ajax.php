<?php

    header("Content-type: text/html; charset=ISO-8859-1");
    $search = $_GET['search']; 
    $nomBase = $_GET['base'];
    $nomTable = $_GET['table'];
    $nomColonne = $_GET['colonne'];                        
    $mode = $_GET['mode'];
    $methode = $_GET['methode'];
    $visuel = $_GET['visuel']; 
    $limite = $_GET['limite'];
    $containerAll = $_GET['containerAll'];
    $containerResult = $_GET['containerResult']; 
    $containerDetails = $_GET['containerDetails']; 
    $ordreMax=3; // à changer : recherche dans stats

    $nomMaitre = $nomBase;
    include "../proto2/config/db.php";
    include "../proto2/config/config.php";
    include "../proto2/time_function.php";
    include "../proto2/controller.php";                 

    // Détermination des colonnes nécessaires
    $tabDetails = explode("~",$containerDetails);
    $tabCol = array($nomColonne);
    for ($i=1; $i<sizeof($tabDetails); $i+=2){
        array_push($tabCol,$tabDetails[$i]);
    }
    $tabCol = array_unique($tabCol);                     
    $selecCol = strtolower(implode(", ",$tabCol));

    // Recherche proprement dite
    $tab = recherche($search,$mode,$methode,$selecCol,$limite,null); 
    $result = $tab['resultats'];

    if ($visuel=="result"){                                              
        $res = ""; 
        if (sizeof($result)==0) $res = str_replace("~RES~","Pas de résultats.",$containerResult);
        else {
            for ($i=0; $i<sizeof($result); $i++){         
                $details = $containerDetails;
                for ($j=0; $j<sizeof($tabCol); $j++){
                    // Remplacement des ~COLONNE~ par la valeur de colonne
                    $details = str_replace("~".$tabCol[$j]."~",$result[$i][strtolower($tabCol[$j])],$details);
                }  
                $res .= str_replace("~RES~",$details,$containerResult);  
            }
        }                                                               

        $print = str_replace("~TITLE~","Résultats de la recherche : ".$search,$containerAll);
        $print = str_replace("~ALL~",$res,$print);
        $print = str_replace("~TIME~","Temps écoulé : ".$tab['temps']." seconde(s)",$print);
        echo $print;                                             
    }

    else {
        for ($i=0; $i<sizeof($result); $i++){
            $details = $containerDetails;
            for ($j=0; $j<sizeof($tabCol); $j++){
                // Remplacement des ~COLONNE~ par la valeur de colonne  
                $details = str_replace("~".$tabCol[$j]."~",$result[$i][strtolower($tabCol[$j])],$details);
            } 
            echo $details,"|";
        }
    }                                     

?>
