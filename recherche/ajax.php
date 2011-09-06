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
    $tabDetails = explode("~",$container);
    $tabCol = array(strtoupper($nomColonne));
    for ($i=1; $i<sizeof($tabDetails); $i+=2){
        array_push($tabCol,$tabDetails[$i]);
    }
    $tabCol = array_unique($tabCol);
    
    // Recherche proprement dite
    $search = str_replace("~plus~","+",$search);
    $tab = recherche($search,$hash,$mode,$methode,$tabCol,$limite,null);
    $result = $tab['resultats'];

    if ($visuel=="result"){
        $res = "";
        if (sizeof($result)==0) $res = str_replace("~RES~","Pas de résultats.",$container_list);
        else {
            foreach ($result as $r){
                $details = $container;
                foreach ($tabCol as $col){
                    // Remplacement des ~COLONNE~ par la valeur de colonne
                    $details = str_replace("~".$col."~",$r[strtolower($col)],$details);
                }
                $res .= str_replace("~RES~",$details,$container_list);
            }
        }                                                               

        $print = str_replace("~TITLE~","Résultats de la recherche : ".$search,$container_all);
        $print = str_replace("~ALL~",$res,$print);
        $print = str_replace("~TIME~","Temps écoulé : ".$tab['temps']." seconde(s)",$print);
        echo $print;                                             
    }

    else {
        for ($i=0; $i<sizeof($result); $i++){
            $suggest = $container;
            for ($j=0; $j<sizeof($tabCol); $j++){
                // Remplacement des ~COLONNE~ par la valeur de colonne  
                $suggest = str_replace("~".$tabCol[$j]."~",$result[$i][strtolower($tabCol[$j])],$suggest);
            }
            echo $suggest,"|";
        }
    }

?>
