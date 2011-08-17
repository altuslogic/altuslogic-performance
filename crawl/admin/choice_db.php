<div style="margin:50px;line-height:20pt;">
<form action="">
    <input type="text" size="10" id="host">
    <input type="text" size="10" id="userdb">
    <input type="password" size="10" id="passdb">
    <input type="submit" value="Create Database">
</form>
<a href="install.php">install</a><br />

<?php

    echo list_db_spider()."</div>";
    $success = mysql_select_db ($nomBase);  

    function list_db_spider(){

        global $DbUser,$DbPassword,$DbHost,$nomBase;

        $link = mysql_connect($DbHost, $DbUser, $DbPassword);
        $db_list = mysql_list_dbs($link);
        $out_print = "";
        $out_print.="<table width=\"100%\">";
        $table=array("keywords","links","temp","sites","domains");
        $x=0;
        $out_print.="<tr><td>Table names</td>";

        while ($table[$x]) {   
            $out_print .= "<td><b>".$table[$x]."</b></td>";
            $x++;
        } 
        $out_print.="<tr>";



        while ($row = mysql_fetch_object($db_list)) {
            if ($row->Database==$nomBase){
                $out_print .= "<tr><td>".$row->Database."</b></td>";  
                $success = mysql_select_db ($row->Database);

            }
            else {
                $out_print.= "<tr><td><a href=\"?nomBase=".$row->Database."\">".$row->Database."</a></td>";
                $success = mysql_select_db ($row->Database);
                //$tables_crawl= new array();


            }   $x=0;
            while ($table[$x]) {   
                $list_key=mysql_query("SELECT COUNT(*) as compte FROM ".$row->Database.".".$table[$x]."");
                $row_key = mysql_fetch_array($list_key);
                $out_print .= "<td>".$row_key[compte]."</td>";
                $x++;
            } 

            $out_print.="</tr>";      
        } 
        $out_print.="</table>";

        return $out_print;

    }

?>
