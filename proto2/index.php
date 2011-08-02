<?php             

    include "config/db.php";
    include "time_function.php";
    include "cookie.php";  
    include "controller.php"; 
    include "progressbar.php";

    $temps_total = start_timer();
    echo '<link rel="stylesheet" type="text/css" href="my.css"><body><br><br><br>';
    init(5,5,600,30,'#fff','#444','#006699');

    $stage = $_GET[stage];                       
    $option = $_POST[option];

    switch($stage){
        case 'initialize':
            creeLog();
            creeStats();
            creeTables(); 
            break;
        case 'performances': 
            performance();                                       
        case 'details':    
            $print_details = getDetails();  
            break;       
        case 'clear_tables':
            clearTables();
            clearStats(); 
            break;
        case 'delete_tables':
            deleteTables(); 
            deleteStats();
            break;
        case 'clear_cache':
            mysql_query("RESET QUERY CACHE");
            break;
        case 'search':
            deleteLog();
            creeLog();
            /*$text =  $_POST[champ1];
            if ($text!="") recherche($text,true); */
            break;
        case 'modif':
            $text = $_POST[champ2];   
            if ($_POST[insert]=="insert"){
                if ($text!="" && !existe($text)) insertion($text);
            }
            else {
                if ($text!="" && existe($text)) suppression($text);  
            }                                                     
            break;
        case 'index':            
            creeIndex();
            break;
    }

    // Sélection des données
    $sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = '$DbDatabase'
    AND table_name NOT LIKE 'y\_%' AND table_name NOT LIKE 'z\_%'";
    $result = mysql_query($sql);
    $print_donnees = "";

    while ($ligne=mysql_fetch_array($result)){
        if ($ligne[0]==$nomBase){
            $print_donnees .= "<b>".$ligne[0]."</b><br>";  
        }
        else $print_donnees .= "<a href='?nomBase=".$ligne[0]."'>".$ligne[0]."</a><br>";
    }                                                            
    $print_donnees .= "<br>Temps total : ".end_timer($temps_total)." secondes.<br>".$print_details;   

    // Colonnes
    $sql = "SHOW COLUMNS FROM ".$nomBase;
    $result = mysql_query($sql);
    $print_colonnes = "<table><tr><td>Nom</td><td>Type</td><td>Nombre</td><td>Tables</td><td>Index</td><td>Action</td>";

    while ($ligne=mysql_fetch_array($result)){
        $print_colonnes .= "<tr><form action='?stage=index' method='post'><td>";
        if ($ligne[Field]==$nomColonne){
            $print_colonnes .= "<b>".$ligne[Field]."</b>";  
        }
        else $print_colonnes .= "<a href='?nomColonne=".$ligne[Field]."'>".$ligne[Field]."</a>";
        $print_colonnes .= "</td><td>".$ligne[Type]."</td>";
        $sql = "SELECT COUNT(DISTINCT $ligne[Field]) FROM ".$nomBase;
        $nb = mysql_result(mysql_query($sql),0);
        $print_colonnes .= "<td>".$nb."</td><td><p align='center'><input type='checkbox'></p></td><td><p align='center'><input type='checkbox'></p></td><td><input type='submit' value='go'></td></form></tr>";    
    }                                                                                                
    $print_colonnes .= "</table>";

    // Log
    $sql = "SELECT action,temps,heure FROM y_".$nomBase."_log ORDER BY id DESC LIMIT 10";
    $result = mysql_query($sql);
    $print_log = "";

    while ($ligne=mysql_fetch_array($result)){ 
        $print_log = "<tr><td>".$ligne[heure]."</td><td>".$ligne[action]."</td><td>".$ligne[temps]."</td></tr>".$print_log;  
    }
    $print_log = "<table><tr><td>Heure</td><td>Action</td><td>Durée</td></tr>".$print_log."</table>";

    include "view.html";   

?>
