<?php
    $nomBase = "maitre";
    include "../proto2/config/db.php";
    $sql = "SELECT hash FROM champs_recherche";
    $result = mysql_query($sql);
    $print = "";
    while ($tab = mysql_fetch_array($result)){
        $print .= "<iframe src='getSearchField.php?key=".$tab[hash]."' width='100%' height='300'";
        $print .= "<p>Your browser does not support iframes.</p></iframe><br><br>";
    } 
    echo $print;
?>