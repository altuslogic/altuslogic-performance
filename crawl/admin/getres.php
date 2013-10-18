<?php

   $action=$_GET['action'];
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
                     
  
   $cols_sel=$_GET[cols_sel];     
   $item_sel= $_GET[item_sel];
   
   $sel_select="AND `".$cols_sel."`='".$item_sel."'"; 
   $sel_select2="`$cols_sel`='$item_sel' AND"; 
   $sel_select3="WHERE `$cols_sel`='$item_sel'"; 
   
   $publish=$_GET[publish];
 /*  $result=mysql_query("SELECT * FROM `location_center` WHERE `name`='$item_sel'");
                                            
   $rowx=mysql_fetch_array($result);
   $center_lat=$rowx[latitude];
   $center_lng=$rowx[longitude]; 
   */
   $zoom=11;   
   if($cols_sel=="cp"){
                          $zoom=15;
                          $center=$cols_sel;            
                      } 
                      
   if($center_lat==""){       $center_lat="45.5356733";
                              $center_lng="-73.6020229";
                      } 
    /*
     $col="all";$results=mysql_query("SELECT count(*) as count FROM business_jp  WHERE `public`!='0' ORDER BY count DESC");$rows=mysql_fetch_array($results);$va[$col]=$rows[count];                                 
     $col="allx";$results=mysql_query("SELECT count(*) as count FROM business_jp  WHERE `public`!='0' $sel_select ORDER BY count DESC");$rows=mysql_fetch_array($results);$va[$col]=$rows[count];                                 
     $pourcent[$col]=round($va[$col]/$va['all']*100,1);
      $col="alln";$results=mysql_query("SELECT count(*) as count FROM business_jp  WHERE `public`!='0' $sel_select AND (`contxprofile`='true' OR `contxphoto`='true' OR `contxurl`='true' OR `contxdspad`='true' OR `contxlogo`='true' OR `contxvideo`='true') ORDER BY count DESC");$rows=mysql_fetch_array($results);$va[$col]=$rows[count];                                 
     $pourcent[$col]=round($va[$col]/$va['all']*100,1);
     $col="contxprofile";$results=mysql_query("SELECT count(*) as count FROM business_jp  WHERE `public`!='0' AND `$col`='true' $sel_select GROUP BY $col ORDER BY count DESC");$rows=mysql_fetch_array($results);$va[$col]=$rows[count];                                 
     $pourcent[$col]=round($va[$col]/$va['all']*100,1);
     $col="contxphoto";$results=mysql_query("SELECT count(*) as count FROM business_jp  WHERE `public`!='0' AND `$col`='true' $sel_select  GROUP BY $col ORDER BY count DESC");$rows=mysql_fetch_array($results);$va[$col]=$rows[count];                                 
     $pourcent[$col]=round($va[$col]/$va['all']*100,1);
     $col="contxurl";$results=mysql_query("SELECT count(*) as count FROM business_jp  WHERE `public`!='0' AND `$col`='true' $sel_select GROUP BY $col ORDER BY count DESC");$rows=mysql_fetch_array($results);$va[$col]=$rows[count];                                 
     $pourcent[$col]=round($va[$col]/$va['all']*100,1);
     $col="contxdspad";$results=mysql_query("SELECT count(*) as count FROM business_jp  WHERE `public`!='0' AND `$col`='true' $sel_select GROUP BY $col ORDER BY count DESC");$rows=mysql_fetch_array($results);$va[$col]=$rows[count];                                 
     $pourcent[$col]=round($va[$col]/$va['all']*100,1);
     $col="contxlogo";$results=mysql_query("SELECT count(*) as count FROM business_jp  WHERE `public`!='0' AND `$col`='true' $sel_select GROUP BY $col ORDER BY count DESC");$rows=mysql_fetch_array($results);$va[$col]=$rows[count];                                 
     $pourcent[$col]=round($va[$col]/$va['all']*100,1);
     $col="contxvideo";$results=mysql_query("SELECT count(*) as count FROM business_jp  WHERE `public`!='0' AND `$col`='true' $sel_select GROUP BY $col ORDER BY count DESC");$rows=mysql_fetch_array($results);$va[$col]=$rows[count];                                 
     $pourcent[$col]=round($va[$col]/$va['all']*100,3);
     
    */ 
                  
       function  numb_count($col,$cols_sel,$item_sel,$publish,$col2){  
                        $sel_select="AND `".$cols_sel."`='".$item_sel."'"; 
                        $sel_select2="`$cols_sel`='$item_sel' AND"; 
                        $sel_select3="WHERE `$cols_sel`='$item_sel'"; 
   
     
                           $wi="350";
                   
                           echo " <div class=\"tab-page\"  style=\"overflow:auto;\"><h2 class=\"tab\">$col</h2> 
                                        <div \">
                                         <br><table><tr><td width=\"$wi\"><table width=\"$wi\">";                                                          
                           
                           $result=mysql_query("SELECT count($col) as count, $col, $col2 FROM links $sel_select3 GROUP BY $col ORDER BY count DESC LIMIT 800");
                         // echo "SELECT count($col) as count, $col, $col2 FROM business_jp $sel_select3 GROUP BY $col ORDER BY count DESC LIMIT 800<br>"; 
                           $f=0;
                          while ( ($row=mysql_fetch_array($result)) && $f<3000){  
                                                   $nn = substr($row[$col2], 0, 40); 
                                                   $f++;   if($item_sel==$row[$col2]){$class=" class=\"sel\" ";}
                                                if($row[$col]!=null)   echo "<tr><td>".$row[count]."</td><td> <a id=\"add\" href=\"http://184.107.136.98/~api/ink.php?wo=$item_sel&lo=$row[$col]\" target=\"new\">y</a> <a id=\"add\" href=\"http://184.107.136.98/~api/ink.php?wo=$item_sel&lo=$row[$col]\" target=\"new\">p</a> <a href=\"?item_sel=".$row[$col]."&cols_sel=$col&publish=".$_GET[publish]."\"$class>".$nn."</a></td></tr>";   
                           $class="";      
                           if($f%25==0)echo "</table></td><td width=\"$wi\"><table width=\"$wi\">";
                           }
                              echo "</table></td></tr></table></div> 
          </div>       ";   
                        
       }
                           
       function  numb_count_key($col,$cols_sel,$item_sel,$publish){  
                          
                           $wi="550";
                                    
                           echo "<div class=\"tab-page\"  style=\"overflow:auto;\"><h2 class=\"tab\">$col</h2> 
                                        <div style=\"width:8200px;\"><br><table><tr><td width=\"$wi\"><table >";                                                          
                           
                           $result=mysql_query("SELECT count($col) as count, $col, name FROM business_cat_id GROUP BY $col ORDER BY count DESC LIMIT 300");
                      
                           $f=0;
                          
                           while ( ($row=mysql_fetch_array($result)) && $f<3000){  
                                                   $nn = substr($row[name], 0, 60); 
                                                   $f++;   if($item_sel==$row[$col]){$class=" class=\"sel\" ";}
                                                if($row[$col]!=null)   echo "<tr><td>".$row[count]."</td><td> <a id=\"add\" href=\"http://184.107.136.98/~api/ink.php?wo=$item_sel&lo=$row[$col]\" target=\"new\">y</a> <a id=\"add\" href=\"http://184.107.136.98/~api/ink.php?wo=$item_sel&lo=$row[$col]\" target=\"new\">p</a> <a href=\"?item_sel=".$row[$col]."&cols_sel=$col&publish=".$_GET[publish]."\"$class>".$nn."</a></td></tr>";   
                           $class="";      
                           if($f%25==0)echo "</table></td><td width=\"$wi\"><table >";
                           }
                              echo "</table></td></tr></table></div> 
          </div>  ";   
                           } 
                           
                           
                           
                                      
       function  numb_count_city($col,$cols_sel,$item_sel,$publish){  
                                 $wi="550";
                                   
                           echo "<br><table><tr><td width=\"$wi\"><table >";                                                          
                                                                                                 //        WHERE `public`='' 
                           $result=mysql_query("SELECT count($col) as count, $col FROM business_jp GROUP BY $col ORDER BY count DESC");
                                              $f=0;
                          
                              
                           while ( ($row=mysql_fetch_array($result)) && $f<500){  
                                                   
                                               $resulty=mysql_query("SELECT * FROM business_jp WHERE `city`='$row[$col]' AND `pcode`!='' LIMIT 2");
       
                                               $rowy=mysql_fetch_array($resulty);    
                                                   $nn = substr($row[$col], 0, 60); 
                                                   $f++;   if($item_sel==$row[$col]){$class=" class=\"sel\" ";
                                                   
                                                   
                                                   }
                           $rwr=mysql_query("SELECT * FROM business_tag_search  WHERE `locate`='".$rowy[pcode]."' AND `name`='".$item_sel."'");
                           if(!($rr=mysql_fetch_array($rwr))){            $pp=" <a id=\"add\" href=\"http://184.107.136.98/~api/xml2.php?wo=".$item_sel."&lo=$rowy[pcode]\" target=\"new\">p</a>";}
                           $rwr=mysql_query("SELECT * FROM business_pj_search  WHERE `locate`='".$rowy[pcode]."' AND `name`='".$item_sel."'");
                           if(!($rr=mysql_fetch_array($rwr))){            $yy=" <a id=\"add\" href=\"http://184.107.136.98/~api/ink.php?wo=".$item_sel."&lo=$rowy[pcode]\" target=\"new\">y</a>";}
                                                                       
                           if($row[$col]!=null)   echo "<tr><td>".$row[count]."</td><td> $pp $yy <a href=\"?item_sel=".$row[$col]."&cols_sel=$col&publish=".$_GET[publish]."\"$class>".$nn.", $rowy[prov]</a></td></tr>";   
                           $class="";      
                           $pp='';
                           $yy='';
                           
                           if($f%25==0)echo "</table></td><td width=\"$wi\"><table >";
                           }
                              echo "</table></td></tr></table>";   
               }
      
                       
      function  numb_count_center($col,$cols_sel,$item_sel,$publish){  
                           
                           $wi="550";  
                           echo "<br><table><tr><td width=\"$wi\"><table >";
                                                                                                  
                           $result=mysql_query("SELECT count($col) as count, $col FROM location_center  GROUP BY $col ORDER BY count DESC");
                           $f=0;
                           while ( ($row=mysql_fetch_array($result)) && $f<3000){
                                                   $nn = substr($row[$col], 0, 60);
                                                $f++;   if($item_sel==$row[$col]){$class=" class=\"sel\" ";}
                                                if($row[$col]!=null)   echo "<tr><td>".$row[count]."</td><td><a href=\"?item_sel=".$row[$col]."&cols_sel=$col&publish=".$_GET[publish]."\"$class>".$nn."</a> </td></tr>";
                                                   
                                                $class="";
                                                      
                           if($f%25==0)echo "</table></td><td width=\"$wi\"><table >";
                           }
                              echo "</table></td></tr></table>";   
                           }
                       
                             

   
      function  showitems($cols_sel,$item_sel){ 
                            /*   $sel_select="AND `".$cols_sel."`='".$item_sel."'"; 
                               $sel_select2="`$cols_sel`='$item_sel' AND"; 
                               $sel_select3="WHERE `$cols_sel`='$item_sel'"; 
                             
                               $result=mysql_fetch_array(mysql_query("SELECT count(*) as count FROM `links`"));
                               $ps=$result['count'];
                               $result=mysql_fetch_array(mysql_query("SELECT count(*) as count FROM `links`"));
                               $as=$result['count'];
                             */  
                               // WHERE $sel_select2 WHERE $sel_select2 WHERE $sel_select2
                               $result=mysql_query("SELECT * FROM `links` ORDER BY `final_url` DESC LIMIT 100");
                               $aa.= "<br><b>$item_sel</b> ($as / $ps)<br> "; 
                    
                              while($row=mysql_fetch_array($result)){ 
                                  
                                     $name = utf8_encode(substr($row[final_titre], 0, 40)); 
                                     $lat=substr($row[final_titre], 0, 5);
                                     $long=substr($row[final_titre], 0, 5); 
                                     $email = str_replace('monmail +=', '', $row[final_email]);
									 $email = str_replace('"', '', $email);
									 $email = str_replace(';', '', $email);
									 $email = str_replace("'", '', $email);
									 $contact = utf8_decode($row[final_contact]);
									 $contact = str_replace("Ã«", 'ë', $row[final_contact]);
									 $contact = str_replace("Ã«", 'ë', $row[final_contact]);
									 $contact = str_replace("Ã«", 'ë', $row[final_contact]);
									 $contact = str_replace("Ã«", 'ë', $row[final_contact]);
									 $contact = str_replace("Ã«", 'ë', $row[final_contact]);
									 $contact = str_replace("Ã«", 'ë', $row[final_contact]);


                                
                                     $aa.= " <div id=\"sticker\"><div style=\"margin-bottom:3px;font-weight:bold;\">".$name."</div>".$row[final_telephone]."<br>".$contact."<br>".$email."
                                     <br><a href=\"".$row[final_url]."\">".$row[final_url]."</a><br><br></div>";

                              }
                            
                    return $aa;
      }                               
    
    include "view.php";
?>        


             
            
