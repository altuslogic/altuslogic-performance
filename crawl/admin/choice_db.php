<table width="98%"><tr><td valign="top" width="50%">
<div style="line-height:20pt;">

<?php

    if (isset($_GET['action']) && $_GET['action']=='delete'){
        mysql_query("DROP DATABASE IF EXISTS $nomBase");
    }

    echo list_db_spider()."</div>";
    $success = mysql_select_db ($nomBase);  

    function list_db_spider(){

        global $DbUser,$DbPassword,$DbHost,$nomBase;

        $link = mysql_connect($DbHost, $DbUser, $DbPassword);
        $db_list = mysql_list_dbs($link);
        $out_print = "";
        $out_print.="<table width=\"100%\" border=0 cellspacing=0 cellpadding=0 align=\"center\">";
        $table=array("keywords","images","links","temp","sites","domains");
        $x=0;
        $out_print.="<tr><td class='greyHeading'><b>Databases<b></td>";

        while ($table[$x]) {   
            $out_print .= "<td class='greyHeading' align='center'><b>".$table[$x]."</b></td>";
            $x++;
        } 
        $out_print.="<tr>";


        $bgcolor='white';
        while ($row = mysql_fetch_object($db_list)) {
            if ($row->Database==$nomBase){
                $out_print .= "<tr><td class=$bgcolor><b>".$row->Database."</b></td>";  
                $success = mysql_select_db ($row->Database);

            }
            else {
                $out_print.= "<tr><td class=$bgcolor><a href=\"?nomBase=".$row->Database."\">".$row->Database."</a></td>";
                $success = mysql_select_db ($row->Database);
                //$tables_crawl= new array();


            }   $x=0;
            while ($table[$x]) {   
                $list_key=mysql_query("SELECT COUNT(*) as compte FROM ".$row->Database.".".$table[$x]."");
                $row_key = mysql_fetch_array($list_key);
                $out_print .= "<td class=$bgcolor align='center'>".$row_key[compte]."</td>";
                $x++;
            } 

            $out_print.="</tr>";
            if ($bgcolor=='grey') {
                $bgcolor='white';
            } else {
                $bgcolor='grey';
            }      
        } 
        $out_print.="</table><form method='post' action='?action=delete'>
        <input type='submit' id='submit' value='Delete' onclick='return confirm(\"Are you sure you want to delete the database $nomBase ?\");'></form>";

        return $out_print;

    }

?>
</td>