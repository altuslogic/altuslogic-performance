<?php

    function cleanForm () {
        global $mysql_table_prefix;
        $result = mysql_query("select count(*) from ".$mysql_table_prefix."query_log");
        echo mysql_error();
        if ($row=mysql_fetch_array($result)) {
            $log=$row[0];
        }
        $result = mysql_query("select count(*) from ".$mysql_table_prefix."temp");
        echo mysql_error();
        if ($row=mysql_fetch_array($result)) {
            $temp=$row[0];
        }

    ?>
    <div id="submenu">
    </div>
    <br/><div align="center">
        <table cellspacing ="0" cellpadding="0" class="darkgrey"><tr><td align="left"><table cellpadding="3" cellspacing = "1"  width="100%">
                        <tr class="grey"  ><td align="left"><a href="admin.php?f=15" id="small_button">Clean keywords</a> 
                            </td><td align="left"> Delete all keywords not associated with any link.</td></tr>
                        <tr class="grey"  ><td align="left"><a href="admin.php?f=16" id="small_button">Clean links</a>
                            </td><td align="left"> Delete all links not associated with any site.</td></tr>
                        <tr class="grey"  ><td align="left"><a href="admin.php?f=17" id="small_button">Clear temp tables </a>
                            </td><td align="left"> <?php print $temp;?> items in temporary table.</td></tr>
                        <tr class="grey"  ><td align="left"><a href="admin.php?f=23" id="small_button">Clear search log </a> 
                            </td><td align="left"><?php print $log;?> items in search log.
                        </td></tr></table>        </td></tr></table></div>
    <?php 
    }

    function cleanKeywords() {
        global $mysql_table_prefix;
        $query = "select keyword_id, keyword from ".$mysql_table_prefix."keywords";
        $result = mysql_query($query);
        echo mysql_error();
        $del = 0;
        while ($row=mysql_fetch_array($result)) {
            $keyId=$row['keyword_id'];
            $keyword=$row['keyword'];
            $wordmd5 = substr(md5($keyword), 0, 1);
            $query = "select keyword_id from ".$mysql_table_prefix."link_keyword$wordmd5 where keyword_id = $keyId";
            $result2 = mysql_query($query);
            echo mysql_error();
            if (mysql_num_rows($result2) < 1) {
                mysql_query("delete from ".$mysql_table_prefix."keywords where keyword_id=$keyId");
                echo mysql_error();
                $del++;
            }
        }
    ?>
    <div id="submenu">
    </div><?php 
        print "<br/><center><b>Keywords table cleaned, $del keywords deleted.</b></center>";
    }


    function cleanLinks() {
        global $mysql_table_prefix;
        $query = "select site_id from ".$mysql_table_prefix."sites";
        $result = mysql_query($query);
        echo mysql_error();
        $todelete = array();
        if (mysql_num_rows($result)>0) {
            while ($row=mysql_fetch_array($result)) {
                $todelete[]=$row['site_id'];
            }
            $todelete = implode(",", $todelete);
            $sql_end = " not in ($todelete)";
        }

        $result = mysql_query("select link_id from ".$mysql_table_prefix."links where site_id".$sql_end);
        echo mysql_error();
        $del = mysql_num_rows($result);
        while ($row=mysql_fetch_array($result)) {
            $link_id=$row[link_id];
            for ($i=0;$i<=15; $i++) {
                $char = dechex($i);
                mysql_query("delete from ".$mysql_table_prefix."link_keyword$char where link_id=$link_id");
                echo mysql_error();
            }
            mysql_query("delete from ".$mysql_table_prefix."links where link_id=$link_id");
            echo mysql_error();
        }

        $result = mysql_query("select link_id from ".$mysql_table_prefix."links where site_id is NULL");
        echo mysql_error();
        $del += mysql_num_rows($result);
        while ($row=mysql_fetch_array($result)) {
            $link_id=$row[link_id];
            for ($i=0;$i<=15; $i++) {
                $char = dechex($i);
                mysql_query("delete from ".$mysql_table_prefix."link_keyword$char where link_id=$link_id");
                echo mysql_error();
            }
            mysql_query("delete from ".$mysql_table_prefix."links where link_id=$link_id");
            echo mysql_error();
        }
    ?>
    <div id="submenu">
    </div><?php 
        print "<br/><center><b>Links table cleaned, $del links deleted.</b></center>";
    }


    function cleanTemp() {
        global $mysql_table_prefix;
        $result = mysql_query("delete from ".$mysql_table_prefix."temp where level >= 0");
        echo mysql_error();
        $del = mysql_affected_rows();
    ?>
    <div id="submenu">
    </div><?php 
        print "<br/><center><b>Temp table cleared, $del items deleted.</b></center>";
    }


    function clearLog() {
        global $mysql_table_prefix;
        $result = mysql_query("delete from ".$mysql_table_prefix."query_log where time >= 0");
        echo mysql_error();
        $del = mysql_affected_rows();
    ?>
    <div id="submenu">
    </div><?php 
        print "<br/><center><b>Search log cleared, $del items deleted.</b></center>";
    }

?>
