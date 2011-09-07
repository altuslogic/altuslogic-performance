<?php

    include "configSearch/cookie.php";
    include "auth.php";
    include "conversion_funcs.php";
    //require_once ("../include/commonfuncs.php");
    //require_once ("../settings/conf.php");
    error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);

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
            $debut = strpos($html,$start_text)+strlen($start_text);
            if ($debut!==FALSE) $html = substr($html,$debut);
        }
        if ($end_text!=""){
            $fin = strpos($html,stripslashes($end_text));
            if ($fin!==FALSE) $html = substr($html,0,$fin);
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

?>
