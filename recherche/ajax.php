<?php
    header("Content-type: text/html; charset=ISO-8859-1");
    $search = $_GET['search'];                         

    /*include "cookie.php";
    include "config/db.php";
    include "time_function.php";  
    include "controller.php";*/ 

    // retourne les r�sultats de la recherche
    //$array = recherche($search); 
    $result = array('r�sultat 1','r�sultat 2');                                                 
    for ($i=0; $i<sizeof($result); $i++){
        echo $result[$i],"|";
    }                     
?>
