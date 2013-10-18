<?php
header("Content-Type: text/plain");
function myTruncate($string, $limit, $break=".", $pad="...")
{
  $string = str_replace(array("\n", "\r"), '', $string);// return with no change if string is shorter than $limit
  if(strlen($string) <= $limit) return $string;
$string = str_split($string, 1000);
  return $string[0];
}   
        
    foreach ($_POST as $key=>$val){
  //      echo $key,"=",$val,"<br>";
        $$key = $val;
        
    }
   $col[0]=$col0;
   $col[1]=$col1;
   $col[2]=$col2;
   $col[3]=$col3;
   $col[4]=$col4;
   $col[5]=$coln;
   $col[6]=$colo;
    
     
        $result = mysql_query("SHOW columns FROM ".$mysql_table_prefix."links");
        echo mysql_error();
        $print_columns = "<option $selected value=''>Source column</option>";
        while ($tab = mysql_fetch_array($result)){
            //$selected = ($tab[0]==$site? "selected": "");
            
            $x=1;
            while($x<=6){  
            
                        if($col[$x]==$tab[0]){$sel=" SELECTED "; $select.='`'.$tab[0].'`,';}else{$sel="";}
				            $cols[$x] .= "<option  value='$tab[0]' $sel>$tab[0]</option>";
				            $x++;
            }
        }
        
        
        
       if($coln){$add="WHERE `".$coln."`!=''";}else{}
       if($colo){$order="ORDER BY `".$colo."`";}else{}
       					$result2 = mysql_query("SELECT $select link_id,title FROM links $add $order LIMIT 250");
         				echo mysql_error()."<br><br>SELECT $select link_id FROM links $add LIMIT 10";
        $y=1;
        				while ($table = mysql_fetch_array($result2)){
        					
        					$outputdata.="<div><b>".$y."</b></div>";
        					
        					$x=1;
        					while($x<5){ 
        					if($x==2){  $hide=" style='display:none;' ";    }else{    $hide="";  }
			        					$term=$table[$col[$x]];//(".strlen($term).")
			        					$outputdata .= "<div valign='top' class='t$x' $hide>".$term."</div>"; 
			        					$x++;
			        					}
					        	     	$outputdata.="<br><br>";
		        	     	     	    $y++; 
		        	     	     	               
        	            }
       
       
        
        
        
        
        
        
    ?><br><br><form action="" method="post"> 
    
   NOT NULL :<select name="coln" onchange="this.form.submit()">
     <option value=''>Source column</option>
     
          <?php echo $cols[5]; ?>
      </select><br>
   Order by :<select name="colo" onchange="this.form.submit()">
     <option value=''>Source column</option>
          <?php echo $cols[6]; ?>
      </select><br>
     <table border="0" width="100%" style="border: 1px solid #aaa;">
           <tr><td>
                   <select name="col1" onchange="this.form.submit()">
                   		<option value=''>Source column</option>
                        <?php echo $cols[1]; ?>
                    </select></td>
               <td>
                            <select name="col2" onchange="this.form.submit()">
                            <option value=''>Source column</option>
                            
                                 <?php echo $cols[2]; ?>
                             </select></td>
            <td>
                                         <select name="col3" onchange="this.form.submit()">
                                         <option value=''>Source column</option>
                                         
                                              <?php echo $cols[3]; ?>
                                          </select></td>
            <td>
                                         <select name="col4" onchange="this.form.submit()">
                                         <option value=''>Source column</option>
                                         
                                              <?php echo $cols[4]; ?>
                                          </select></td>
              </tr>
                       
          </form>
          
         
                       
           </table><br><br>
           <div style="float: left;">
            <?php echo $outputdata; ?>  
           </div> 
          