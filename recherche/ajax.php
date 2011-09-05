<?php

    header("Content-type: text/html; charset=ISO-8859-1");
    foreach ($_GET as $key=>$val){
        $$key = $val;
    }

    $nomMaitre = $nomBase;
    include "config/db.inc.php";
    include "config/config.inc.php";
    include "search_funcs.php";                  
    
    // Détermination des colonnes nécessaires
    $tabDetails = explode("~",$visuel=='result'?$containerDetails:$containerSuggest);
    $tabCol = array($nomColonne);
    for ($i=1; $i<sizeof($tabDetails); $i+=2){
        array_push($tabCol,$tabDetails[$i]);
    }
    if ($methode=="mot" || $methode=="phrase") array_push($tabCol,"nombre");
    $tabCol = array_unique($tabCol);                     
    $selecCol = strtolower(implode(", ",$tabCol));

    // Recherche proprement dite
    $tab = recherche($search,$hash,$mode,$methode,$selecCol,$limite,null);
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
            $suggest = $containerSuggest;
            for ($j=0; $j<sizeof($tabCol); $j++){
                // Remplacement des ~COLONNE~ par la valeur de colonne  
                $suggest = str_replace("~".$tabCol[$j]."~",$result[$i][strtolower($tabCol[$j])],$suggest);
            }
            echo $suggest,"|";
        }
    }

?>
