<?php

    function saveCookie($token,$value){
        setcookie($token,$value);
    }

    function readCookie($methode,$token,$default){
        if (!isset($methode[$token])){
            if (isset($_COOKIE[$token])){
                $value = $_COOKIE[$token]; 
            }
            else {
                $value = $default;
            } 
        }
        else {
            saveCookie($token,$methode[$token]);
            $value = $methode[$token];
        }
        return $value;
    }      

    // Choix des données
    $nomProjet = readCookie($_GET,"nomProjet","global");
    $nomBase = readCookie($_GET,"nomBase","");    
    $nomTable = readCookie($_GET,"nomTable","");
    $nomColonne = readCookie($_GET,"nomColonne","");
    
    // Paramètres sous-tables 
    $thres = readCookie($_GET,"threshold",5000);
    $ordreMax = readCookie($_GET,"ordre",3);
    
    // Choix de l'action
    $f = readCookie($_GET,"f",2); 
    $type = readCookie($_GET,"type","");
    
    // Paramètres de l'extraction
    $site = readCookie($_POST,"site","");
    $in = readCookie($_POST,"in","");
    $out = readCookie($_POST,"out","");
    $column = readCookie($_POST,"column","partialtxt");
    $tag_name = readCookie($_POST,"tag_name","");
    $attrib_name = readCookie($_POST,"attrib_name","");
    $attrib_value = readCookie($_POST,"attrib_value","");
    $attrib_mode = readCookie($_POST,"attrib_mode","exact");
    $start_text = readCookie($_POST,"start_text","");
    $end_text = readCookie($_POST,"end_text","");

?>
