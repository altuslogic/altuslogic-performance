<?php		$action=$_GET['action'];
   			$count=0;
   			
   	
   
   
     $SESSION_NAME = "expo";
           session_name($SESSION_NAME);
           session_start();
           $MAX_UPLOAD_SIZE = 100000;
           $MD5_PREFIX = "astrochew_is_king_of_security";
           
   $DbHost     = "localhost"; // The host where the MySQL server resides
   $DbDatabase = "crawl_annuairexagence"; // The database you are going to use
   $DbUser     = "root"; // Username
   $DbPassword = "d3f4ult"; // Password
   
   
   
                     if(!mysql_connect($DbHost,$DbUser,$DbPassword))
                     {
             $NOTCONNECTED = TRUE;
                     }
                     if(!mysql_select_db($DbDatabase))
                     {
             $NOTCONNECTED = TRUE;
                     }
		
   			 
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>: money : </title>
<meta name="keywords" content="Web browsing optimization, AI related software engineering">
<meta name="description" content="Web browsing optimization, AI related software engineering">
<style>
html,td { font-weight: normal;font-size: 8pt;color : #555; font-family: Verdana;}
#add { font-size: 7pt; color : #888;}
</style>
<script language="javascript" src="hover.js" type="text/javascript"></script>
</head>
<body>
<a href="?action=longto">set long to private</a><br>                   
<a href="?action=noadd">set noaddress to private</a><br>                   
<a href="?action=noadd_string">set to private sting</a><br>                   
<a href="?action=view_private">view private</a><br><br>                   
<a href="?action=view_street">view steet</a><br><br>  
<a href="?action=set_number_private">set_number_private</a><br><br>                  
<a href="?action=set_long_private">set_long_private</a><br><br>                  
<a href="?action=build_wordpress">build_wordpress</a><br><br>                  
<a href="?action=get_lat">get_lat</a><br><br>                  
<a href="?action=clean_long">clean_long</a><br><br>                  
<?php
                    // print of last visted (20)
                    if($sel_cat==null){
                    $result = mysql_query ( "SELECT * FROM `crawl_annuairexagence`.`links` WHERE `final_url` IS NOT NULL LIMIT 100");
                    }else{
                    //$result = mysql_query ( "SELECT * FROM `crawl_annuairexagence`.`links` WHERE `category`='$sel_cat'");
                    }
                    $print_clair.= "<table width=\"100%\"><tr>";   echo "<table>";  
                    while ($row=mysql_fetch_array($result)){
                                            $count++;
                                            $name =$row[name]; 
                                     
                                      if($action=='longto')     
                                            if(strlen($name)>100){ $count_l++; 
                                                        $name = substr($row[name], 0, 80); 
                                                        $result_up = mysql_query ( "UPDATE `location` SET `public`='no' WHERE `id`=$row[id]");
                                                        echo $count_l." - ".$row[id]." - ".$name." - ".$row[address]."<br>";  
                                                        }else{//$result_up = mysql_query ( "UPDATE `location` SET `public`='' WHERE `id`=$row[id]");
                                                        }
                                                        
                                      if($action=='noadd')     
                                            if(strlen($row[address])<13 || $row[ylocation]==null){ $count_l++; 
                                                        $name = substr($row[name], 0, 80); 
                                                        $result_up = mysql_query ( "UPDATE `location` SET `public`='no' WHERE `id`=$row[id]");
                                                        echo $count_l." - ".$row[id]." - ".$name." - <font color=\"#eee\">".$row[ylocation]."</font><br>"; 
                                                        }else{//$result_up = mysql_query ( "UPDATE `location` SET `public`='' WHERE `id`=$row[id]");                          
                                                        }
                                  
                                                           // Inc inc & A ' enr Enrg Enrg enrg LA DE                                              
                                   if($action=='noadd_string')     
                                            if(strpos($row[name]," Inc") || strpos($row[name]," inc") || strpos($row[name]," &") || strpos($row[name]," enr") || strpos($row[name]," Enr") || strpos($row[name]," Ltee") || strpos($row[name]," ltee") || strpos($row[name]," Enrg") || strpos($row[name]," LA ") || strpos($row[name],"LA ") || strpos($row[name]," DE ") || strpos($row[name],"A ") || strpos($row[name],"â€™t") || strpos($row[ylocation],"#") || strpos($row[ylocation],"â€™")){ $count_l++; 
                                                        $name = substr($row[name], 0, 80); 
                                                        $result_up = mysql_query ( "UPDATE `location` SET `public`='no' WHERE `id`=$row[id]");
                                                        echo $count_l." - ".$row[id]." - ".$name." - <font color=\"#eee\">".$row[ylocation]."</font><br>"; 
                                                        }else{//$result_up = mysql_query ( "UPDATE `location` SET `public`='' WHERE `id`=$row[id]");
                                                        }
                                                                                                                                                                                                                                                                                                                      
                                      if($action=='view_private')                                                                                                                                                                                                                                               
                                            if($row['public']=="no"){ $count_l++; 
                                                        $name = substr($row[name], 0, 80); 
                                                        //$result_up = mysql_query ( "UPDATE `location` SET `public`='no' WHERE `id`=$row[id]");
                                                        echo $count_l." - ".$row[id]." - ".$name." - <font color=\"#eee\">".$row[ylocation]."</font><br>"; 
                                                        }else{//$result_up = mysql_query ( "UPDATE `location` SET `public`='' WHERE `id`=$row[id]");
                                                        }
                                                        
                                                                                                                                                                                                                                                                                                                                          
                                      if($action=='get_lat')         //$row['latitude']==""    $row['yadd']==""                                                                                                                                                                                                                                  
                                            if($row['yadd']==""){ $count_l++; 
                                                        geoLocateUser($row[ylocation]." Montreal", $row[id]);
                                                        $name = substr($row[name], 0, 80); 
                                                        //$result_up = mysql_query ( "UPDATE `location` SET `public`='no' WHERE `id`=$row[id]");
                                                        echo $count_l." | ".$name.""; 
                                                        }else{echo "<br>Region : ".$row[yregion]." -- ".$row[yadd]." -- ".$row[latitude]." -- ".$row[longitude]." -- ".$row[yville]." -- ".$row[ygoogle]." -- ".$row[ycp];
                                                        }
                                                                               //         && $row['category']=="restaurant"
                                     if($action=='build_wordpress')     
                                            if($row['public']==""){ $count_l++; 
                                                        $name = substr($row[name], 0, 80); 
                                                        //$result_up = mysql_query ( "UPDATE `location` SET `public`='no' WHERE `id`=$row[id]");
                                                        echo "print to post : ".$count_l." - ".$row[id]." - ".$row[final_contact]." - <font color=\"#eee\">".$row[final_telephone]."</font> - ".$row[final_adresse]."<br>"; 
                                                        
                                                       
                                                      echo "INSERT INTO `wp_posts` ( `ID` ,`post_author` ,`post_date` ,`post_date_gmt` ,`post_content` ,`post_title` ,`post_excerpt` ,`post_status` ,`comment_status` ,`ping_status` ,`post_password` ,`post_name` , `to_ping` ,`pinged` , `post_modified` ,`post_modified_gmt` , `post_content_filtered` ,`post_parent` ,`guid` ,`menu_order` , `post_type` ,`post_mime_type` , `comment_count`, `list_id` ) VALUES (NULL , '0', '2012-05-04 17:36:08', '2012-05-04 17:36:08', '".(addslashes($row[final_adresse]))."<br> ".$row[final_telephone]."', '".(addslashes($row[final_title]))."', '".(addslashes($row[final_adresse]))."', 'publish', 'open', 'open', '', '".friendly_seo_string($row[final_title]."-".$row[final_adresse])."', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '0', '', '0', 'post', '', '0','$row[link_id]'";
                                                        
                                
                                                           $result_up = mysql_query ("INSERT INTO `wp_posts` ( `ID` ,`post_author` ,`post_date` ,`post_date_gmt` ,`post_content` ,`post_title` ,`post_excerpt` ,`post_status` ,`comment_status` ,`ping_status` ,`post_password` ,`post_name` , `to_ping` ,`pinged` , `post_modified` ,`post_modified_gmt` , `post_content_filtered` ,`post_parent` ,`guid` ,`menu_order` , `post_type` ,`post_mime_type` , `comment_count`, `list_id` ) VALUES (NULL , '0', '2012-05-04 17:36:08', '2012-05-04 17:36:08', '".(addslashes($row[final_adresse]))."<br> ".$row[final_telephone]."', '".(addslashes($row[final_title]))."', '".(addslashes($row[final_adresse]))."', 'publish', 'open', 'open', '', '".friendly_seo_string($row[final_title]."-".addslashes($row[final_adresse]))."', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '0', '', '0', 'post', '', '0','$row[link_id]' )");
                                                        
                                                           $result_up2 = mysql_query ("SELECT ID FROM `wp_posts` ORDER BY `ID` DESC LIMIT 1");
                                                           $row2=mysql_fetch_array($result_up2); 
                                                           $result_up = mysql_query ("INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES
                                                                                                                ($row2[ID], 'phone', '$row[final_telephone]'4),
                                                                                                                ($row2[ID], 'woo_maps_zoom', '15'),
                                                                                                                ($row2[ID], 'woo_maps_lat', '$row[final_latitude]'),      
                                                                                                                ($row2[ID], 'place', '".($row[final_title])."'),
                                                                                                                ($row2[ID], 'woo_maps_type', 'G_NORMAL_MAP'),
                                                                                                                ($row2[ID], 'woo_maps_address', '".($row[final_adresse])."'),
                                                                                                                ($row2[ID], 'woo_maps_long', '".str_replace("<coordinates>","",$row[final_latitude])."'),
                                                                                                                ($row2[ID], 'address', '". ($row[final_adresse])."'),
                                                                                                                ($row2[ID], 'image', ''),
                                                                                                                ($row2[ID], 'force_excerpt', 'false'),
                                                                                                                ($row2[ID], 'seo_follow', 'true'),
                                                                                                                ($row2[ID], 'woo_maps_enable', 'on');");
                                                                                                                                                                    
                                                           $result_up2 = mysql_query ("SELECT ID FROM `wp_posts` ORDER BY `ID` DESC LIMIT 1");
                                                           $row2=mysql_fetch_array($result_up2); 
                                                            
                                                      /*    iff new 
                                                            INSERT INTO `wp_terms` (`term_id`, `name`, `slug`, `term_group`) VALUES (null, 'internet', 'internet', 0); 
                                                            INSERT INTO `wp_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES (null, null, 'post_tag', '', 0, 1);

                                                      /*    or get id 
                                                            INSERT INTO `wp_term_relationships` (`$row2[ID]`, `term_taxonomy_id`, `term_order`) VALUES (null, 2, 0);*/ 
                                                            
                                                        }else{
                                                        
                                                        //$result_up = mysql_query ( "UPDATE `location` SET `public`='' WHERE `id`=$row[id]");
                                                        
                                                        }                 
                                                         
                                     if($action=='set_long_private')     
                                            if(strlen($row[name])>='70'){ $count_l++; 
                                                        $name = substr($row[name], 0, 200); 
                                                        $result_up = mysql_query ( "UPDATE `location` SET `public`='no' WHERE `id`=$row[id]");
                                                        echo $count_l." - ".$row[id]." - ".$name." - <font color=\"#eee\">".$row[ylocation]."</font><br>"; 
                                                        }else{//$result_up = mysql_query ( "UPDATE `location` SET `public`='' WHERE `id`=$row[id]");
                                                        }
                                
                                     if($action=='clean_long')     
                                            if(1){ $count_l++;                        //   http://www.yulwatch.com/getpointceneter.php?item_sel=%3CPostalCodeNumber%3EH2V%3C/PostalCodeNumber%3E&cols_sel=ycp&publish=
                                                       // $name = substr($row[name], 0, 200); 
                                                       $ycp =str_replace("<postalcodenumber>", "", $row[ycp]);
                                                       $ycp =str_replace("</postalcodenumber>", "", $ycp);
                                                        $result_up = mysql_query ( "UPDATE `location` SET `cp`='$ycp' WHERE `id`=$row[id]");
                                                        echo ", ".$name; 
                                                        }else{//$result_up = mysql_query ( "UPDATE `location` SET `public`='' WHERE `id`=$row[id]");
                                                        }
                                                        
                                    if($action=='set_number_private')     
                                            if(substr($row[name], 0, 1)<'999'){ $count_l++; 
                                                        //$name = ; 
                                                        $result_up = mysql_query ( "UPDATE `location` SET `public`='no' WHERE `id`=$row[id]");
                                                        echo $count_l." - ".$row[id]." - ".$name." - <font color=\"#eee\">".$row[ylocation]."</font><br>"; 
                                                        }else{//$result_up = mysql_query ( "UPDATE `location` SET `public`='' WHERE `id`=$row[id]");
                                                        }   
                                                                         
                                      if($action=='view_street'){ 
                                               
                                            if($row['public']==""){ $count_l++; 
                                            
                                                  $done=0;
                                                  $p1=$row[p1]; $p2=$row[p2]; $p3=$row[p3]; $p4=$row[p4]; $p5=$row[p5];$p6=$row[p6];
                                                  if($row[p1]!=''){$done++;$searchp1=1;} if($row[p2]!=''){$done++;$searchp2=1;} if($row[p3]!=''){$done++;$searchp3=1;} if($row[p4]!=''){$done++;$searchp4=1;} if($row[p5]!=''){$done++;$searchp5=1;}
                                                  $pi=explode(' ',$row[address]); 
                                            
                                            
                                                  if($searchp1!=1){    // if nbporte en premier                 sinon ...
                                                                       if($pi[1]<50000){ $p1=$pi[1]; $used[1]=1;  }else{       }              
                                                  
                                                  }
                                            
                                            
                                                   if($searchp2!=1){    
                                                       
                                                   $piece_counter=2; 
                                                   while($p2==null && $piece_counter<9){   
                                                                       $piece=$pi[$piece_counter];      
                                                                       if($piece=="Chemin" || $piece=="ch" || $piece=="CH" || $piece=="chemin" || $piece=="Ch"){$p2="Chemin";$used[$piece_counter]=1;}
                                                                       if($piece=="rue" || $piece=="Rue" || $piece=="RUE" ){$p2="rue";$used[$piece_counter]=1;} 
                                                                       if($piece=="av" || $piece=="Av" || $piece=="Av." || $piece=="Avenue" || $piece=="Ave" || $piece=="avenue" || $piece=="av."){$p2="Avenue";$used[$piece_counter]=1;} 
                                                                       if($piece=="boul" || $piece=="boul." || $piece=="Boul" || $piece=="boulevard" || $piece=="Boulevard" || $piece=="BOULEVARD"){$p2="Boulevard";$used[$piece_counter]=1;} 
                                                                       if($piece=="pl" || $piece=="place" || $piece=="Place" || $piece=="Pl" || $piece=="PL"){$p2="Place";$used[$piece_counter]=1;} 
                                                                       $piece_counter++; 
                                                   }
                                                 }  
                                                 
                                                 
                                                 if($searchp4!=1){    
                                                       
                                                   $piece_counter=3; 
                                                   while($p4==null && $piece_counter<9){   
                                                                       $piece=$pi[$piece_counter]; 
                                                                       
                                                                       if($piece=="Est" || $piece=="E" || $piece=="est" || $piece=="East"){$p4="Est"; $used[$piece_counter]=1; }
                                                                       if($piece=="Ouest" || $piece=="O" || $piece=="ouest" || $piece=="W" ){$p4="Ouest";$used[$piece_counter]=1;} 
                                                                       $piece_counter++;
                                                    }                                        
                                                  }
                                            
                                            
                                             if($searchp3!=1){    
                                                       
                                                   $piece_counter=0; 
                                                   while($piece_counter<9){   
                                                                       $piece=$pi[$piece_counter]; 
                                                                       if($used[$piece_counter]!=1 && !strpos($piece,'ontr'))$p3.=$piece." ";
                                                                       $piece_counter++;
                                                                       
                                                    }                  
                                                                   
                                                  }
                                                   $used=null;
                                            
                                                        $name = substr($row[name], 0, 40); 
                                                        //$result_up = mysql_query ( "UPDATE `location` SET `public`='no' WHERE `id`=$row[id]");
                                                        
                                                        echo "<tr><td>".$done."/5 </td><td>".$count_l." </td><td> ".$row[id]." </td><td> ".$name." </td><td> ".$row[tel]." </td><td> <font color=\"#eee\">".$row[address]."</font></td>
                                                                  <td>".$p1." ".$p2." ".$p3." ".$p4." ".$p5." ".$p6." ".$p7." </td>
                                                        </tr>"; 
                                                        }else{//$result_up = mysql_query ( "UPDATE `location` SET `public`='' WHERE `id`=$row[id]");
                                                        }
                                                 
                                      }      
                                      
                                      
                                                       
                                           // $yname =$row[yname];
                                           // if(strlen($yname)>40)$yname = substr($row[yname], 0, 40);    
                                            $print_clair.= "<td width=\"25%\">".$name."<br><div id=\"add\">".$row[ylocation]."<br><a href=\"$row[web]\">$row[web]</a></div><br></td>";
                                            if($count%4==0)$print_clair.= "</tr><tr>";
                                    }
                                    $print_clair.= "</tr></table>";
                                    
                                    
function geoLocateUser($location,$id) {
  $location = urlEncode($location); // encode url here...
 
  $request  = 'http://maps.google.com/maps/geo?';
  $request .= 'q='.$location.'&';
  $request .= 'key=ABQIAAAAnNatFxH2LX42s_QZL51BihRZv3i58mVijuzMpJ5h9q3dLezH1BQHCvYAD31aQAXSDoZp2xaPqFSQWw'.'&';
  $request .= 'output=xml'.$config_ga['format'].'&';
  $request .= 'oe=utf8';
  
                                                       
  $response  = file_get_contents(''.$request.'');
  echo   "resp: ".$response;
  
   preg_match_all( "/\<Response\>(.*?)\<\/Response\>/s",$response, $bookblocks );
  foreach( $bookblocks[1] as $block )
  {
     //  preg_match_all( "/\<address\>(.*?)\<\/address\>/",$block,$author );
       preg_match_all( "/\<Point\>(.*?)\<\/Point\>/",$block,$title );
       preg_match_all( "/\<address\>(.*?)\<\/address\>/",$block,$add );  
       preg_match_all( "/\<ThoroughfareName\>(.*?)\<\/ThoroughfareName\>/",$block,$fare );
       preg_match_all( "/\<PostalCode\>(.*?)\<\/PostalCode\>/",$block,$pc );                    
       preg_match_all( "/\<LocalityName\>(.*?)\<\/LocalityName\>/",$block,$loc );
       preg_match_all( "/\<SubAdministrativeAreaName\>(.*?)\<\/SubAdministrativeAreaName\>/",$block,$adm );
    //    echo( "<br><b>Adress : ".$title[1][0]."</b>" );    
    //    echo( "<br><b>Adress : ".$title[1][0]."</b>" );    
        $pi2=explode(',',$title[1][0]);
        $lat=$pi2[1];
        $long=$pi2[0];
                      $adma=$adm[1][0];
                      $loca=$loc[1][0];
                      $farea=$fare[1][0];
                      $pca =$pc[1][0];
                      $adda=$add[1][0];
        echo " (l.".$lat.") ";
       $result_upt = mysql_query ( "UPDATE `location` SET `latitude`='$lat',`longitude`='$long',`ygoogle`='$adda',`ycp`='$pca',`yville`='$loca',`yadd`='$farea',`yregion`='$adma' WHERE `id`=$id");
   }

  
  
 // $result = simplexml_load_string($response);
 // $request="";
 //   $latLong = (string)$result->Response->Placemark->Point->coordinates;
          // echo "latlong: ".$latLong;
  return $latLong;
}      



function normaliza ($string){
    $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ
ßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
    $b = 'aaaaaaaceeeeiiiidnoooooouuuuy
bsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
    $string = utf8_decode($string);    
    $string = strtr($string, utf8_decode($a), $b);
    $string = strtolower($string);
    return utf8_encode($string);
} 
                                                   
function friendly_seo_string($string, $separator = '-')
{
    
           
   $string=normaliza($string);
    
$string = trim($string);

$string = strtolower($string); // convert to lowercase text

// Recommendation URL: http://www.webcheatsheet.com/php/regular_expressions.php

// Only space, letters, numbers and underscore are allowed

$string = trim(ereg_replace("[^ A-Za-z0-9_]", " ", $string));

/*

"t" (ASCII 9 (0x09)), a tab.
"n" (ASCII 10 (0x0A)), a new line (line feed).
"r" (ASCII 13 (0x0D)), a carriage return. 

*/

//$string = ereg_replace("[ tnr]+", "-", $string);

$string = str_replace(" ", $separator, $string);

$string = ereg_replace("[ -]+", "-", $string);

return $string;
}

              
                       echo "</table>";                   
  
?>                                  














