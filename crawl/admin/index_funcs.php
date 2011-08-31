<?php

    function submenuIndex(){
    ?>
    <div id="submenu">
        <ul>
            <li><a href="admin.php?f=index&type=index">Index</a></li>
            <li><a href="admin.php?f=index&type=extract">Extract</a></li>
        </ul>
    </div>
    <br/>
    <?php
    }

    function indexscreen ($url, $reindex) {
        global $mysql_table_prefix;
        $check = "";
        $levelchecked = "checked";
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
    <div class="indexoptions"><table>
            <form action="spider.php" method="post">
            <tr><td><b>Address:</b></td><td> <input type="text" name="url" size="48" value=<?php print "\"$url\"";?>></td></tr>
            <tr><td><b>Indexing options:</b></td><td>
                    <input type="radio" name="soption" value="full" <?php print $fullchecked;?>> Full<br/>
                    <input type="radio" name="soption" value="level" <?php print $levelchecked;?>>To depth: <input type="text" name="maxlevel" size="2" value="<?php print $spider_depth;?>"><br/>
                    <?php if ($reindex==1) $check="checked"?>
                    <input type="checkbox" name="reindex" value="1" <?php print $check;?>> Reindex<br/>
                </td></tr>
            <tr><td></td><td><input type="checkbox" name="domaincb" value="1" <?php print $checkcan;?>> CRAWLER can leave domain <!--a href="javascript:;" onClick="window.open('hmm','newWindow','width=300,height=300,left=600,top=200,resizable');" >?</a--><br/></td></tr>
            <tr><td><b>URL must include:</b></td><td><textarea name="in" cols=35 rows=2 wrap="virtual"><?php print $must;?></textarea></td></tr>
            <tr><td><b>URL must not include:</b></td><td><textarea name="out" cols=35 rows=2 wrap="virtual"><?php print $mustnot;?></textarea></td></tr></table>
        <center><input type="submit" id="submit" value="Start indexing"></center>
        </form></div>

    <?php 
    }

    function extractScreen(){
        global $mysql_table_prefix, $site, $in, $out, $column, $tag_name, $attrib_name, $attrib_mode, $attrib_value, $start_text, $end_text;
        $result = mysql_query("SELECT site_id,url FROM ".$mysql_table_prefix."sites");
        echo mysql_error();
        $printSites = "";
        while ($tab = mysql_fetch_array($result)){
            $selected = ($tab['site_id']==$site? "selected": "");
            $printSites .= "<option $selected value='$tab[site_id]'>$tab[url]</option>";
        }
    ?>
    <div class="indexoptions"><table>
            <form action="extract.php" method="post">
            <tr><td><b>Site:</b></td><td><select name="site">
                        <?php echo $printSites; ?>
                    </select></td></tr>
            <tr><td><b>URL must include:</b></td><td><input type="text" name="in" value="<?php print $in;?>"></td></tr> 
            <tr><td><b>URL must not include:</b></td><td><input type="text" name="out" value="<?php print $out;?>"></td></tr>
            <tr><td><b>Column to save in:</b></td><td><input type="text" name="column" value="<?php print $column;?>"></td></tr> 
            <tr><td><b>HTML Container:</b></td><td><input type="text" name="tag_name" value="<?php print $tag_name;?>"></td></tr>
            <tr><td><b>Attribute:</b></td><td>              
                    <input type="text" name="attrib_name" value="<?php print $attrib_name; ?>" style="width:100px;">
                    <select name="attrib_mode">
                        <option <?php if ($attrib_mode=='exact') echo 'selected' ?> value="exact">equals</option>
                        <option <?php if ($attrib_mode=='contains') echo 'selected' ?> value="contains">contains</option> 
                    </select>
                    <input type="text" name="attrib_value" value="<?php print $attrib_value; ?>">  
                </td></tr>
            <tr><td><b>Start text:</b></td><td><textarea name="start_text" cols=35 rows=2 wrap="virtual"><?php print $start_text;?></textarea></td></tr> 
            <tr><td><b>End text:</b></td><td><textarea name="end_text" cols=35 rows=2 wrap="virtual"><?php print $end_text;?></textarea></td></tr></table>
        <center><input type="submit" id="submit" value="Start extracting"></center> 
        </form></div>
    <?php
    }

?>
