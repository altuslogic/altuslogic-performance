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
    if (isset($_GET['option'])) $option = $_POST['option'];

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
            echo analyse();
            //deleteLog();
            /*$text =  $_POST[search];
            if ($text!="") recherche($text,true); */
            break;
        case 'modif':
            $text = $_POST[modif];   
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
        case 'position':

            break;
    }

    include "view.html";   

?>
