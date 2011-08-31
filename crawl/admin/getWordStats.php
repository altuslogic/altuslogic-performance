<?php

    header("Content-type: text/html; charset=ISO-8859-1");         
    $id = $_GET['id'];  
    $nomMaitre = $_GET['base'];
    $nomTable = $_GET['table'];
    $nomColonne = $_GET['colonne'];
    $zone = $_GET['zone']; 
    $meth = $_GET['methode'];

    include "../../recherche/config/db.inc.php";
    include "configSearch/controller.php";
                                             
    if ($zone=='stats_expr'){

        $expr = addslashes(str_replace("**","&",$id)); // $id contient ici une chaîne de caractères
        $tablePhrase = "y_".$nomTable."_".$nomColonne."_keyphrase";
        $table = getSousTable($tablePhrase,$nomColonne,$expr);
        if ($table==""){
            $table = $tablePhrase; echo $expr."<br>";
        }
        else $table = "z_".$tablePhrase."_".$nomColonne."_".$table;

        $sql = "SELECT $nomColonne FROM $table WHERE $nomColonne LIKE '%$expr%'";  
        $result = mysql_query($sql) or die($sql."<br>".mysql_error());
        echo "<h2>$expr</h2>"; 

    }

    else {

        $table = "y_".$nomTable."_".$nomColonne."_key".$meth;
        $sql = "SELECT $nomColonne FROM $table WHERE id='$id' LIMIT 1";
        $result = mysql_query($sql) or die($sql."<br>".mysql_error());
        $row = mysql_fetch_row($result);
        $word = $row[0];

        $primary = getPrimaryKey($nomTable);                                                                                                 
        $table = "y_".$nomTable."_".$nomColonne."_index".$meth;                                               
        $sql = "SELECT $nomTable.$nomColonne FROM $table,$nomTable WHERE $table.keyword='$id' AND $table.id=$nomTable.$primary";   
        $result = mysql_query($sql) or die($sql."<br>".mysql_error());       
        echo "<h2>$word</h2>";                

    }

    echo "<div style='max-height:400px; overflow:auto; border:1px solid #aaa;'><ul>";
    while ($tab = mysql_fetch_array($result)){
        $t = $tab[$nomColonne];
        if (mb_detect_encoding($t,"UTF-8",true)) $t = utf8_decode($t);
        echo "<li><a href='?type=correct&correc_type=phrase&correc_word=".strtoupper($tab[$nomColonne])."'>$t</a></li>";
    }
    echo "</ul></div>";

    if ($zone=='stats_keywords'){ ?>
    <form action="?stage=keywords" method="post">
        <p align="center">         
            <input type="hidden" name="word" value="<?php echo $word; ?>">
            <input type='submit' name='action' value='ignore'>
        </p></form>
    <?php } ?>