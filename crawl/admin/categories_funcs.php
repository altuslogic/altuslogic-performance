<?php

    function list_cats($parent, $lev, $color, $message) {
        global $mysql_table_prefix;
        if ($lev == 0) {
        ?>
        <div id="submenu">
            <ul>
                <li><a href="admin.php?f=add_cat">Add category</a> </li>
            </ul>
        </div>
        <?php 
            print $message;
            print "<br/>";
            print "<br/><div align=\"center\"><center><table cellspacing =\"0\" cellpadding=\"0\" class=\"darkgrey\" width =\"600\"><tr><td><table table cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">\n";
        }
        $space = "";
        for ($x = 0; $x < $lev; $x++)
            $space .= "&nbsp;&nbsp;&nbsp;&nbsp;";

        $query = "SELECT * FROM ".$mysql_table_prefix."categories WHERE parent_num=$parent ORDER BY category";
        $result = mysql_query($query);
        echo mysql_error();

        if (mysql_num_rows($result) <> '')
            while ($row = mysql_fetch_array($result)) {
                if ($color =="white") 
                    $color = "grey";
                else 
                    $color = "white";                

                $id = $row['category_id'];
                $cat = $row['category'];
                print "<tr class=\"$color\"><td width=90% align=left>$space<a href=\"admin.php?f=edit_cat&cat_id=$id\">".stripslashes($cat). "</a></td><td><a href=\"admin.php?f=edit_cat&cat_id=$id\" id=\"small_button\">Edit</a></td><td> <a href=\"admin.php?f=11&cat_id=$id\" onclick=\"return confirm('Are you sure you want to delete? Subcategories will be lost.')\" id=\"small_button\">Delete</a></td></tr>\n";

                $color = list_cats($id, $lev + 1, $color, "");
        }

        if ($lev == 0)
            print "</table></td></tr></table></center></div>\n";
        return $color;
    }

    
    function walk_through_cats($parent, $lev, $site_id) {
        global $mysql_table_prefix;
        $space = "";
        for ($x = 0; $x < $lev; $x++)
            $space .= "&nbsp;&nbsp;&nbsp;&nbsp;";

        $query = "SELECT * FROM ".$mysql_table_prefix."categories WHERE parent_num=$parent ORDER BY category";
        $result = mysql_query($query);
        echo mysql_error();

        if (mysql_num_rows($result) <> '')
            while ($row = mysql_fetch_array($result)) {
                $id = $row['category_id'];
                $cat = $row['category'];
                $state = '';
                if ($site_id <> '') {
                    $result2 = mysql_query("select * from ".$mysql_table_prefix."site_category where site_id=$site_id and category_id=$id");
                    echo mysql_error();
                    $rows = mysql_num_rows($result2);

                    if ($rows > 0)
                        $state = "checked";
                }

                print $space . "<input type=checkbox name=cat[$id] $state>" . $cat . "<br/>\n";
                ;
                walk_through_cats($id, $lev + 1, $site_id);
        }
    }

    
    function addcatform($parent) {
        global $mysql_table_prefix;
        $par2 = "";
        $par2num = "";
    ?>
    <div id="submenu">
    </div>
    <?php 
        if ($parent=='') 
            $par='(Top level)';
        else {
            $query = "SELECT category, parent_num FROM ".$mysql_table_prefix."categories WHERE category_id='$parent'";
            $result = mysql_query($query);
            if (!mysql_error())    {
                if ($row = mysql_fetch_row($result)) {
                    $par=$row[0];
                    $query = "SELECT Category_ID, Category FROM ".$mysql_table_prefix."categories WHERE Category_ID='$row[1]'";
                    $result = mysql_query($query);
                    echo mysql_error();
                    if (mysql_num_rows($result)<>'') {
                        $row = mysql_fetch_row($result);
                        $par2num = $row[0];
                        $par2 = $row[1];
                    }
                    else
                        $par2 = "Top level";

                }
            }
            else
                echo mysql_error();
            print "</td></tr></table>";
        }

    ?>
    <br/><center><table><tr><td valign=top align=center colspan=2><b>Parent: <?php print "<a href=admin.php?f=add_cat&parent=$par2num>$par2</a> >".stripslashes($par)?></b></td></tr>
    <form action=admin.php method=post>
        <input type=hidden name=f value=7>
        <input type=hidden name=parent value="<?php print $parent?>"
        <tr><td><b>Category:</b></td><td> <input type=text name=category size=40></td></tr>
        <tr><td></td><td><input type=submit id="submit" value=Add></td></tr></form>

    <?php 
        print "<tr><td colspan=2>";
        $query = "SELECT category_ID, Category FROM ".$mysql_table_prefix."categories WHERE parent_num='$parent'";
        $result = mysql_query($query);
        echo mysql_error();
        if (mysql_num_rows($result)>0) {
            print "<br/><b>Create subcategory under</b><br/><br/>";
        }
        while ($row = mysql_fetch_row($result)) {
            print "<a href=\"admin.php?f=add_cat&parent=$row[0]\">".stripslashes($row[1])."</a><br/>";
        }
        print "</td></tr></table></center>";
    }

    
    function addcat ($category, $parent) {
        global $mysql_table_prefix;
        if ($category=="") return;
        $category = addslashes($category);
        if ($parent == "") {
            $parent = 0;
        }
        $query = "INSERT INTO ".$mysql_table_prefix."categories (category, parent_num)
        VALUES ('$category', ".$parent.")";
        mysql_query($query);
        If (!mysql_error()) {
            return "<center><b>Category $category added.</b></center>" ;
        } else {
            return mysql_error();
        }
    }
    
    
    function editcatform($cat_id) {
        global $mysql_table_prefix;
        $result = mysql_query("SELECT category FROM ".$mysql_table_prefix."categories where category_id='$cat_id'");
        echo mysql_error();
        $row=mysql_fetch_array($result);
        $category=$row[0];
        ?>
                <div id="submenu">
                <center><b>Edit category</b></center>
            </div>
            <br/>
           <div align="center"><center><table>
            <form action="admin.php" method="post">
            <input type="hidden" name="f" value="10">
            <input type="hidden" name="cat_id" value="<?php  print $cat_id;?>"
            <tr><td><b>Category:</b></td><td> <input type="text" name="category" value="<?php print $category?>"size=40></td></tr>
            <tr><td></td><td><input type="submit"  id="submit"  value="Update"></td></tr></form></table></center></div>
        <?php 
        }


    function editcat ($cat_id, $category) {
        global $mysql_table_prefix;
        $qry = "UPDATE ".$mysql_table_prefix."categories SET category='".addslashes($category)."' WHERE category_id='$cat_id'";
        mysql_query($qry);
        if (!mysql_error())    {
            return "<br/><center><b>Category updated</b></center>";
        } else {
            return mysql_error();
        }
    }
    
    
    function deletecat($cat_id) {
        global $mysql_table_prefix;
        $list = implode(",", get_cats($cat_id));
        mysql_query("delete from ".$mysql_table_prefix."categories where category_id in ($list)");
        echo mysql_error();
        mysql_query("delete from ".$mysql_table_prefix."site_category where category_id=$cat_id");
        echo mysql_error();
        return "<center><b>Category deleted.</b></center>";
    }

?>
