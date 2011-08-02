<?php

    function saveCookie($token,$value){
        setcookie($token,$value);
    }

    function readCookie($token,$default){
        if (!$_GET[$token]){
            $value = $_COOKIE[$token]; 
            if (!$value) $value = $default; 
        }
        else {
            saveCookie($token,$_GET[$token]);
            $value = $_GET[$token];
        }
        return $value;
    }

    $nomBase = readCookie("nomBase","business_10k");
    $nomColonne = readCookie("nomColonne","name");
    $thres = readCookie("threshold",5000);
    $ordreMax = readCookie("ordre",3);

?>
