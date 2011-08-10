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
    $ordreMax=3; // à changer : recherche dans stats

    include "../proto2/config/db.php";
    include "../proto2/time_function.php";
    include "../proto2/controller.php";                 

    $tab = recherche($search,$mode,$methode,$limite,null); 
    $result = $tab['resultats'];


    if ($visuel=="result"){                                              
        $res = ""; 
        if (sizeof($result)==0) $res = str_replace("~RES~","Pas de résultats.",$containerResult);
        else {
            for ($i=0; $i<sizeof($result); $i++){
                //if ($containerDetails!=""){
                //    $sql = "SELECT * FROM ".$nomTable." WHERE id='$result[$i][id]' LIMIT 1";
                //}
                $res .= str_replace("~RES~",$result[$i][$nomColonne],$containerResult);  
            }
        }                                                               

        $print = str_replace("~TITLE~","Résultats de la recherche : ".$search,$containerAll);
        $print = str_replace("~ALL~",$res,$print);
        $print = str_replace("~TIME~","Temps écoulé : ".$tab['temps']." seconde(s)",$print);
        echo $print;                                             
    }

    else {
        for ($i=0; $i<sizeof($result); $i++){
            echo $result[$i][$nomColonne],"|";
        }
    }                                     

?>
