<?php

    function saveCookie($token,$value){
        setcookie($token,$value);
    }

    function readCookie($token,$default){
        if (!isset($_GET[$token])){
            if (isset($_COOKIE[$token])){
                $value = $_COOKIE[$token]; 
            }
            else {
                $value = $default;
            } 
        }
        else {
            saveCookie($token,$_GET[$token]);
            $value = $_GET[$token];
        }
        return $value;
    }     

    function readCookieP($token,$default){
        if (!isset($_POST[$token])){
            if (isset($_COOKIE[$token])){
                $value = $_COOKIE[$token]; 
            }
            else {
                $value = $default;
            } 
        }
        else {
            saveCookie($token,$_POST[$token]);
            $value = $_POST[$token];
        }
        return $value;
    } 

    $nomBase = readCookie("nomBase","");    
    $nomTable = readCookie("nomTable","");
    $nomColonne = readCookie("nomColonne","");
    $mode = readCookieP("mode","milieu");     
    $methode = readCookieP("methode","direct");
    $visuel = readCookieP("visuel","result");
    $resume = readCookieP("resume",1);
    $limite = readCookieP("limite",10);
    $nomDiv = readCookieP("nomDiv","ajax");
    $afficheDiv = readCookieP("afficheDiv",1);  
    $containerAll = readCookieP("containerAll","<b>~TITLE~</b><ul>~ALL~</ul>~TIME~"); 
    $containerResult = readCookieP("containerResult","<li>~RES~</li>");
    $containerDetails = readCookieP("containerDetails","");  
    $f = readCookie("f",2); 
    $type = readCookie("type",""); 
    $thres = readCookie("threshold",5000);
    $ordreMax = readCookie("ordre",3);
    echo "base : $nomBase<br>table : $nomTable<br>colonne : $nomColonne";
 
?>
