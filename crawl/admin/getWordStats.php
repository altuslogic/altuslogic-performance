<?php

    header("Content-type: text/html; charset=ISO-8859-1");

    $nomMaitre = $_GET['base'];
    $nomTable = $_GET['table'];
    $nomColonne = $_GET['colonne'];
    
    include "../../recherche/config/db.inc.php";
    include "configSearch/controller.php";
    include "conversion_funcs.php";

    $zone = $_GET['zone'];
    $meth = $_GET['methode'];
    $string = decode($_GET['string']);
    $id = $_GET['id']; 
                                      
    if ($zone=='stats_expr'){

        $expr = addslashes($string); 
        $tablePhrase = "y_".$nomTable."_".$nomColonne."_keyphrase";
        $table = getSousTable($tablePhrase,$nomColonne,$expr);
        if ($table==""){
            $table = $tablePhrase; echo "Sous-table introuvable : $expr<br>";
        }
        else $table = "z_".$tablePhrase."_".$nomColonne."_".$table;

        $sql = "SELECT $nomColonne FROM $table WHERE $nomColonne LIKE '%$expr%'";  
        $result = mysql_query($sql) or die($sql."<br>".mysql_error());
        echo "<h2>$expr</h2>"; 

    }

    else {

        $primary = getPrimaryKey($nomTable);                                                                                                 
        $table = "y_".$nomTable."_".$nomColonne."_index".$meth;                                               
        $sql = "SELECT $nomTable.$nomColonne FROM $table,$nomTable WHERE $table.keyword='$id' AND $table.id=$nomTable.$primary";   
        $result = mysql_query($sql) or die($sql."<br>".mysql_error());       
        echo "<h2>$string</h2>";                

    }

    echo "<div style='max-height:400px; overflow:auto; border:1px solid #aaa;'><ul>";
    while ($tab = mysql_fetch_array($result)){
        $t = $tab[$nomColonne];
        if (mb_detect_encoding($t,"UTF-8",true)) $t = utf8_decode($t);
        echo "<li><a href='?type=correct&correc_type=phrase&correc_word=".strtoupper(encode(sansAccents($t)))."'>$t</a></li>";
    }
    echo "</ul></div>";

    if ($zone=='stats_keywords'){ ?>
    <form action="?stage=keywords" method="post">
        <p align="center">         
            <input type="hidden" name="word" value="<?php echo $string; ?>">
            <input type='submit' name='action' value='ignore'>
        </p></form>
    <?php } ?>