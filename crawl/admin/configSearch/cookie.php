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

    $nomBase = readCookie($_GET,"nomBase","");    
    $nomTable = readCookie($_GET,"nomTable","");
    $nomColonne = readCookie($_GET,"nomColonne","");
    $mode = readCookie($_POST,"mode","milieu");     
    $methode = readCookie($_POST,"methode","direct");
    $visuel = readCookie($_POST,"visuel","result");
    $resume = readCookie($_POST,"resume",1);
    $limite = readCookie($_POST,"limite",10);
    $nomDiv = readCookie($_POST,"nomDiv","ajax");
    $afficheDiv = readCookie($_POST,"afficheDiv",1);  
    $containerAll = readCookie($_POST,"containerAll","<b>~TITLE~</b><ul>~ALL~</ul>~TIME~"); 
    $containerResult = readCookie($_POST,"containerResult","<li>~RES~</li>");
    $containerDetails = readCookie($_POST,"containerDetails","");  
    $f = readCookie($_GET,"f",2); 
    $type = readCookie($_GET,"type",""); 
    $thres = readCookie($_GET,"threshold",5000);
    $ordreMax = readCookie($_GET,"ordre",3);

?>
