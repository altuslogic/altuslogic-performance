<?php

    function saveCookie($token,$value){
        setcookie($token,$value);
    }

    function readCookie($token,$default){
        if (!isset($_GET[$token])){
            if (isset($_COOKIE[$token])){
                $value = $_COOKIE[$token]; 
            }
            else{
                $value = $default;
            } 
        }
        else {
            saveCookie($token,$_GET[$token]);
            $value = $_GET[$token];
        }
        return $value;
    }     

    $nomBase = readCookie("nomBase","");     
    $nomTable = readCookie("nomTable","");
    $nomColonne = readCookie("nomColonne","name");
    $mode = readCookie("mode","contient");     
    $methode = readCookie("methode","tout");
    $visuel = readCookie("visuel","result");
    $thres = readCookie("threshold",5000);
    $ordreMax = readCookie("ordre",3);

?>
