<?php 
 
 include "crawl/settings/database.php";
 mysql_connect("localhost", $DbUser, $DbPassword) or die(mysql_error()); 
 mysql_select_db($_GET[db]) or die(mysql_error()); 
 
 $field["links"]="url";
 $field["temp"]="link";
 $sql_col["links"]=", link_id";
 $sql_col["temp"]="";
 
 $urldata="temp.php?db=$_GET[db]&find=$_GET[find]&table=$_GET[table]";
 



// retour d'action reload de la page sans les actions

if($_GET[action]=='delete'){
		$e=mysql_query("DELETE FROM `$_GET[table]` WHERE `".$field[$_GET[table]]."` LIKE '%$_GET[find]%'");
		header('http://localhost:8888/webproject/'.$urldata);
}
if($_GET[action]=='insert'){
		$e=mysql_query("INSERT INTO links (`url`) VALUES ($_GET[find])");
		header('http://localhost:8888/webproject/'.$urldata);
}

if($_GET[action]=='notdelete'){
		$e=mysql_query("DELETE FROM `$_GET[table]` WHERE `".$field[$_GET[table]]."` NOT LIKE '%$_GET[find]%'");
		header('http://localhost:8888/webproject/'.$urldata);
}


 ?>
 <style>
 body,td { background: #fcfcfc; color: #333; font-family: Verdana; font-size: 9pt; line-height: 19px; }
 a { text-decoration: none; color: #333; }
 table {
 	border-top: 1px solid #333;
 }
 ul#page li{
 	padding: 4px;
 	height: 20px;
 	width: 20px;
 	margin: 3px;
 	text-align: center;
 	border: 1px solid #ccc;
 	margin-left:2px;
 	float:left; /*pour IE*/
 }
 ul#page li.over{
 	border: 1px solid #e73223;
 }
 ul#page{
 clear: both;
 	padding:0;
 	 margin:0;
 	 list-style-type:none;
 }
 a:hover{
   color: #e73223;
 }
 td{
 	height: 50px;
 	border-bottom: 1px solid #aaa;
 }
 tr{
 	
 }
 </style>
 
 
 &nbsp;&nbsp;<a href="<?php echo $urldata; ?>&table=links">LINK</a>&nbsp;&nbsp;<a href="<?php echo $urldata; ?>&table=temp">TEMP</a><hr>
 
 <?php
 
 
 $sq="SELECT COUNT(DISTINCT `".$field[$_GET[table]]."`) AS `numb` FROM `$_GET[table]` WHERE `".$field[$_GET[table]]."` LIKE '%$_GET[find]%' ORDER BY `".$field[$_GET[table]]."`";
 $e=mysql_query($sq);
 echo $sq."<br><br>";
 $r=mysql_fetch_array($e);
 echo "DISTINCT : $r[numb]";
 ?> 
 &nbsp;&nbsp;&nbsp;&nbsp;
 <a href="<?php echo $urldata; ?>&action=clear">CLEAR</a>
 <form action="<?php echo $urldata; ?>" method="GET">
 <input type="hidden" value="<?php echo $_GET[db]; ?>" name="db">
 <input type="hidden" value="<?php echo $_GET[table]; ?>" name="table">
 <input type="text" name="find" size="80" value="<?php echo $_GET[find]; ?>">
 Occurence : <input type="text" name="occ" size="2" value="<?php echo $_GET[occ]; ?>">
 &nbsp;&nbsp;&nbsp;&nbsp;
 <input type="submit" value="Search">
 &nbsp;&nbsp;&nbsp;&nbsp;
 <a href="<?php echo $urldata; ?>&action=insert">INSERT</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $urldata; ?>&action=delete">DELETE</a>&nbsp;&nbsp;&nbsp;&nbsp; <a href="<?php echo $urldata; ?>&action=notdelete">NOTDELETE</a><br><br><br>
 </form>
 
 
 
 
 <?php 
 
 if(!$_GET[nbppage])$nbppage=12000;
 $total=$r[numb];
 $y=0;
 $nbpage=$total/$nbppage;
 if(!$_GET[p]){$p=1;}else{$p=$_GET[p];}

 $start=($p-1)*$nbppage;
 
 $class[$p]="over";
 while ($y<=$nbpage){
 		$y++;
 		$pri.="<li class='".$class[$y]."'><a href='<?php echo $urldata; ?>?p=$y'>$y</a></li>";
 }
 
 
 
 
			 
			 
			 //echo "SELECT * FROM `links` ORDER BY `url` ASC LIMIT 0, ".$nbppage."";//
			  $e=mysql_query("SELECT DISTINCT `".$field[$_GET[table]]."` ".$sql_col[$_GET[table]]." FROM `$_GET[table]`  WHERE `".$field[$_GET[table]]."` LIKE '%$_GET[find]%' ORDER BY `".$field[$_GET[table]]."` ASC LIMIT ".$start.", ".$nbppage."");
			  echo "<div style='clear:both;'> <table cellpadding='6' width='100%'>";
			  $x=0;
			  echo "<tr>
			  		<td align='center' width='25'>ID</td>
			  		<td width='25'>NB</td>
			  		<td width='40'>URL</td>
			  		<td width='500'>URL</td>
			  		<td width='40'>FB:TT</td>
			  		<td width='40'>FB:CM</td>
			  		<td width='40'>FB:CL</td>
			  		<td width='40'>FB:LK</td>
			  		<td width='40'>TW</td>
			  		<td width='40'>GO</td>
			  		<td width='40'>LK</td>
			  		<td width='40'>ST</td>
			  		<td width='40'>PN</td>
			  		</tr>";
			  		
			  $x=$start;
			  while ($r=mysql_fetch_array($e)) {
			  
			  
			  $sqx="SELECT COUNT(DISTINCT `".$field[$_GET[table]]."`) AS `numb` ".$sql_col[$_GET[table]]." FROM `$_GET[table]` WHERE `".$field[$_GET[table]]."` LIKE '%".$r[$field[$_GET[table]]]."%' ORDER BY `".$field[$_GET[table]]."`";
			  $ex=mysql_query($sqx);
			  //echo $sqx."<br><br>";
			  $rx=mysql_fetch_array($ex);
			   
			  if($rx[numb]>1){$countx="<b>".$rx[numb]."</b>";}else{$countx="&nbsp;";}
			  
			  
			   
			  $sw=0;
			  
					if($sw || $_GET[occ]){ 
					  if($sw || substr_count($r[$field[$_GET[table]]], '/')==$_GET[occ]){
					  
					  	$soc=socount($r[$field[$_GET[table]]],$r[link_id]);					  
						$x++;
					  	echo "<tr>
					  		<td align='right'>".$x."</td>
					  		<td align='center'><a href='".$urldata."&find=".$r[$field[$_GET[table]]]."'>".$countx."</a></td>
					  			<td><a href='".$urldata."&action=insert'>IN</a>&nbsp;
					  			<a href=''>CO</a>&nbsp;
					  			<a href='".$urldata."&find=".$r[$field[$_GET[table]]]."'>FD</a></td>
					  			<td ><a href='".$r[$field[$_GET[table]]]."' target='new'>".$r[$field[$_GET[table]]]."</a>&nbsp;&nbsp;&nbsp;&nbsp;
					  			      </td>".$soc."</tr>";
					  }
					}else{
						$x++;
						echo "<tr>
							<td align='right'>".$x."</td>
							<td align='center'><a href='".$urldata."&find=".$r[$field[$_GET[table]]]."'>".$countx."</a></td>
								<td><a href='".$urldata."&action=insert'>INS</a>&nbsp;&nbsp;
								<a href=''>COUNT</a>&nbsp;&nbsp;
								<a href='".$urldata."&find=".$r[$field[$_GET[table]]]."'>FIND</a></td>
								<td ><a href='".$r[$field[$_GET[table]]]."' target='new'>".$r[$field[$_GET[table]]]."</a>&nbsp;&nbsp;&nbsp;&nbsp;
								      </td>".$soc."</tr>";
										
					
					
					}
					
					  
			  }
			  
			  
			  
			  
			  echo "</table></div>";
			  
			 echo "<hr><br><div style='clear:both;height:70px;'><ul id='page'>".$pri."</ul></div>";
			 
			 
  
 
 
 function socount($url,$id) {
 		 
		$json = file_get_contents("http://api.sharedcount.com/?url=".rawurlencode($url));
		$counts = json_decode($json, true);
	  $cols=array('StumbleUpon','Reddit','Facebook_commentsbox_count','Facebook_click_count','Facebook_total_count','Facebook_comment_count','Facebook_like_count','Facebook_share_count','Delicious','GooglePlusOne','Buzz','Twitter','Diggs','Pinterest','LinkedIn');
		 
		$cols_cn=array($counts["StumbleUpon"],$counts["Reddit"],$counts["Facebook"]["commentsbox_count"],$counts["Facebook"]["click_count"],$counts["Facebook"]["total_count"],$counts["Facebook"]["comment_count"],$counts["Facebook"]["like_count"],$counts["Facebook"]["share_count"],$counts["Delicious"],$counts["GooglePlusOne"],$counts["Buzz"],$counts["Twitter"],$counts["Diggs"],$counts["Pinterest"],$counts["LinkedIn"]);
		 
		$y=0; 
		while($y<15){
					
					$co.="`".$cols[$y]."`=".$cols_cn[$y].", ";
					$y++;
					}
		
		
		$ee=mysql_query("UPDATE links SET $co `fulltxt`='1' WHERE `link_id`=$id");
		// echo "UPDATE links SET ".$co." `fulltxt`='1' WHERE `link_id`=".$id."<br><br>";
		 
   		return "<td>" .$counts["Facebook"]["total_count"]."</td><td>" .$counts["Facebook"]["click_count"]."</td><td>" .$counts["Facebook"]["comment_count"]."</td><td>" .$counts["Facebook"]["like_count"]."</td><td>". $counts["Twitter"]."</td><td>". $counts["GooglePlusOne"]."</td><td>". $counts["LinkedIn"]."</td><td>". $counts["StumbleUpon"]."</td><td>". $counts["Pinterest"]."</td>";
 

}
 
// echo "<br>&nbsp;<br>&nbsp;<div style='clear:both;height:70px;'><ul id='page'>".$pri."</ul></div>";
 
 ?>
 
