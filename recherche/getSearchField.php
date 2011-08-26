<?php 
                  
    include "config/config.inc.php";
    include "config/db.inc.php";
    include "search_funcs.php";

    error_reporting(15); 
    if (isset($_GET['key'])) $hash=$_GET['key'];
    else $hash="";

    $sql = "SELECT * FROM champs_recherche WHERE hash='$hash' LIMIT 1";
    $result = mysql_query($sql) or die(mysql_error());
    $ligne = mysql_fetch_assoc($result);

    foreach ($ligne as $key => $val){    
        $$key = $val;                                       
    }                   

    if ($resume==1){
        echo "<table border='1'>"; 
        foreach ($ligne as $key => $val){    
            echo "<tr><td>".$key."</td><td".($key=="hash"? " style='font-size:7pt;'": "").">".$val."</td></tr>";
        }          
        echo "</table><br>";
    }

    mysql_select_db($nomBase);

    $param = "\"".$hash."\",\"".$nomBase."\",\"".$nomTable."\",\"".$nomColonne."\",\"".$mode."\",\"".$methode."\",\"".$visuel."\",\"".$limite."\",\"".$nomDiv."\",\"".$containerAll."\",\"".$containerResult."\",\"".$containerDetails."\"";     

    $ok = "OK";
    $t = "y_".$nomTable."_".$nomColonne;
    if ($methode=='tables' && !tableExiste($t."_stats") || $methode=='mot' && !tableExiste($t."_keyword") || $methode=='tout' && !tableExiste($t."_keyphrase")){
        $ok = "KO";
    }

?> 

<form style="margin-bottom: 0;">
    <input type='text' onkeyup='javascript:soumettre(this.value,<?php echo $param; ?>);' id='champ_<?php echo $hash; ?>' style="background-color: transparent; color: #444; border: 1px solid #444;">
    <?php echo $ok; ?>
</form><br>

<?php if ($afficheDiv){ ?>                   
    <div id='<?php echo $nomDiv; ?>'></div>  
    <?php } ?>
