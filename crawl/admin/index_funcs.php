<?php

    function submenuIndex($type){
    ?>
    <div id="submenu">
        <ul>
            <li><a href="admin.php?f=index&type=index" class=<?php print ($type=='index'?"subselected":"subdefault"); ?>>Index</a></li>
            <li><a href="admin.php?f=index&type=sites" class=<?php print ($type=='sites'?"subselected":"subdefault"); ?>>Sites</a></li>
            <li><a href="admin.php?f=index&type=clean" class=<?php print ($type=='clean'?"subselected":"subdefault"); ?>>Clean</a></li>
            <li><a href="admin.php?f=index&type=settings" class=<?php print ($type=='settings'?"subselected":"subdefault"); ?>>Settings</a></li>
        </ul>
    </div>
    <br/>
    <?php
    }

    function indexscreen ($url, $reindex) {
        global $mysql_table_prefix, $nomBase;
        $check = "";
        $fullchecked = "checked";
        $spider_depth = 2;
        if ($url=="") {
            $url = "http://";
            $advurl = "";
        } else {
            $advurl = $url;
            $result = mysql_query("select spider_depth, required, disallowed, can_leave_domain from ".$mysql_table_prefix."sites " .
            "where url='$url'");
            echo mysql_error();
            if (mysql_num_rows($result) > 0) {
                $row = mysql_fetch_row($result);
                $spider_depth = $row[0];
                if ($spider_depth == -1 ) {
                    $fullchecked = "checked";
                    $spider_depth ="";
                    $levelchecked = "";
                }
                $must = $row[1];
                $mustnot = $row[2];
                $canleave = $row[3];
            }            
        }                        
    ?>

    <script>
        function text_transform(field,id){
            var casechanged = field.toLowerCase();
            var result = casechanged.replace("http://","");
            result = result.replace("www.","");
            result = result.replace(/[^a-zA-Z 0-9\/]+/g,'x');
            result = result.replace(/\/+/g,'_')

            document.getElementById(id).value = result;
        }
    </script>

    <div class="indexoptions"><table>
            <form action="spider.php" method="post">
            <tr><td><b>Database:</b></td><td><input type="text" name="prefix" size="10" value="<?php print "crawl"; ?>">
                    <input type="text" name="db" id="db" size="34" value="<?php print str_replace("crawl_","",$nomBase); ?>"><input type="checkbox" name="create_db">Create DB</td></tr>
            <tr><td><b>Address:</b></td><td> <input type="text" name="url" size="64" onkeyup="javascript:text_transform(this.value,'db');" onkeydown="javascript:text_transform(this.value,'db');" oninput="javascript:text_transform(this.value,'db');" value=<?php print "\"$url\"";?>></td></tr>
            <tr><td><b>Indexing options:</b></td><td>
                    <input type="radio" name="soption" value="full" <?php print $fullchecked;?>> Full<br/>
                    <input type="radio" name="soption" value="level" <?php print $levelchecked;?>>To depth: <input type="text" name="maxlevel" size="2" value="<?php print $spider_depth;?>"><br/>
                    <?php if ($reindex==1) $check="checked"?>
                    <input type="checkbox" name="reindex" value="1" <?php print $check;?>> Reindex<br/>
                    <input type="checkbox" name="save_keywords" value="1"> Save keywords<br/>
                    <input type="checkbox" name="show_images" value="1"> Show images<br/>
                    <input type="checkbox" name="save_images" value="1"> Save images<br/>  
                    <input type="checkbox" name="capture_pages" value="1"> Capture pages<br/>  
                </td></tr>
            <tr><td></td><td><input type="checkbox" name="domaincb" value="1" <?php print $checkcan;?>> CRAWLER can leave domain <!--a href="javascript:;" onClick="window.open('hmm','newWindow','width=300,height=300,left=600,top=200,resizable');" >?</a--><br/></td></tr>
            <tr><td>&nbsp;</td><td><table><tr><td valign="top"><b>URL must include:</b><br><textarea name="in" cols=20 rows=10 wrap="virtual"><?php print $must;?></textarea></td><td valign="top"><b>URL must not include:</b><br><textarea name="out" cols=20 rows=12 wrap="virtual" ><?php print $mustnot; echo "feed\nrss\nbutton\nusager\nq=\nads\ncbwebserver6\nsearch\nfacebook.com\nutm";?></textarea></td></tr></table></td></tr></table>
        <center><input type="submit" id="submit" value="Start indexing"></center>
        </form></div>

    <?php 
    }

?>
