<link rel="stylesheet" type="text/css" href="configSearch/my.css">

<div id='submenu'>
    <ul>                               
        <li><a href="admin.php?f=production&type=selection">selection</a></li>  
        <li><a href="admin.php?f=production&type=edition">edition</a></li>  
    </ul>
</div>

<?php   


$stats_slq="SELECT COUNT(*) AS `count_titre` FROM `$nomBase`.$nomTable WHERE `final_titre`IS NOT NULL";

 $result=mysql_query($stats_slq);
 echo $result[count_titre];                          
  //  include "configSearch/index.php";
?>

<iframe src=""></iframe>