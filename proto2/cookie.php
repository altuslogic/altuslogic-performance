<?php

    function saveCookie($token,$value){
        setcookie($token,$value);
    }

    function readCookie($token,$default){
        if (!isset($_GET[$token])){
            if (isset($_COOKIE[$token])){
            	
            	$value = $_COOKIE[$token]; 
            }else{
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
    $nomColonne = readCookie("nomColonne","name");
    $thres = readCookie("threshold",5000);
    $ordreMax = readCookie("ordre",3);
    $DbDatabase = readCookie("DbDatabase","");
    $lat = readCookie("latitude",45.6);
    $long = readCookie("longitude",-73.5);

?>
