<?php

    $nomMaitre = "antoine_maitre";
    include "configSearch/time_function.php";
    include "configSearch/progressbar.php";
    error_reporting(15);                    
    $temps_total = start_timer();
    //  echo '<link rel="stylesheet" type="text/css" href="my.css"><body><br><br><br>';
    if ($show=="index" || $show=="expr" || $show=="correct" || $show=="all") init(5,5,600,30,'#fff','#444','#006699');

    mysql_select_db($nomMaitre);
    creeLog();
    creeCorrec(); 
    creeChamps();
    mysql_select_db($nomBase);

    $stage = "";
    if (isset($_GET['stage'])) $stage = $_GET['stage'];                       
    $hash = "";
    if (isset($_GET['hash'])) $hash = $_GET['hash']; 
    $print_details = "";
    $print_search = "";       

    switch($stage){
        case 'initialize':
            creeTables(); 
            break;
        case 'performances': 
            performances();                                       
        case 'details':    
            $print_details = getDetails();  
            break;       
        case 'delete_tables':
            deleteTables(); 
            deleteStats();
        case 'delete_index':
            deleteIndex();
            break;
        case 'clear_log':
            videLog();
            break;
        case 'clear_cache':
            mysql_query("RESET QUERY CACHE");
            break;
        case 'update_param':
            if (isset($_POST['action'])){
                foreach ($_POST as $key=>$val){
                    $$key = $val;
                } 
                mysql_select_db($nomMaitre);
                if ($action=="new"){
                    $hash = md5(microtime());
                    mysql_query("INSERT INTO champs_recherche SET hash='$hash', nomBase='$nomBase', nomTable='$nomTable', nomColonne='$nomColonne',
                    mode='$mode', methode='$methode', visuel='$visuel', resume='$resume', limite='$limite', nomDiv='$nomDiv', afficheDiv='$afficheDiv',
                    containerAll='$containerAll', containerResult='$containerResult', containerDetails='$containerDetails', description='$description'");// or die($sql);
                }
                else if ($action=="save"){       
                        mysql_query("UPDATE champs_recherche SET nomBase='$nomBase', nomTable='$nomTable', nomColonne='$nomColonne',
                        mode='$mode', methode='$methode', visuel='$visuel', resume='$resume', limite='$limite', nomDiv='$nomDiv', afficheDiv='$afficheDiv',
                        containerAll='$containerAll', containerResult='$containerResult', containerDetails='$containerDetails', description='$description' WHERE hash='$hash'");// or die($sql);
                    }
                    else if ($action=="delete"){      
                            mysql_query("DELETE FROM champs_recherche WHERE hash='$hash'");// or die($sql);
                            $hash = "";
                        }
                        mysql_select_db($nomBase);                        
            }
            break;
        case 'load_param':
            mysql_select_db($nomMaitre);
            $result = mysql_query("SELECT * FROM champs_recherche WHERE hash='$hash' LIMIT 1");
            $tab = mysql_fetch_assoc($result);
            unset($tab['hash']);
            foreach ($tab as $col=>$val){
                $$col = $val;
            }
            mysql_select_db($nomBase); 
            break;    
        case 'modif':
            if (isset($_POST['action'])){
                $text = $_POST['modif'];   
                if ($_POST['action']=="insert"){
                    if ($text!="" && !existe($text)) insertion($text);
                }
                else if ($_POST['action']=="delete"){   
                        if ($text!="" && existe($text)) suppression($text);  
                }                 
            }                                    
            break;
        case 'index':            
            $col = $nomColonne;

            $sql = "SHOW COLUMNS FROM ".$nomTable;
            $result = mysql_query($sql) or die(mysql_error()."<br>".$sql);                                                                       

            while ($ligne=mysql_fetch_array($result)){
                $nomColonne = $ligne['Field'];                                                               
                if (isset($_POST['t_'.$nomColonne])) creeTables(); 
                if (isset($_POST['i_'.$nomColonne])) creeIndex(true); 
                if (isset($_POST['j_'.$nomColonne])) creeIndex(false);                        
            }
            $nomColonne = $col;
            break;
        case 'indexmot':
            creeIndex(true);
            break;
        case 'indexphrase':
            creeIndex(false);
            break;
        case 'stats_keywords':
            $id = $_POST['id'];
            if (isset($_POST['action']) && $_POST['action']=="ignore"){
                $table = "y_".$nomTable."_".$nomColonne."_keyword";
                $sql = "UPDATE $table SET ignored=1-ignored WHERE id='$id'";
                $result = mysql_query($sql) or die($sql."<br>".mysql_error());
            }                                                                               
            break;
        case 'correc':     
            foreach ($_POST as $key=>$val){
                $mot = explode("|",$key);
                updateCorrec($mot[1],$mot[0]);
            }
            break;
    }

    if ($hash!="") $print_search = "<div id='search_zone_".$hash."'></div>\n<script type='text/javascript'>var key='".$hash."';</script>\n<script type='text/javascript' src='../../recherche/getSearchField.js'></script>";

    include "configSearch/view.html";   

?>
