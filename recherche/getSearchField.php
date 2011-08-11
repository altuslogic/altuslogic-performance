<?php 

    $nomBase="maitre";

    include "../proto2/config/db.php";
    include "../proto2/time_function.php"; 
    include "../proto2/controller.php";

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
            echo "<tr><td>".$key."</td><td>".$val."</td></tr>";
        }          
        echo "</table>";
    }

    mysql_select_db($nomBase);

    $param = "\"".$nomBase."\",\"".$nomTable."\",\"".$nomColonne."\",\"".$mode."\",\"".$methode."\",\"".$visuel."\",\"".$limite."\",\"".$nomDiv."\",\"".$containerAll."\",\"".$containerResult."\",\"".$containerDetails."\"";     

    $ok = "OK";
    $t = "y_".$nomTable."_".$nomColonne;
    if ($methode=='tables' && !tableExiste($t."_stats") || $methode=='mot' && !tableExiste($t."_keyword") || $methode=='tout' && !tableExiste($t."_keyphrase")){
        $ok = "KO";
    }

?> 

<head> 
    <link rel="stylesheet" href="../jquery.ui/all.css"> 
    <script src="../jquery-1.5.1.js"></script> 
    <script src="../jquery.ui/core.js"></script> 
    <script src="../jquery.ui/widget.js"></script> 
    <script src="../jquery.ui/position.js"></script> 
    <script src="../jquery.ui/autocomplete.js"></script>
</head> 

<br><form>
    <input type='text' onkeyup='javascript:soumettre(this.value,<?php echo $param; ?>);' id='champ_<?php echo $hash; ?>' style="background-color: transparent; color: #444; border: 1px solid #444;">
    <?php echo $ok; ?>
</form>

<?php if ($afficheDiv){ ?>                   
<div id='<?php echo $nomDiv; ?>'></div>  
<?php } ?>

<script> 
    $("#champ_<?php echo $hash; ?>").autocomplete({source: []}); 
</script>