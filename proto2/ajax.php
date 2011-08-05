<?php

    header("Content-type: text/html; charset=ISO-8859-1");
    $search = $_GET['search'];
    $mode = $_GET['mode'];                         
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];

    include "cookie.php";
    include "config/db.php";
    include "time_function.php";  
    include "controller.php";  

    // retourne les résultats de la recherche
    $array = recherche($search, $mode, "tout", "result", array($lat,$lon)); 
    $result = $array['resultats'];                                                 
    for ($i=0; $i<sizeof($result); $i++){
        echo $result[$i][0],",",round($result[$i]['distance']),",",$result[$i]['latitude'],",",$result[$i]['longitude'],"|";
    }
    echo $array['temps'];                           

?>
