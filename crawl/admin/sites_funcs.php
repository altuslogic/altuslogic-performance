<?php

    function siteScreen($site_id, $message) {
        global $mysql_table_prefix;
        $result = mysql_query("SELECT site_id, url, title, short_desc, indexdate from ".$mysql_table_prefix."sites where site_id=$site_id");
        echo mysql_error();
        $row=mysql_fetch_array($result);
        $url = replace_ampersand($row[url]);
        if ($row['indexdate']=='') {
            $indexstatus="<font color=\"red\">Not indexed</font>";
            $indexoption="<a href=\"admin.php?f=index&url=$url\">Index</a>";
        } else {
            $site_id = $row['site_id'];
            $result2 = mysql_query("SELECT site_id from ".$mysql_table_prefix."pending where site_id =$site_id");
            echo mysql_error();            
            $row2=mysql_fetch_array($result2);
            if ($row2['site_id'] == $row['site_id']) {
                $indexstatus = "Unfinished";
                $indexoption="<a href=\"admin.php?f=index&url=$url\">Continue indexing</a>";

            } else {
                $indexstatus = $row['indexdate'];
                $indexoption="<a href=\"admin.php?f=index&url=$url&reindex=1\">Re-index</a>";
            }
        }
        ?>

        <div id="submenu">
        </div>
        <?php print $message;?>
            <br/>

        <center>
        <div style="width:755px;">
        <div style="float:left; margin-right:0px;">
        <div class="darkgrey">
        <table cellpadding="3" cellspacing="0">

            <table  cellpadding="5" cellspacing="1" width="640">
              <tr >
                <td class="grey" valign="top" width="20%" align="left">URL:</td>
                <td class="white" align="left"><a href="<?php print  $row['url']; print "\">"; print $row['url'];?></a></td>
              </tr>
            <tr>
                <td class="grey" valign="top" align="left">Title:</td>
                <td class="white" align="left"><b><?php print stripslashes($row['title']);?></b></td>
            </tr>
              <tr>
                <td class="grey" valign="top" align="left">Description:</td>
                <td width="80%" class="white"  align="left"><?php print stripslashes($row['short_desc']);?></td>
              </tr>
              <tr>
                <td class="grey" valign="top" align="left">Last indexed:</td>
                <td class="white"  align="left"><?php print $indexstatus;?></td>
              </tr>
            </table>
        </div>
        </div>
        <div id= "vertmenu">
        <ul>
         <li><a href=admin.php?f=edit_site&site_id=<?php print  $row['site_id']?>>Edit</a></li>
        <li><?php print $indexoption?></li>
        <li><a href=admin.php?f=21&site_id=<?php print  $row['site_id']?>>Browse pages</a></li>
        <li><a href=admin.php?f=25&site_id=<?php print  $row['site_id']?>>Explore pages</a></li> 
        <li><a href=admin.php?f=5&site_id=<?php print  $row['site_id'];?> onclick="return confirm('Are you sure you want to delete? Index will be lost.')">Delete</a></li>
        <li><a href=admin.php?f=19&site_id=<?php print  $row['site_id'];?>>Stats</a></li>
        </div>
        </ul>
        </div>
        </center>
        <div class="clear">
        </div>
        <br/>
    <?php 
    }


    function addsiteform() {
    ?>
    <div id="submenu"><center><b>Add a site</b></center></div>
    <br/><div align=center><center><table>
                <form action=admin.php method=post>
                    <input type=hidden name=f value=1>
                    <input type=hidden name=af value=2>
                    <tr><td><b>URL:</b></td><td align ="right"></td><td><input type=text name=url size=60 value ="http://"></td></tr>
                    <tr><td><b>Title:</b></td><td></td><td> <input type=text name=title size=60></td></tr>
                    <tr><td><b>Short description:</b></td><td></td><td><textarea name=short_desc cols=45 rows=3 wrap="virtual"></textarea></td></tr>
                    <tr><td>Category:</td><td></td><td>
                        <?php  walk_through_cats(0, 0, '');?></td></tr>
                    <tr><td></td><td></td><td><input type=submit id="submit" value=Add></td></tr></form></table></center></div>
    <?php 
    }

    function editsiteform($site_id) {
        global $mysql_table_prefix;
        $result = mysql_query("SELECT site_id, url, title, short_desc, spider_depth, required, disallowed, can_leave_domain from ".$mysql_table_prefix."sites where site_id=$site_id");
        echo mysql_error();
        $row = mysql_fetch_array($result);
        $depth = $row['spider_depth'];
        $fullchecked = "";
        $depthchecked = "";        
        if ($depth == -1 ) {
            $fullchecked = "checked";
            $depth ="";
        } else {
            $depthchecked = "checked";
        }
        $leave_domain = $row['can_leave_domain'];
        if ($leave_domain == 1 ) {
            $domainchecked = "checked";
        } else {
            $domainchecked = "";
        }        
    ?>
    <div id="submenu"><center><b>Edit site</b></center>
    </div>
    <br/><div align=center><center><table>
                <form action=admin.php method=post>
                    <input type=hidden name=f value=4>
                    <input type=hidden name=site_id value=<?php print $site_id;?>>
                    <tr><td><b>URL:</b></td><td align ="right"></td><td><input type=text name=url value=<?php print "\"".$row['url']."\""?> size=60></td></tr>
                    <tr><td><b>Title:</b></td><td></td><td> <input type=text name=title value=<?php print  "\"".stripslashes($row['title'])."\""?> size=60></td></tr>
                    <tr><td><b>Short description:</b></td><td></td><td><textarea name=short_desc cols=45 rows=3 wrap><?php print stripslashes($row['short_desc'])?></textarea></td></tr>
                    <tr><td><b>Spidering options:</b></td><td></td><td><input type="radio" name="soption" value="full" <?php print $fullchecked;?>> Full<br/>
                            <input type="radio" name="soption" value="level" <?php print $depthchecked;?>>To depth: <input type="text" name="depth" size="2" value="<?php print $depth;?>"><br/>
                            <input type="checkbox" name="domaincb" value="1" <?php print $domainchecked;?>> Spider can leave domain
                        </td></tr>            
                    <tr><td><b>URLs must include:</b></td><td></td><td><textarea name=in cols=45 rows=2 wrap="virtual"><?php print $row['required'];?></textarea></td></tr>
                    <tr><td><b>URLs must not include:</b></td><td></td><td><textarea name=out cols=45 rows=2 wrap="virtual"><?php print $row['disallowed'];?></textarea></td></tr>

                    <tr><td>Category:</td><td></td><td>
                        <?php  walk_through_cats(0, 0, $site_id);?></td></tr>
                    <tr><td></td><td></td><td><input type="submit"  id="submit"  value="Update"></td></tr></form></table></center></div>
    <?php 
    }


    function addsite ($url, $title, $short_desc, $cat) {
        global $mysql_table_prefix;
        $short_desc = addslashes($short_desc);
        $title = addslashes($title);
        $compurl=parse_url("".$url);
        if ($compurl['path']=='')
            $url=$url."/";
        $result = mysql_query("select site_ID from ".$mysql_table_prefix."sites where url='$url'");
        echo mysql_error();
        $rows = mysql_numrows($result);
        if ($rows==0 ) {
            mysql_query("INSERT INTO ".$mysql_table_prefix."sites (url, title, short_desc) VALUES ('$url', '$title', '$short_desc')");
            echo mysql_error();
            $result = mysql_query("select site_ID from ".$mysql_table_prefix."sites where url='$url'");
            echo mysql_error();
            $row = mysql_fetch_row($result);
            $site_id = $row[0];
            $result=mysql_query("select category_id from ".$mysql_table_prefix."categories");
            echo mysql_error();
            while ($row=mysql_fetch_row($result)) {
                $cat_id=$row[0];
                if ($cat[$cat_id]=='on') {
                    mysql_query("INSERT INTO ".$mysql_table_prefix."site_category (site_id, category_id) values ('$site_id', '$cat_id')");
                    echo mysql_error();
                }
            }

            If (!mysql_error())    {
                $message =  "<br/><center><b>Site added</b></center>" ;
            } else {
                $message = mysql_error();
            }

        } else {
            $message = "<center><b>Site already in database</b></center>";
        }
        return $message;
    }

    function editsite ($site_id, $url, $title, $short_desc, $depth, $required, $disallowed, $domaincb,  $cat) {
        global $mysql_table_prefix;
        $short_desc = addslashes($short_desc);
        $title = addslashes($title);
        mysql_query("delete from ".$mysql_table_prefix."site_category where site_id=$site_id");
        echo mysql_error();
        $compurl=parse_url($url);
        if ($compurl['path']=='')
            $url=$url."/";
        mysql_query("UPDATE ".$mysql_table_prefix."sites SET url='$url', title='$title', short_desc='$short_desc', spider_depth =$depth, required='$required', disallowed='$disallowed', can_leave_domain=$domaincb WHERE site_id=$site_id");
        echo mysql_error();
        $result=mysql_query("select category_id from ".$mysql_table_prefix."categories");
        echo mysql_error();
        print mysql_error();
        while ($row=mysql_fetch_row($result)) {
            $cat_id=$row[0];
            if ($cat[$cat_id]=='on') {
                mysql_query("INSERT INTO ".$mysql_table_prefix."site_category (site_id, category_id) values ('$site_id', '$cat_id')");
                echo mysql_error();
            }
        }
        If (!mysql_error()) {
            return "<br/><center><b>Site updated.</b></center>" ;
        } else {
            return mysql_error();
        }
    }



    function showsites($message) {
        global $mysql_table_prefix;
        $result = mysql_query("SELECT site_id, url, title, indexdate from ".$mysql_table_prefix."sites ORDER By indexdate, title");
        echo mysql_error();
    ?>
    <div id='submenu'>
        <ul>
            <li><a href='admin.php?f=add_site'>Add site</a> </li>
            <?php 
                if (mysql_num_rows($result) > 0) {
                ?>
                <li><a href='spider.php?all=1'> Reindex all</a></li>
                <?php 
                }
            ?>
        </ul>
    </div>

    <?php 
        print $message;
        print "<br/>";
        if (mysql_num_rows($result) > 0) {
            print "<div align=\"center\"><table cellspacing =\"0\" cellpadding=\"0\" class=\"darkgrey\"><tr><td><table cellpadding=\"3\" cellspacing=\"1\">
            <tr class=\"grey\"><td align=\"center\"><b>Site name</b></td><td align=\"center\"><b>Site url</b></td>
            <td align=\"center\"><b>Last indexed</b></td><td colspan=7 align=\"center\"><b>Options</b></td></tr>\n";
        } else {
        ?><center><p><b>Welcome to Glerkp's CRAWL. <br><br>Choose "Add site" from the submenu to add a new site, or "Index" to directly go to the indexing section.</b></p></center><?php 
        }
        $class = "grey";
        while ($row=mysql_fetch_array($result))    {
            if ($row['indexdate']=='') {
                $indexstatus="<font color=\"red\">Not indexed</font>";
                $indexoption="<a href=admin.php?f=index&url=$row[url] id=\"small_button\">Index</a>";
            } else {
                $site_id = $row['site_id'];
                $result2 = mysql_query("SELECT site_id from ".$mysql_table_prefix."pending where site_id =$site_id");
                echo mysql_error();            
                $row2=mysql_fetch_array($result2);
                if ($row2['site_id'] == $row['site_id']) {
                    $indexstatus = "Unfinished";
                    $indexoption="<a href=admin.php?f=index&url=$row[url] id=\"small_button\">Continue</a>";

                } else {
                    $indexstatus = $row['indexdate'];
                    $indexoption="<a href=admin.php?f=index&url=$row[url]&reindex=1 id=\"small_button\">Re-index</a>";
                }
            }
            if ($class =="white") 
                $class = "grey";
            else 
                $class = "white";
            print "<tr class=\"$class\"><td align=\"left\">".stripslashes($row[title])."</td><td align=\"left\"><a href=\"$row[url]\">$row[url]</a></td><td>$indexstatus</td>";
            print "<td><a href=admin.php?f=20&site_id=$row[site_id] id=\"small_button\">Details</a></td>";
            print "<td><a href=admin.php?f=edit_site&site_id=$row[site_id] id=\"small_button\">Edit</a></td>";
            print "<td>".$indexoption."</td>";
            print "<td><a href=admin.php?f=21&site_id=$row[site_id] id=\"small_button\">Browse</a></td>";
            print "<td><a href=admin.php?f=25&site_id=$row[site_id] id=\"small_button\">Explore</a></td>";
            print "<td><a href=admin.php?f=5&site_id=$row[site_id] id=\"small_button\" onclick=\"return confirm('Are you sure you want to delete? Index will be lost.')\">Delete</a></td>";
            print "<td><a href=admin.php?f=19&site_id=$row[site_id] id=\"small_button\">Stats</a></td></tr>\n";

        }
        if (mysql_num_rows($result) > 0) {
            print "</table></td></tr></table></div>";
        }
    }


    function deletesite($site_id) {
        global $mysql_table_prefix;
        mysql_query("delete from ".$mysql_table_prefix."sites where site_id=$site_id");
        echo mysql_error();
        mysql_query("delete from ".$mysql_table_prefix."site_category where site_id=$site_id");
        echo mysql_error();
        $query = "select link_id from ".$mysql_table_prefix."links where site_id=$site_id";
        $result = mysql_query($query);
        echo mysql_error();
        $todelete = array();
        while ($row=mysql_fetch_array($result)) {
            $todelete[]=$row['link_id'];
        }

        if (count($todelete)>0) {
            $todelete = implode(",", $todelete);
            for ($i=0;$i<=15; $i++) {
                $char = dechex($i);
                $query = "delete from ".$mysql_table_prefix."link_keyword$char where link_id in($todelete)";
                mysql_query($query);
                echo mysql_error();
            }
        }

        mysql_query("delete from ".$mysql_table_prefix."links where site_id=$site_id");
        echo mysql_error();
        mysql_query("delete from ".$mysql_table_prefix."pending where site_id=$site_id");
        echo mysql_error();
        return "<br/><center><b>Site deleted</b></center>";
    }

        function deletePage($link_id) {
        global $mysql_table_prefix;
        mysql_query("delete from ".$mysql_table_prefix."links where link_id=$link_id");
        echo mysql_error();
        for ($i=0;$i<=15; $i++) {
            $char = dechex($i);
            mysql_query("delete from ".$mysql_table_prefix."link_keyword$char where link_id=$link_id");
        }
        echo mysql_error();
        return "<br/><center><b>Page deleted</b></center>";
    }


    function siteStats($site_id) {
        global $mysql_table_prefix;
        $result = mysql_query("select url from ".$mysql_table_prefix."sites where site_id=$site_id");
        echo mysql_error();
        if ($row=mysql_fetch_array($result)) {
            $url=$row[0];

            $lastIndexQuery = "SELECT indexdate from ".$mysql_table_prefix."sites where site_id = $site_id";

            $result = mysql_query($lastIndexQuery);
            echo mysql_error();
            if ($row=mysql_fetch_array($result)) {
                $stats['lastIndex']=$row[0];
            }

            $otherQueries = "select sum(length(fulltxt)) as sumSize, sum(size) as siteSize, count(*) as links from ".$mysql_table_prefix."links where site_id = $site_id";
            
            $result = mysql_query($otherQueries);
            echo mysql_error();
            if ($row=mysql_fetch_array($result)) {
                $stats['sumSize']=$row['sumSize'];
                $stats['links']=$row['links'];
                $stats['siteSize']=$row['siteSize'];
            }
    
            if ($stats['siteSize']=="")
                $stats['siteSize'] = 0;
            $stats['siteSize'] = number_format($stats['siteSize'], 2);

            for ($i=0;$i<=15; $i++) {
                $char = dechex($i);
                $result = mysql_query("select count(*) from ".$mysql_table_prefix."links, ".$mysql_table_prefix."link_keyword$char where ".$mysql_table_prefix."links.link_id=".$mysql_table_prefix."link_keyword$char.link_id and ".$mysql_table_prefix."links.site_id = $site_id");
                echo mysql_error();
                if ($row=mysql_fetch_array($result)) {
                    $stats['index']+=$row[0];
                }
            }
            for ($i=0;$i<=15; $i++) {
                $char = dechex($i);
                $wordQuery = "select count(distinct keyword) from ".$mysql_table_prefix."keywords, ".$mysql_table_prefix."links, ".$mysql_table_prefix."link_keyword$char where ".$mysql_table_prefix."links.link_id=".$mysql_table_prefix."link_keyword$char.link_id and ".$mysql_table_prefix."links.site_id = $site_id and ".$mysql_table_prefix."keywords.keyword_id = ".$mysql_table_prefix."link_keyword$char.keyword_id";
                $result = mysql_query($wordQuery);
                echo mysql_error();
                if ($row=mysql_fetch_array($result)) {
                    $stats['words']+=$row[0];
                }
            }
            
            print"<div id=\"submenu\"></div>";
            print "<br/><div align=\"center\"><center><table cellspacing =\"0\" cellpadding=\"0\" class=\"darkgrey\"><tr><td><table cellpadding=\"3\" cellspacing = \"1\"><tr  class=\"grey\"><td colspan=\"2\">";
            print "Statistics for site <a href=\"admin.php?f=20&site_id=$site_id\">$url</a>";
            print "<tr class=\"white\"><td>Last indexed:</td><td align=\"center\"> ".$stats['lastIndex']."</td></tr>";
            print "<tr class=\"grey\"><td>Pages indexed:</td><td align=\"center\"> ".$stats['links']."</td></tr>";
            print "<tr class=\"white\"><td>Total index size:</td><td align=\"center\"> ".$stats['index']."</td></tr>";
            $sum = number_format($stats['sumSize']/1024, 2);
            print "<tr class=\"grey\"><td>Cached texts:</td><td align=\"center\"> ".$sum."kb</td></tr>";
            print "<tr class=\"white\"><td>Total number of keywords:</td><td align=\"center\"> ".$stats['words']."</td></tr>";
            print "<tr class=\"grey\"><td>Site size:</td><td align=\"center\"> ".$stats['siteSize']."kb</td></tr>";
            print "</table></td></tr></table></center></div>";
        }
    }

    function browsePages($site_id, $start, $filter, $per_page) {
        global $mysql_table_prefix;
        $result = mysql_query("select url from ".$mysql_table_prefix."sites where site_id=$site_id");
        echo mysql_error();
        $row = mysql_fetch_row($result);
        $url = $row[0];
        
        $query_add = "";
        if ($filter != "") {
            $query_add = "and url like '%$filter%'";
        }
        $linksQuery = "select count(*) from ".$mysql_table_prefix."links where site_id = $site_id $query_add";
        $result = mysql_query($linksQuery);
        echo mysql_error();
        $row = mysql_fetch_row($result);
        $numOfPages = $row[0]; 

        $from = ($start-1) * 10;
        $to = min(($start)*10, $numOfPages);

        
        $linksQuery = "select link_id, url from ".$mysql_table_prefix."links where site_id = $site_id and url like '%$filter%' order by url limit $from, $per_page";
        $result = mysql_query($linksQuery);
        echo mysql_error();
        ?>
        <div id="submenu"></div>
        <br/>
        <center>
        <b>Pages of site <a href="admin.php?f=20&site_id=<?php  print $site_id?>"><?php print $url;?></a></b><br/>
        <p>
        <form action="admin.php" method="post">
        Urls per page: <input type="text" name="per_page" size="3" value="<?php print $per_page;?>"> 
        Url contains: <input type="text" name="filter" size="15" value="<?php print $filter;?>"> 
        <input type="submit" id="submit" value="Filter">
        <input type="hidden" name="start" value="1">
        <input type="hidden" name="site_id" value="<?php print $site_id?>">
        <input type="hidden" name="f" value="21">
        </form>
        </p>
    <table width="600"><tr><td>
        <table cellspacing ="0" cellpadding="0" class="darkgrey" width ="100%"><tr><td>
        <table  cellpadding="3" cellspacing="1" width="100%">

        <?php 
        $class = "white";
        while ($row = mysql_fetch_array($result)) {
            if ($class =="white") 
                $class = "grey";
            else 
                $class = "white";
            print "<tr class=\"$class\"><td><a href=\"".$row['url']."\">".$row['url']."</a></td><td width=\"8%\"> <a href=\"admin.php?link_id=".$row['link_id']."&f=22&site_id=$site_id&start=1&filter=$filter&per_page=$per_page\">Delete</a></td></tr>";
        }

        print "</table></td></tr></table>";

        $pages = ceil($numOfPages / $per_page);
        $prev = $start - 1;
        $next = $start + 1;

        if ($pages > 0)
            print "<center>Pages: ";

        $links_to_next =10;
        $firstpage = $start - $links_to_next;
        if ($firstpage < 1) $firstpage = 1;
        $lastpage = $start + $links_to_next;
        if ($lastpage > $pages) $lastpage = $pages;
        
        for ($x=$firstpage; $x<=$lastpage; $x++)
            if ($x<>$start)    {
                print "<a href=admin.php?f=21&site_id=$site_id&start=$x&filter=$filter&per_page=$per_page>$x</a> ";
            }     else
                print "<b>$x </b>";
        print"</td></tr></table></center>";

    }


    function explorePages($site_id, $lim) {
        global $mysql_table_prefix;
        $result = mysql_query("select url from ".$mysql_table_prefix."sites where site_id=$site_id");
        echo mysql_error();               
        $row = mysql_fetch_row($result);
        $url = $row[0];   
        
        $linksQuery = "select link_id, url from ".$mysql_table_prefix."links where site_id = $site_id order by url";
        $result = mysql_query($linksQuery);
        echo mysql_error();
        ?>
        <div id="submenu"></div>
        <br/>
        <center>
        <b>Pages of site <a href="admin.php?f=20&site_id=<?php  print $site_id?>"><?php print $url;?></a></b><br/>
        <p>
        <form action="admin.php" method="post">
        Max number pages: <input type="text" name="limit_affiche" size="3" value="<?php print $limit_affiche;?>">
        <input type="submit" id="submit" value="Limit"> 
        <input type="hidden" name="site_id" value="<?php print $site_id?>">
        <input type="hidden" name="f" value="25">
        </form>
        </p>
        <table width="600"><tr><td>
        <table cellspacing ="0" cellpadding="0" class="darkgrey" width ="100%"><tr><td>
        <table  cellpadding="3" cellspacing="1" width="100%">
        
        <?php                                                               
        $current_path = extractURL("http://");
        $current_level = sizeof($current_path);      
        
        while ($row = mysql_fetch_array($result)) {
                       
            $path = extractURL($row['url']);
            $level = sizeof($path);   
            $common = 0;

            while ($common<$level && $common<$current_level && $path[$common]==$current_path[$common]) $common++;
            $lien = "<a href=\"".$row['url']."\">".$row['url']."</a>";       
            $sql = "select count(*) from ".$mysql_table_prefix."links where url like '$row[url]%'";
            $row = mysql_fetch_row(mysql_query($sql));
            $cpt = $row[0];
                    
            if ($level!=$current_level){                                                                  
                for ($i=$common+1; $i<$level; $i++){
                    $url = "";
                    for ($j=0; $j<$i; $j++) $url .= $path[$j]."/";
                    $sql = "select count(*) from ".$mysql_table_prefix."links where url like '$url%'";
                    $row = mysql_fetch_row(mysql_query($sql));                     
                    if ($row[0]>$lim){
                        if ($class =="white") 
                            $class = "grey";
                        else 
                            $class = "white";
                        print "<tr class=\"$class\"><td>$url</td><td align='right'>$row[0]</td></tr>";       
                    }
                }                           
            }   
            
            if ($cpt>$lim){
                if ($class =="white") 
                    $class = "grey";
                else 
                    $class = "white";
                print "<tr class=\"$class\"><td>$lien</td><td align='right'>$cpt</td></tr>";
            }                           
            $current_path = $path;
            $current_level = $level;   
            
        }
        
        print "</table></td></tr></table>";
        
        for ($x=$firstpage; $x<=$lastpage; $x++)
            if ($x<>$start)    {
                print "<a href=admin.php?f=21&site_id=$site_id&start=$x&filter=$filter&per_page=$per_page>$x</a> ";
            }     else
                print "<b>$x </b>";
        print"</td></tr></table></center>";

    }
       
    
    function extractURL($url){
       $path = explode("/",$url);           
       if ($path[sizeof($path)-1]=='') array_pop($path);
       return $path;  
   } 
   
?>
