<?php


    function statisticsForm ($type) {
        global $mysql_table_prefix, $log_dir;
    ?>  
    <div id='submenu'>
        <ul>
            <li><a href="admin.php?f=statistics&type=keywords">Top keywords</a></li>
            <li><a href="admin.php?f=statistics&type=pages">Largest pages</a></li>
            <li><a href="admin.php?f=statistics&type=top_searches">Most popular searches</a></li>
            <li><a href="admin.php?f=statistics&type=log">Search log</a></li>
            <li><a href="admin.php?f=statistics&type=spidering_log">Spidering logs</a></li>
        </ul>
    </div>

    <?php 
        if ($type == "") {
            $cachedSumQuery = "select sum(length(fulltxt)) from ".$mysql_table_prefix."links";
            $result=mysql_query("select sum(length(fulltxt)) from ".$mysql_table_prefix."links");
            echo mysql_error();
            if ($row=mysql_fetch_array($result)) {
                $cachedSumSize = $row[0];
            }
            $cachedSumSize = number_format($cachedSumSize / 1024, 2);

            $sitesSizeQuery = "select sum(size) from ".$mysql_table_prefix."links";
            $result=mysql_query("$sitesSizeQuery");
            echo mysql_error();
            if ($row=mysql_fetch_array($result)) {
                $sitesSize = $row[0];
            }
            $sitesSize = number_format($sitesSize, 2);

            $stats = getStatistics();
            print "<br/><div align=\"center\"><table cellspacing =\"0\" cellpadding=\"0\" class=\"darkgrey\"><tr><td><table cellpadding=\"3\" cellspacing = \"1\"><tr  class=\"grey\"><td><b>Sites:</b></td><td align=\"center\">".$stats['sites']."</td></tr>";                
            print "<tr class=\"white\"><td><b>Links:</b></td><td align=\"center\"> ".$stats['links']."</td></tr>";
            print "<tr class=\"grey\"><td><b>Categories:</b></td><td align=\"center\"> ".$stats['categories']."</td></tr>";
            print "<tr class=\"white\"><td><b>Keywords:</b></td><td align=\"center\"> ".$stats['keywords']."</td></tr>";
            print "<tr class=\"grey\"><td><b>Keyword-link realations:</b></td><td align=\"center\"> ".$stats['index']."</td></tr>";
            print "<tr class=\"white\"><td><b>Cached texts total:</b></td><td align=\"center\"> $cachedSumSize kb</td></tr>";
            print "<tr class=\"grey\"><td><b>Sites size total:</b></td><td align=\"center\"> $sitesSize kb</td></tr>";
            print "</table></td></tr></table></div>";
        }    

        if ($type=='keywords') {
            $class = "grey";
            print "<br/><div align=\"center\"><table cellspacing =\"0\" cellpadding=\"0\" class=\"darkgrey\"><tr><td><table cellpadding=\"3\" cellspacing = \"1\"><tr  class=\"grey\"><td><b>Keyword</b></td><td><b>Occurrences</b></td></tr>";
            for ($i=0;$i<=15; $i++) {
                $char = dechex($i);
                $result=mysql_query("select keyword, count(".$mysql_table_prefix."link_keyword$char.keyword_id) as x from ".$mysql_table_prefix."keywords, ".$mysql_table_prefix."link_keyword$char where ".$mysql_table_prefix."keywords.keyword_id = ".$mysql_table_prefix."link_keyword$char.keyword_id group by keyword order by x desc limit 30");
                echo mysql_error();
                while (($row=mysql_fetch_row($result))) {
                    $topwords[$row[0]] = $row[1];
                }
            }
            arsort($topwords);
            $count = 0;
            while ((list($word, $weight) = each($topwords)) && $count <= 30) {

                $count++;
                if ($class =="white") 
                    $class = "grey";
                else 
                    $class = "white";

                print "<tr class=\"$class\"><td align=\"left\">".$word."</td><td> ".$weight."</td></tr>\n";
            }            
            print "</table></td></tr></table></div>";
        }
        if ($type=='pages') {
            $class = "grey";
        ?>
        <br/><div align="center">
        <table cellspacing ="0" cellpadding="0" class="darkgrey"><tr><td>
        <table cellpadding="2" cellspacing="1">
        <tr class="grey"><td>
                <b>Page</b></td>
            <td><b>Text size</b></td></tr>
        <?php 
            $result=mysql_query("select ".$mysql_table_prefix."links.link_id, url, length(fulltxt)  as x from ".$mysql_table_prefix."links order by x desc limit 20");
            echo mysql_error();
            while ($row=mysql_fetch_row($result)) {
                if ($class =="white") 
                    $class = "grey";
                else 
                    $class = "white";
                $url = $row[1];
                $sum = number_format($row[2]/1024, 2);
                print "<tr class=\"$class\"><td align=\"left\"><a href=\"$url\">".$url."</td><td align= \"center\"> ".$sum."kb</td></tr>";
            }            
            print "</table></td></tr></table></div>";
        }

        if ($type=='top_searches') {
            $class = "grey";
            print "<br/><div align=\"center\"><table cellspacing =\"0\" cellpadding=\"0\" class=\"darkgrey\"><tr><td><table cellpadding=\"3\" cellspacing = \"1\"><tr  class=\"grey\"><td><b>Query</b></td><td><b>Count</b></td><td><b> Average results</b></td><td><b>Last queried</b></td></tr>";
            $result=mysql_query("select query, count(*) as c, date_format(max(time), '%Y-%m-%d %H:%i:%s'), avg(results)  from ".$mysql_table_prefix."query_log group by query order by c desc");
            echo mysql_error();
            while ($row=mysql_fetch_row($result)) {
                if ($class =="white") 
                    $class = "grey";
                else 
                    $class = "white";

                $word = $row[0];
                $times = $row[1];
                $date = $row[2];
                $avg = number_format($row[3], 1);
                print "<tr class=\"$class\"><td align=\"left\">".htmlentities($word)."</td><td align=\"center\"> ".$times."</td><td align=\"center\"> ".$avg."</td><td align=\"center\"> ".$date."</td></tr>";
            }            
            print "</table></td></tr></table></div>";
        }
        if ($type=='log') {
            $class = "grey";
            print "<br/><div align=\"center\"><table cellspacing =\"0\" cellpadding=\"0\" class=\"darkgrey\"><tr><td><table cellpadding=\"3\" cellspacing = \"1\"><tr  class=\"grey\"><td align=\"center\"><b>Query</b></td><td align=\"center\"><b>Results</b></td><td align=\"center\"><b>Queried at</b></td><td align=\"center\"><b>Time taken</b></td></tr>";
            $result=mysql_query("select query,  date_format(time, '%Y-%m-%d %H:%i:%s'), elapsed, results from ".$mysql_table_prefix."query_log order by time desc");
            echo mysql_error();
            while ($row=mysql_fetch_row($result)) {
                if ($class =="white") 
                    $class = "grey";
                else 
                    $class = "white";

                $word = $row[0];
                $time = $row[1];
                $elapsed = $row[2];
                $results = $row[3];
                print "<tr class=\"$class\"><td align=\"left\">".htmlentities($word)."</td><td align=\"center\"> ".$results."</td><td align=\"center\"> ".$time."</td><td align=\"center\"> ".$elapsed."</td></tr>";
            }            
            print "</table></td></tr></table></div>";
        }

        if ($type=='spidering_log') {
            $class = "grey";
            $files = get_dir_contents($log_dir);
            if (count($files)>0) {
                print "<br/><div align=\"center\"><table cellspacing =\"0\" cellpadding=\"0\" class=\"darkgrey\"><tr><td><table cellpadding=\"3\" cellspacing = \"1\"><tr  class=\"grey\"><td align=\"center\"><b>File</b></td><td align=\"center\"><b>Time</b></td><td align=\"center\"><b></b></td></tr>";

                for ($i=0; $i<count($files); $i++) {
                    $file=$files[$i];
                    $year = substr($file, 0,2);
                    $month = substr($file, 2,2);
                    $day = substr($file, 4,2);
                    $hour = substr($file, 6,2);
                    $minute = substr($file, 8,2);
                    if ($class =="white") 
                        $class = "grey";
                    else 
                        $class = "white";
                    print "<tr class=\"$class\"><td align=\"left\"><a href='$log_dir/$file' tareget='_blank'>$file</a></td><td align=\"center\"> 20$year-$month-$day $hour:$minute</td><td align=\"center\"> <a href='?f=delete_log&file=$file' id='small_button'>Delete</a></td></tr>";
                }

                print "</table></td></tr></table></div>";
            } else {
            ?>
            <br/><br/>
            <center><b>No saved logs.</b></center>
            <?php 
            }
        }

    }
    
    function getStatistics() {
        global $mysql_table_prefix;
        $stats = array();
        $keywordQuery = "select count(keyword_id) from ".$mysql_table_prefix."keywords";
        $linksQuery = "select count(url) from ".$mysql_table_prefix."links";
        $siteQuery = "select count(site_id) from ".$mysql_table_prefix."sites";
        $categoriesQuery = "select count(category_id) from ".$mysql_table_prefix."categories";

        $result = mysql_query($keywordQuery);
        echo mysql_error();
        if ($row=mysql_fetch_array($result)) {
            $stats['keywords']=$row[0];
        }
        $result = mysql_query($linksQuery);
        echo mysql_error();
        if ($row=mysql_fetch_array($result)) {
            $stats['links']=$row[0];
        }
        for ($i=0;$i<=15; $i++) {
            $char = dechex($i);
            $result = mysql_query("select count(link_id) from ".$mysql_table_prefix."link_keyword$char");
            echo mysql_error();
            if ($row=mysql_fetch_array($result)) {
                $stats['index']+=$row[0];
            }
        }
        $result = mysql_query($siteQuery);
        echo mysql_error();
        if ($row=mysql_fetch_array($result)) {
            $stats['sites']=$row[0];
        }
        $result = mysql_query($categoriesQuery);
        echo mysql_error();
        if ($row=mysql_fetch_array($result)) {
            $stats['categories']=$row[0];
        }
        return $stats;
    }
?>
