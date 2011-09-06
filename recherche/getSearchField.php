<?php 

    include "config/config.inc.php";
    include "config/db.inc.php";
    include "search_funcs.php";

    error_reporting(15); 
    $hash = isset($_GET['hash'])? $_GET['hash']: "";

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

    unset($ligne['resume']);
    unset($ligne['afficheDiv']);
    unset($ligne['description']);
    $param = json_encode($ligne);

    mysql_select_db($nomBase);

    $ok = "";
    $t = "y_".$nomTable."_".$nomColonne;
    if (($methode_suggest=='tables' || $methode_result=='tables') && !tableExiste($t."_stats")
    || ($methode_suggest=='mot' || $methode_result=='mot') && !tableExiste($t."_keyword")
    || ($methode_suggest=='phrase' || $methode_result=='phrase') && !tableExiste($t."_keyphrase")){
        $ok = "disabled";
    }

?> 

<form style="margin-bottom: 0;">
    <input type='text' onkeyup='javascript:soumettre(true,"champ_<?php echo $hash; ?>",<?php echo $param; ?>);' id='champ_<?php echo $hash; ?>' style="background-color: transparent; color: #444; border: 1px solid #444;">
    <input type='button' <?php echo $ok; ?> value="search" onclick='javascript:soumettre(false,"champ_<?php echo $hash; ?>",<?php echo $param; ?>);'>
</form><br>

<?php if ($afficheDiv){ ?>                   
    <div id='<?php echo $nomDiv; ?>'></div>  
    <?php } ?>
