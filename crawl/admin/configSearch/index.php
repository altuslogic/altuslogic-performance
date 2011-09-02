<?php

    $nomMaitre = "antoine_maitre";
    include "configSearch/time_function.php";
    include "configSearch/progressbar.php";
    include "conversion_funcs.php";

    error_reporting(15);                    
    $temps_total = start_timer();
    initProgress(5,5,600,30,'#fff','#444','#006699');

    mysql_select_db($nomMaitre);
    creeLog();
    creeProjets();
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
            $result = mysql_query("SELECT * FROM $nomMaitre.champs_recherche WHERE hash='$hash' LIMIT 1");
            $tab = mysql_fetch_assoc($result);
            unset($tab['hash']);
            foreach ($tab as $col=>$val){
                $$col = $val;
            }
            break;
        case 'update_correc':
            if (isset($_POST['action'])){
                foreach ($_POST as $key=>$val){
                    $$key = $val;
                }
                $correc_word = strtoupper($correc_word); 
                mysql_select_db($nomMaitre);
                if ($action=="new"){         
                    mysql_query("INSERT INTO corrections SET action='$correc_action', type='$correc_type', word='$correc_word', project='$nomProjet'");// or die($sql);
                    $correc_id = mysql_insert_id();
                }
                else if ($action=="save"){       
                        mysql_query("UPDATE corrections SET action='$correc_action', type='$correc_type', word='$correc_word', project='$nomProjet' WHERE id=$correc_id");// or die($sql);
                    }
                    else if ($action=="delete"){      
                            mysql_query("DELETE FROM corrections WHERE id=$correc_id");// or die($sql);
                            unset($correc_id);
                        }
                        mysql_select_db($nomBase);                        
            }
            break;
        case 'load_correc':
            $result = mysql_query("SELECT project,action,type,word FROM $nomMaitre.corrections WHERE id=$correc_id LIMIT 1");
            $tab = mysql_fetch_assoc($result);
            foreach ($tab as $col=>$val){
                $nomVar = "correc_".$col;
                $$nomVar = $val;
            } 
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
                if (isset($_POST['i_'.$nomColonne])) creeIndex(null,1); 
                if (isset($_POST['j_'.$nomColonne])) creeIndex(null,2);                        
            }
            $nomColonne = $col;
            break;
        case 'indexmot':
            creeIndex(null,1);
            break;
        case 'indexphrase':
            creeIndex(null,2);
            break;
        case 'keywords':
            if (isset($_POST['action']) && $_POST['action']=="ignore"){
                updateCorrec('no_index','word',$_POST['word'],$nomProjet); 
            }                                                                              
            break;
        case 'add_project':
            if (isset($_POST['projet'])){
                $name = $_POST['projet'];
                if ($name!=""){
                    updateProjets($name);
                    $nomProjet = $name; 
                }
            }
            break;
        case 'reindex':
            creeIndex($_POST,1);
            break;
    }

    if ($hash!="") $print_search = "<div id='search_zone_".$hash."'></div>\n<script type='text/javascript'>var key='".$hash."';</script>\n<script type='text/javascript' src='../../recherche/getSearchField.js'></script>";

    include "configSearch/view.html";   

?>
