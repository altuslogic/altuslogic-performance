<?php

    $nomMaitre = "antoine_maitre";
    include "configSearch/time_function.php";
    include "configSearch/progressbar.php";
    error_reporting(15);                    
    $temps_total = start_timer();
    //  echo '<link rel="stylesheet" type="text/css" href="my.css"><body><br><br><br>';
    if ($show=="index" || $show=="expr" || $show=="correct" || $show=="all") initProgress(5,5,600,30,'#fff','#444','#006699');

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
        case 'update_correc':
            if (isset($_POST['action'])){
                foreach ($_POST as $key=>$val){
                    $$key = $val;
                } 
                mysql_select_db($nomMaitre);
                if ($action=="new"){         
                    mysql_query("INSERT INTO corrections SET action='$correc_action', type='$correc_type', word='$correc_word', project='$correc_project'");// or die($sql);
                    $correc_id = mysql_insert_id();
                }
                else if ($action=="save"){       
                        mysql_query("UPDATE corrections SET action='$correc_action', type='$correc_type', word='$correc_word', project='$correc_project' WHERE id=$correc_id]");// or die($sql);
                    }
                    else if ($action=="delete"){      
                            mysql_query("DELETE FROM corrections WHERE id=$correc_id");// or die($sql);
                            unset($correc_id);
                        }
                        mysql_select_db($nomBase);                        
            }
            break;
        case 'load_correc':
            mysql_select_db($nomMaitre);
            $result = mysql_query("SELECT project,action,type,word FROM corrections WHERE id='$correc_id' LIMIT 1");
            $tab = mysql_fetch_assoc($result);
            foreach ($tab as $col=>$val){
                $nomVar = "correc_".$col;
                $$nomVar = $val;
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
        case 'keywords':
            if (isset($_POST['action']) && $_POST['action']=="ignore"){
                updateCorrec('ignore','word',$_POST['word'],$nomProjet); 
            }                                                                              
            break;
        case 'correct':
            if (isset($_POST['action'])){
                $meth = $_POST['methode'];
                if ($_POST['action']=="correct"){     
                    foreach ($_POST as $key=>$val){
                        if ($key!='action' && $key!='methode'){
                            $key = str_replace(array("++","_"),array("'"," "),$key);
                            $t = explode("**",$key);            
                            $mot = explode("##",$t[1]);
                            if ($t[0]=='correct') updateCorrec($t[0],$meth,$mot[0]." => ".$mot[1],$nomProjet);
                            else if ($t[0]=='split') updateCorrec($t[0],'word',$mot[0]." | ".$mot[1],$nomProjet);      
                        }
                    }
                }
                else if ($_POST['action']=="ignore"){     
                        foreach ($_POST as $key=>$val){    
                            if ($key!='action' && $key!='methode'){
                                $t = explode("**",$key);
                                $t[2] = str_replace(array("++","_"),array("'"," "),$t[2]);
                                updateCorrec('no_problem',$meth,$t[2],$nomProjet);
                            }
                    }
                }
            }
            break;
        case 'expression':
            if (isset($_POST['action']) && $_POST['action']=="add"){
                foreach ($_POST as $key=>$val){
                    if ($key!='action'){          
                        updateCorrec('expression','word',str_replace(array("++","_"),array("'"," "),$key),$nomProjet);
                    }
                } 
            } 
            break;
    }

    if ($hash!="") $print_search = "<div id='search_zone_".$hash."'></div>\n<script type='text/javascript'>var key='".$hash."';</script>\n<script type='text/javascript' src='../../recherche/getSearchField.js'></script>";

    include "configSearch/view.html";   

?>
