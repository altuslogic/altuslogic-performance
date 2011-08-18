<?php
   
    header("Content-type: text/html; charset=ISO-8859-1");         
    $id = $_GET['id'];  
    $nomMaitre = $_GET['base'];
    $nomTable = $_GET['table'];
    $nomColonne = $_GET['colonne'];

    include "../../recherche/config/db.inc.php";
    
    $table = "y_".$nomTable."_".$nomColonne."_keyword";
    $sql = "SELECT $nomColonne FROM $table WHERE id='$id' LIMIT 1";
    $result = mysql_query($sql) or die($sql."<br>".mysql_error());
    $row = mysql_fetch_row($result);
    $word = $row[0];
    
    $table = "y_".$nomTable."_".$nomColonne."_indexword";                                               
    $sql = "SELECT $nomTable.$nomColonne FROM $table,$nomTable WHERE $table.keyword='$id' AND $table.id=$nomTable.id LIMIT 10";   
    $result = mysql_query($sql) or die($sql."<br>".mysql_error());         
      
    echo "<h2>$word</h2><ul>";                
    while ($tab = mysql_fetch_array($result)){ 
        echo "<li>".$tab[$nomColonne]."</li>";
    } 

?>

</ul>
<form action="?stage=keyword" method="post">
<p align="center">         
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="submit" name="ignore" id="ignore" value="ignore">
<input type="submit" name="rename" id="rename" value="rename">      
</p>
</form>