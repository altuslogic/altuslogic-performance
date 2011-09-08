<?php

    header("Content-type: text/html; charset=ISO-8859-1");
    foreach ($_GET as $key=>$val){
        $$key = $val;
    }

    $nomMaitre = $nomBase;
    include "config/db.inc.php";
    include "config/config.inc.php";
    include "search_funcs.php";                  
    
    // D�termination des colonnes n�cessaires
    $tabDetails = explode("~",$container);
    $tabCol = array(strtoupper($nomColonne));
    for ($i=1; $i<sizeof($tabDetails); $i+=2){
        array_push($tabCol,$tabDetails[$i]);
    }
    $tabCol = array_unique($tabCol);

    // Recherche proprement dite
    $search = str_replace("~plus~","+",$search);
    $tab = recherche($search,$hash,$mode,$methode,$tabCol,$limite,$auto,null);
    $result = $tab['resultats'];
    $print = "";

    if ($visuel=="result"){
        $res = "";
        if (sizeof($result)==0) $res = str_replace("~RES~","Pas de r�sultats.",$container_list);
        else {
            foreach ($result as $r){     
                $details = $container;
                foreach ($tabCol as $col){
                    $txt = decodeUTF($r[strtolower($col)]);                             
                    // Remplacement des ~COLONNE~ par la valeur de colonne
                    $details = str_replace("~$col~",$txt,$details);
                }
                $res .= str_replace("~RES~",$details,$container_list);
            }
        }                                                               

        $print = str_replace("~TITLE~","R�sultats de la recherche : ".decodeUTF($search),$container_all);
        $print = str_replace("~ALL~",$res,$print);
        $print = str_replace("~TIME~","Temps �coul� : ".$tab['temps']." seconde(s)",$print);                                             
    }

    else {
        foreach ($result as $r){                                         
            $suggest = $container;
            foreach ($tabCol as $col){
                $txt = decodeUTF($r[strtolower($col)]); 
                // Remplacement des ~COLONNE~ par la valeur de colonne  
                $suggest = str_replace("~$col~",$txt,$suggest);
            }
            $print .= $suggest."|";
        }
    }

    echo $print;

?>
