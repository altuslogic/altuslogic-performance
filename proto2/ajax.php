<?php

    header("Content-type: text/html; charset=ISO-8859-1");
    $search = $_GET[search];
    $mode = $_GET[mode];                         

    include "config/db.php";
    include "time_function.php";
    include "cookie.php";  
    include "controller.php";  

    // retourne les résultats de la recherche
    echo "<h2>Résultats de la recherche</h2>";
    $array = recherche($search, $mode); 
    $result = $array["resultats"];
    $temps = $array["temps"];
    echo "<br>",str_replace(",","<br>",$result);
    echo "<br><br>Temps écoulé : ",$temps," seconde(s)";
?>
