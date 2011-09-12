<?php

    if (isset($_GET['action'])){
        if ($_GET['action']=='extract') extractFromHTML();
        else if ($_GET['action']=='extract') extractFromHTML();
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
            <form action="extract_funcs.php?action=extract" method="post"> 
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

    function geoScreen(){
        global $nomBase,$nomTable;

        $sql = "SHOW COLUMNS FROM ".$nomTable;
        $result = mysql_query($sql);
        echo mysql_error();
        $printCol = "<option>No column</option>";

        if ($result){
            while ($ligne=mysql_fetch_array($result)){
                $printCol .= "<option>".$ligne['Field']."</option>";
            }
        }

    ?>

    <table><tr><td valign="top">
                <div class="divNoScroll">
                    <h2>Localisation</h2>
                    <form action="?action=localize" method="post">
                        <table><tr><td>Adresse complète : </td><td><select name="location"><?php echo $printCol; ?></select></td></tr>
                            <tr><td>Rue : </td><td><select name="street"><?php echo $printCol; ?></select></td></tr>
                            <tr><td>Ville : </td><td><select name="city"><?php echo $printCol; ?></select></td></tr>
                            <tr><td>Région : </td><td><select name="province"><?php echo $printCol; ?></select></td></tr>
                            <tr><td>Pays : </td><td><select name="country"><?php echo $printCol; ?></select></td></tr></table>
                        <p align='center'><input type="submit" value="create columns"></p> 
                    </form>
                </div>
            </td></tr></table>

    <?php
    }


    function extractFromHTML(){

        error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);
        include "configSearch/cookie.php";
        include "auth.php";
        include "conversion_funcs.php";                

        $success = mysql_pconnect ($DbHost, $DbUser, $DbPassword);
        if (!$success)
            die ("<b>Cannot connect to database, check if username, password and host are correct.</b>"); 
        $success = mysql_select_db ($nomBase);
        if (!$success) {
            print "<b>Cannot choose database, check if database name is correct.";
            die();
        }

        echo "[Back to <a href=\"admin.php\">admin</a>]";

        foreach ($_POST as $key=>$val){
            //echo $key,"=",$val,"<br>";
            $$key = $val;
        }

        mysql_query("ALTER TABLE ".$mysql_table_prefix."links ADD $column MEDIUMTEXT");

        $sql = "SELECT link_id,fullhtml FROM ".$mysql_table_prefix."links WHERE site_id='$site'";
        if ($in!="") $sql .= " AND url LIKE '%$in%'";
        if ($out!="") $sql .= " AND url NOT LIKE '%$out%'";  
        $result = mysql_query($sql);

        while ($tab = mysql_fetch_array($result)){

            $html = $tab['fullhtml'];
            if ($tag_name!=""){           
                // Cas intérieur <tag>
                $doc = new DOMDocument();
                $doc->preserveWhiteSpace = false; 
                $doc->loadHTML($html);                                                   

                $path = new DOMXPath($doc);
                $newDoc = new DOMDocument();
                $newDoc->formatOutput = true;

                $query;                          
                if ($attrib_mode=='exact') $query = "@$attrib_name='$attrib_value'";
                else if ($attrib_mode=='contains') $query = "contains(@$attrib_name,'$attrib_value')"; 
                    $filtered = $path->query("//$tag_name"."[".$query."]");

                $i=0;
                while ($item = $filtered->item($i++)){
                    $node = $newDoc->importNode($item, true);   
                    $newDoc->appendChild($node);                   
                }
                $html = $newDoc->saveHTML();
            }
            if ($start_text!=""){
                $start_text = stripslashes($start_text);
                $debut = strpos($html,$start_text);
                if ($debut!==FALSE) $html = substr($html,$debut+strlen($start_text));
                else $html = "";
            }
            if ($end_text!=""){
                $fin = strpos($html,stripslashes($end_text));
                if ($fin!==FALSE) $html = substr($html,0,$fin);
                else $html = "";
            }

            if ($html!=""){

                $partialtxt = $html;        

                $partialtxt = preg_replace("/<link rel[^<>]*>/i", " ", $partialtxt);
                $partialtxt = preg_replace("@<!--sphider_noindex-->.*?<!--\/sphider_noindex-->@si", " ",$partialtxt);    
                $partialtxt = preg_replace("@<!--.*?-->@si", " ",$partialtxt);    
                $partialtxt = preg_replace("@<script[^>]*?>.*?</script>@si", " ",$partialtxt);

                $regs = Array ();
                if (preg_match("@<title *>(.*?)<\/title*>@si", $partialtxt, $regs)) {
                    $partialtxt = str_replace($regs[0], "", $partialtxt);
                }

                $partialtxt = preg_replace("@<style[^>]*>.*?<\/style>@si", " ", $partialtxt);               

                // HTML tags
                $partialtxt = preg_replace("/&lt;(\/?[^(&gt;)]+)&gt;/", "<\\1>", $partialtxt);   
                //create spaces between tags, so that removing tags doesnt concatenate strings
                $partialtxt = preg_replace("/\<(\/?[^\>]+)\>/", "\\0 ", $partialtxt);       
                $partialtxt = strip_tags($partialtxt);                  
                $partialtxt = htmlToISO($partialtxt);

                mysql_query("UPDATE ".$mysql_table_prefix."links SET $column='$partialtxt' WHERE link_id='$tab[link_id]'");
                echo "<b>".mysql_error()."</b>"; 
                echo "<br><br><b>$tab[link_id]</b>",$partialtxt;

            }

        }
    }

    function localize(){
        echo 'dr';
    }

?>
