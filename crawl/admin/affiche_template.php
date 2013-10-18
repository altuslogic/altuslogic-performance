<?php



$xx[0]='final_titre';
$xx[1]='final_adresse';
$xx[2]='final_telephone';
$xx[3]='final_telecopie';
$xx[4]='final_email';
$xx[5]='final_contact';
$xx[6]='final_latitude';
$xx[7]='final_longitude';
$xx[8]='final_dimanche';
$xx[9]='final_lundi';
$xx[10]='final_mardi';
$xx[11]='final_mercredi';
$xx[12]='final_jeudi';
$xx[13]='final_vendredi';
$xx[14]='final_url';
$xx[15]='final_codepostal';
$xx[16]='final_quartier';
$xx[17]='final_ville';
$xx[18]='final_province';
$xx[19]='final_region';
$xx[20]='final_pays';
$xx[21]='final_google_map';
$xx[22]='final_activite';
$xx[23]='final_categorie';
$xx[24]='final_detail1';
$xx[25]='final_detail2';
$xx[26]='final_detail3';
$xx[27]='final_detail4';
$xx[28]='final_detail5';
$xx[29]='final_detail6';
$xx[30]='final_detail7';
$xx[31]='final_detail8';
$xx[32]='final_tmp1';
$xx[33]='final_detail7';
$xx[34]='final_tmp2';
$xx[35]='final_tmp3';

$x=0;$y=0;
$table_title.="<tr>";

while($x<36){
				$variable=$xx[$x];
				$valu[$variable]=$_GET[$variable];
				/*
				$result = mysql_query("SELECT COUNT(*) AS total_titre from ".$mysql_table_prefix."links where ".$variable." IS NOT NULL");
				//echo mysql_error();
				$row=mysql_fetch_array($result);
				$numb[$variable]=$row[total_titre];
				*/
				
				$x++;
				if($valu[$variable]=='on'){  
				
						$result = mysql_query("SELECT COUNT(*) AS total_titre from ".$mysql_table_prefix."links where ".$variable." IS NOT NULL");
						//echo mysql_error();
						$row=mysql_fetch_array($result);
						$numb[$variable]=$row[total_titre];
						
						$setval="checked"; 
						$sql_line.=' AND '.$variable.' IS NOT NULL';
						
						$sql_select.=','.$variable;
						$sql_mor.="$"."row['".$variable."']~~";
						
						
						$var_toprint[$y]=$variable;
						$table_title.="<td><b>".$variable."</b></td>";
						
						$y++;
						//echo $sql_mor;
						
				}else{ 	$setval=""; 
				}
				$html.='<input type="checkbox" name="'.$variable.'" '.$setval.'>'.$variable.'<font size="1" color="grey"> ('.$numb[$variable].')</font><br>';
				if($x%8==0){ $html.='</td><td width="180" valign="top">'; }
				
				
				
}	
$table_title.="</tr>";



?>

<link rel="stylesheet" type="text/css" href="configSearch/my.css">

<div id='submenu'>
    <ul>                               
        <li><a href="admin.php?f=template&type=selection">selection</a></li>  
        <li><a href="admin.php?f=template&type=edition">edition</a></li>  
    </ul>
</div>

<style>
ul.tmep {
 list-style-type:none;
 }
ul.tmep  li {
 }
ul.tmep li a {
 display:block;
 float:left;   
 width:100px;
 background-color:#ccc;
 color:black;
 text-decoration:none;
 text-align:center;

 border:1px solid #666;
 /*pour avoir un effet "outset" avec IE :*/
 border-color:#DCDCDC;
 }
</style>

<div align="center">
    <ul class="tmep">                               
        <li><a href="admin.php?f=template&type=selection">Home</a></li>  
        <li><a href="admin.php?f=template&type=edition">Search</a></li>  
        <li><a href="admin.php?f=template&type=edition">Single</a></li>  
        <li><a href="admin.php?f=template&type=edition">Page</a></li>  
        <li><a href="admin.php?f=template&type=edition">Header</a></li>  
        <li><a href="admin.php?f=template&type=edition">Footer</a></li>  
        <li><a href="admin.php?f=template&type=edition">Sidebar</a></li>  
    </ul>
</div>
<br /><br />
<?php                              
     
    $result = mysql_query("SELECT COUNT(*) AS total_titre from ".$mysql_table_prefix."links where link_id IS NOT NULL ".$sql_line."");
    echo mysql_error();
    $row=mysql_fetch_array($result);
    echo "<br><b>".$row[total_titre]."</b><br>";


	$result=mysql_query("SELECT link_id".$sql_select." from ".$mysql_table_prefix."links where link_id IS NOT NULL ".$sql_line." LIMIT 100");
 	while($row=mysql_fetch_array($result)){
 			$z=0;
 			$table_data.="<tr>";
 			while($z<$y){
 							$line.=$row[$var_toprint[$z]].'~~';
 							$table_data.="<td><a href=\"".$row[$var_toprint[$z]]."\">".$row[$var_toprint[$z]]."</a></td>";
 							$z++;
 							//echo "-".$z."-".$row[$var_toprint[$z]];

 			}
 			$table_data.="</tr>";
 			$line.='<br />'; 	}


        
?>
<div align="center">
<form type="post" action="admin.php?f=template">
<table><tr><td valign="top" width="180">
<?php echo $html; ?>
</td></tr></table>


<input type="submit" value="Filtrer">										
</form>														
</div>
<table>
<?php echo $table_title." ".$table_data; ?>
</table>

IFRAME src="http://yulwatch.ca/" width=1050 height=800 scrolling=auto frameborder=1>/IFRAME>











