<style type="text/css">
    body {font-family: Verdana, Arial; font-size: 12px;}
    div {max-height:140px; overflow:auto; border:1px solid #aaa;}
</style>

<?php

    error_reporting(15); 

    include "configSearch/cookie.php";
    include "configSearch/controller.php";
    include "../settings/database.php";
    include "conversion_funcs.php";

    $nomMaitre = "antoine_maitre";
    mysql_pconnect ($DbHost, $DbUser, $DbPassword);          
    mysql_select_db($nomBase);

    $stage = "";
    if (isset($_GET['stage'])) $stage = $_GET['stage'];                             

    $correc = array();
    foreach ($_POST as $key=>$val){
        if ($key!='action' && $key!='methode'){
            $correc[] = $key;
        }
    }
    if (sizeof($correc)>0){

        switch($stage){

            case 'correct':
                if (isset($_POST['action'])){
                    $meth = $_POST['methode'];
                    if ($_POST['action']=="correct"){  
                        echo "<h2>Corrections ajoutées</h2><div><ul>";  
                        foreach ($correc as $val){
                            $t = explode("**",decode($val));            
                            $mot = explode("##",$t[1]);
                            if ($t[0]=='correct'){
                                updateCorrec($t[0],$meth,$mot[0]." => ".$mot[1],$nomProjet);
                                echo "<li>$mot[0] => $mot[1]</li>"; 
                            }
                            else if ($t[0]=='split'){
                                updateCorrec($t[0],'word',$mot[0]." | ".$mot[1],$nomProjet);
                                echo "<li>$mot[0] | $mot[1]</li>";    
                            }  
                        }
                    }
                    else if ($_POST['action']=="ignore"){
                            echo "<h2>Termes à ne pas corriger</h2><div><ul>";     
                            foreach ($correc as $val){    
                                $t = explode("**",$val);
                                $t[2] = decode($t[2]);
                                updateCorrec('ignore',$meth,$t[2],$nomProjet);
                                echo "<li>$t[2]</li>";
                            }
                        }
                }
                break;
            case 'expression':
                if (isset($_POST['action']) && $_POST['action']=="add"){
                    echo "<h2>Expressions ajoutées</h2><div><ul>"; 
                    foreach ($correc as $val){        
                        $expr = decode($val);
                        updateCorrec('expression','word',$expr,$nomProjet);
                        echo "<li>$expr</li>";
                    } 
                } 
                break;
        }
        echo "</ul></div>";
    }

?>