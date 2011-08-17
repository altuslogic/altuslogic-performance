<?php

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
    <div id="submenu">
        <ul>
            <li>
                <?php 
                    if ($must !="" || $mustnot !="" || $canleave == 1 ) {    
                        $_SESSION['index_advanced']=1;
                    }
                    if ($_SESSION['index_advanced']==1){
                        print "<a href='admin.php?f=index&adv=0&url=$advurl'>Hide advanced options</a>";
                    } else {
                        print "<a href='admin.php?f=index&adv=1&url=$advurl'>Advanced options</a>";
                    }

                ?>
            </li>
        </ul>
    </div>
    <br/>
    <div id="indexoptions"><table>
            <form action="spider.php" method="post">
                <tr><td><b>Address:</b></td><td> <input type="text" name="url" size="48" value=<?php print "\"$url\"";?>></td></tr>
                <tr><td><b>Indexing options:</b></td><td>
                        <input type="radio" name="soption" value="full" <?php print $fullchecked;?>> Full<br/>
                        <input type="radio" name="soption" value="level" <?php print $levelchecked;?>>To depth: <input type="text" name="maxlevel" size="2" value="<?php print $spider_depth;?>"><br/>
                        <?php if ($reindex==1) $check="checked"?>
                        <input type="checkbox" name="reindex" value="1" <?php print $check;?>> Reindex<br/>
                    </td></tr>
                <tr><td></td><td><input type="checkbox" name="domaincb" value="1" <?php print $checkcan;?>> CRAWLER can leave domain <!--a href="javascript:;" onClick="window.open('hmm','newWindow','width=300,height=300,left=600,top=200,resizable');" >?</a--><br/></td></tr>
                <tr><td><b>URL must include:</b></td><td><textarea name=in cols=35 rows=2 wrap="virtual"><?php print $must;?></textarea></td></tr>
                <tr><td><b>URL must not include:</b></td><td><textarea name=out cols=35 rows=2 wrap="virtual"><?php print $mustnot;?></textarea></td></tr>

                <?php 
                    if ($_SESSION['index_advanced']==1){
                    ?>
                    <?php if ($canleave==1) {$checkcan="checked" ;} ?>
                    <tr><td></td><td><input type="checkbox" name="domaincb" value="1" <?php print $checkcan;?>> ZONE TAG TO SAVE <!--a href="javascript:;" onClick="window.open('hmm','newWindow','width=300,height=300,left=600,top=200,resizable');" >?</a--><br/></td></tr>
                    <tr><td><b>HTML Container:</b></td><td><textarea name=in cols=35 rows=2 wrap="virtual"><?php print $must;?></textarea></td></tr>
                    <tr><td><b>Column to save in:</b></td><td><textarea name=out cols=35 rows=2 wrap="virtual"><?php print $mustnot;?></textarea></td></tr>
                    <?php 
                    }
                ?>

                <tr><td></td><td><input type="submit" id="submit" value="Start indexing"></td></tr>
            </form></table></div>
    <?php 
    }

?>
