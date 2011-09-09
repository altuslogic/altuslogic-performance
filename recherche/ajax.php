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
    $tab = recherche($search,$hash,$mode,$methode,$tabCol,$page,$limite,$source,null);
    $result = $tab['resultats'];
    $nombre = $tab['nombre'];
    $print = "";

    if ($visuel=="result"){
        $res = "";
        foreach ($result as $r){     
            $details = $container;
            foreach ($tabCol as $col){
                $txt = decodeUTF($r[strtolower($col)]);                             
                // Remplacement des ~COLONNE~ par la valeur de colonne
                $details = str_replace("~$col~",$txt,$details);
            }
            $res .= str_replace("~RES~",$details,$container_list);
        }

        $nbPages = ceil($tab['nombre']/$limite);
        if ($nbPages>0){

            unset($_GET['search']);
            unset($_GET['source']);
            unset($_GET['page']);
            $param = json_encode($_GET);

            $print_pages = "<p align='center'>Pages: ";
            $firstpage = max($page-5,1);
            $lastpage = min($page+5,$nbPages);
            for ($i=$firstpage; $i<=$lastpage; $i++){
                if ($i==$page) $print_pages .= "<b>$i</b> ";                                   
                else $print_pages .= "<a href='#' onclick='soumettre(1,0,$i,\"champ_$hash\",$param);'>$i</a> ";
            }
            $print_pages .= "</p>";                                
        }                                                              


        $print = str_replace("~TITLE~","Résultats de la recherche : ".decodeUTF($search),$container_all);
        $print = str_replace("~ALL~",$res,$print);
        $print = str_replace("~TIME~","<p>$nombre résultat".($nombre>1?"s":"")." en ".round($tab['temps'],3)." seconde(s).</p>",$print);
        $print = str_replace("~PAGES~",$print_pages,$print);  

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
        $print = strtolower($print);
    }

    echo $print;

?>