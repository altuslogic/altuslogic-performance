<?php             

    include "cookie.php";  
    include "config/db.php";
    include "time_function.php";
    include "controller.php"; 
    include "progressbar.php";
    error_reporting(15);
    $temps_total = start_timer();
    echo '<link rel="stylesheet" type="text/css" href="my.css"><body><br><br><br>';
    init(5,5,600,30,'#fff','#444','#006699');

    mysql_select_db("maitre");
    creeLog(); 
    initChamps();
    mysql_select_db($nomBase);
    
    $stage="";
    if (isset($_GET['stage'])) $stage = $_GET['stage'];                       

    $print_details = "";
    $print_search = "";

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
        case 'delete_tables':
            deleteTables(); 
            deleteStats();
        case 'delete_index':
            deleteIndex();
            break;
        case 'clear_cache':
            mysql_query("RESET QUERY CACHE");
            break;
        case 'save_param':     
            mysql_select_db("maitre");
            $hash = md5($nomBase.$nomTable.$nomColonne.$mode.$methode.$visuel.$resume.$limite.$nomDiv.$afficheDiv.$containerAll.$containerResult.$containerDetails);
            $sql = "INSERT INTO champs_recherche SET hash='$hash', nomBase='$nomBase', nomTable='$nomTable', nomColonne='$nomColonne',
            mode='$mode', methode='$methode', visuel='$visuel', resume='$resume', limite='$limite', nomDiv='$nomDiv', afficheDiv='$afficheDiv',
            containerAll='$containerAll', containerResult='$containerResult', containerDetails='$containerDetails'";
            mysql_query($sql);// or die($sql);

            $print_search = "<script type='text/javascript'>var key='".$hash."';</script><script type='text/javascript' src='../recherche/getSearchField.js'></script><div id='search_zone'></div>";
            mysql_select_db($nomBase);
             
            //echo analyse();
            break;    
        case 'modif':
            $text = $_POST['modif'];   
            if ($_POST['insert']=="insert"){
                if ($text!="" && !existe($text)) insertion($text);
            }
            else {
                if ($text!="" && existe($text)) suppression($text);  
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
    }

    include "view.html";   

?>
