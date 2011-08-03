<?php             

    include "config/db.php";
    include "time_function.php";
    include "cookie.php";  
    include "controller.php"; 
    include "progressbar.php";
     
    $temps_total = start_timer();
    echo '<link rel="stylesheet" type="text/css" href="my.css"><body><br><br><br>';
    init(5,5,600,30,'#fff','#444','#006699');

    creeLog(); 

    $stage = $_GET['stage'];                       
    $option = $_POST['option'];
    $print_details = "";
    
    switch($stage){
        case 'initialize':
            creeLog();
            creeStats();
            creeTables(); 
            break;
        case 'performances': 
            performances();                                       
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
            $sql = "SELECT * FROM y_".$nomBase."_".$nomColonne."_stats PROCEDURE ANALYSE(3,24)";
            $result = mysql_query($sql);
            echo "<table><tr><td>Field name</td><td>Min value</td><td>Max value</td><td>Min length</td><td>Max length</td><td>Empties or zeros</td><td>Nulls</td><td>Optimal field type</td></tr>";
            while ($ligne=mysql_fetch_array($result)){
              echo "<tr><td>",$ligne[0],"</td><td>",$ligne[1],"</td><td>",$ligne[2],"</td><td>",$ligne[3],"</td><td>",$ligne[4],"</td><td>",$ligne[5],"</td><td>",$ligne[6],"</td><td>",$ligne[9],"</td></tr>";  
            }
            echo "</table>";
            //deleteLog();
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
            //echo "selec : ",$_POST[t_id];            
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
    $print_colonnes = "<form action='?stage=index' method='post'><table><tr><td>Nom</td><td>Type</td><td>Nombre</td><td>Tables</td><td>Index</td>";

    while ($ligne=mysql_fetch_array($result)){
        $nom = $ligne['Field'];

        $sql = "SHOW TABLES LIKE 'z\_".$nomBase."\_".$nom."\_%'";
        $tables = mysql_num_rows(mysql_query($sql))>0? "checked disabled='disabled'" : "";
        $index = tableExiste("y_".$nomBase."_".$nom."_index")? "checked disabled='disabled'" : "";

        $print_colonnes .= "<tr><td>";
        if ($nom==$nomColonne){
            $print_colonnes .= "<b>".$nom."</b>";  
        }
        else $print_colonnes .= "<a href='?nomColonne=".$nom."'>".$nom."</a>";
        $print_colonnes .= "</td><td>".$ligne['Type']."</td>";
        $sql = "SELECT COUNT(DISTINCT $nom) FROM ".$nomBase;
        $nb = mysql_result(mysql_query($sql),0);
        $print_colonnes .= "<td>".$nb."</td><td><p align='center'><input type='checkbox' ".$tables." id='t_".$nom."'></p></td><td><p align='center'><input type='checkbox' ".$index." id='i_".$nom."'></p></td></tr>";    
    }                                                                                                
    $print_colonnes .= "</table><p align='center'><input type='submit' value='apply'></p></form>";

    // Log
    $sql = "SELECT action,temps,heure FROM y_".$nomBase."_log ORDER BY id DESC LIMIT 10";
    $result = mysql_query($sql);
    $print_log = "";

    while ($ligne=mysql_fetch_array($result)){ 
        $print_log = "<tr><td>".$ligne['heure']."</td><td>".$ligne['action']."</td><td>".$ligne['temps']."</td></tr>".$print_log;  
    }
    $print_log = "<table><tr><td>Heure</td><td>Action</td><td>Durée</td></tr>".$print_log."</table>";

    include "view.html";   

?>
