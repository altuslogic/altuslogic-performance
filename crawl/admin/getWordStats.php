<?php
   
    header("Content-type: text/html; charset=ISO-8859-1");         
    $id = $_GET['id'];  
    $nomMaitre = $_GET['base'];
    $nomTable = $_GET['table'];
    $nomColonne = $_GET['colonne'];
    $zone = $_GET['zone']; 
    $meth = $_GET['methode'];
    
    include "../../recherche/config/db.inc.php";
    
    $table = "y_".$nomTable."_".$nomColonne."_key".$meth;
    $sql = "SELECT $nomColonne FROM $table WHERE id='$id' LIMIT 1";
    $result = mysql_query($sql) or die($sql."<br>".mysql_error());
    $row = mysql_fetch_row($result);
    $word = $row[0];
    
    $table = "y_".$nomTable."_".$nomColonne."_index".$meth;                                               
    $sql = "SELECT $nomTable.$nomColonne FROM $table,$nomTable WHERE $table.keyword='$id' AND $table.id=$nomTable.id LIMIT 10";   
    $result = mysql_query($sql) or die($sql."<br>".mysql_error());         
      
    echo "<h2>$word</h2><ul>";                
    while ($tab = mysql_fetch_array($result)){
        $t = $tab[$nomColonne];
        if (mb_detect_encoding($t,"UTF-8",true)) $t = utf8_decode($t);
        echo "<li>$t</li>";
    } 

?>

</ul>
<form action="?stage=<?php echo $zone; ?>" method="post">
<p align="center">         
<input type="hidden" name="id" value="<?php echo $id; ?>">

<?php
switch($zone){
    case 'stats_keywords':
        echo "<input type='submit' name='action' value='ignore'>";
        break;
   /* case 'stats_correc':
        echo "<input type='submit' name='merge' id='merge' value='merge'>";
        break;       */
}
?>
    
</p>
</form>