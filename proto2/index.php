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

    creeLog(); 

    $stage="";
    if (isset($_GET['stage'])) $stage = $_GET['stage'];                       

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
        case 'save':     
            mysql_select_db("maitre");
            $hash = md5($nomBase.$nomTable.$nomColonne.$mode.$methode.$visuel);
            $sql = "INSERT INTO champs_recherche SET hash='$hash', nomBase='$nomBase', nomTable='$nomTable',
            nomCol='$nomColonne', mode='$mode', methode='$methode', visuel='$visuel'";
            mysql_query($sql);
            
            $code = "<iframe src=\'http://localhost/recherche/getSearchField.php?key=".$hash."\' width=\'100%\' height=\'500\'<p>Your browser does not support iframes.</p></iframe><br><br>";
            
            echo "<script>prompt('Code :','".$code."');</script>";    
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
            //echo "selec : ",$_POST[t_id];            
            creeIndex();
            break;
    }

    include "view.html";   

?>
