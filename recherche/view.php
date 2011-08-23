<?php
    include "config/config.inc.php";
    include "config/db.inc.php";  
?>

<head>                                                  
    <link rel="stylesheet" href="../jquery.ui/all.css">      
    <script src="../jquery-1.5.1.js"></script>
</head>   

<?php  
    $sql = "SELECT hash FROM champs_recherche";
    $result = mysql_query($sql);
    $print = "";                    
    while ($tab = mysql_fetch_array($result)){                                     
            $print .= "<div id='search_zone_".$tab['hash']."' style='border:1px solid #444;'></div>
            <script type='text/javascript'>var key='".$tab['hash']."';</script>
            <script type='text/javascript' src='../recherche/getSearchField.js'></script><br><br>"; 
    }
    echo $print;
?>
