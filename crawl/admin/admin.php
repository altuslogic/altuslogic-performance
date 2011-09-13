<?php 

    include "configSearch/cookie.php";
    error_reporting (E_ALL ^ E_NOTICE);
    $include_dir = "../include";
    include "auth.php";
    include "$include_dir/commonfuncs.php";
    extract (getHttpVars());
    $settings_dir = "../settings";
    $template_dir = "../templates";
    include "$settings_dir/conf.php";
    include "configSearch/controller.php";
    include "sites_funcs.php"; 
    include "categories_funcs.php";
    include "index_funcs.php";
    include "extract_funcs.php"; 
    include "clean_funcs.php"; 
    include "statistics.php";        

    set_time_limit (0);          


?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>CRAWL admin</title>
        <link rel="stylesheet" href="admin.css" type="text/css" />    

        <script type="text/javascript">

            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-25353270-1']);
            _gaq.push(['_trackPageview']);

            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();

        </script>     

    </head>
    <body>     
        <?php  
            //<body onload="javascript:initCoord();">
            //die ("a;:$DbHost, $DbUser, $DbPassword");
            $success = mysql_pconnect ($DbHost, $DbUser, $DbPassword);

            if (!$success)
                die ("<b>Cannot connect to database, check if username, password and host are correct.</b>");
            $success = mysql_select_db ($nomBase);
            if (!$success) {
                die("<b>Cannot choose database, check if database name is correct.<br>".list_db());
            }                                     

        ?> 
        <div id="logo_top"><img src="images/logo_altuslogic.png"></div>
        <div id="admin"> 
            <div id="tabs">
                <ul>
                    <li><a href="admin.php?f=database&type=selection" id="<?php print ($f=='database'? "selected":"default");?>">Database</a></li>
                    <li><a href="admin.php?f=index&type=index" id="<?php print ($f=='index'? "selected":"default");?>">Crawl</a></li>
                    <li><a href="admin.php?f=extract&type=selection" id="<?php print ($f=='extract'? "selected":"default");?>">Extract</a> </li>
                    <li><a href="admin.php?f=prototype&type=keywords" id="<?php print ($f=='prototype'? "selected":"default");?>">Prototype</a></li>
                    <li><a href="admin.php?f=search&type=search" id="<?php print ($f=='search'? "selected":"default");?>">Search</a></li>
                    <li><a href="admin.php?f=template" id="<?php print ($f=='template'? "selected":"default");?>">Template</a></li>
                    <li><a href="admin.php?f=production" id="<?php print ($f=='production'? "selected":"default");?>">Production</a></li>   
                    <li><a href="admin.php?f=statistics" id="<?php print ($f=='statistics'? "selected":"default");?>">Statistics</a> </li>

                </ul> 
                <!-- 

                <li><a href="admin.php?f=2" id="<?php print $site_funcs[$f]?>">Sites</a>  </li>
                <li><a href="admin.php?f=settings" id="<?php print $settings_funcs[$f]?>">Settings</a></li>
                <li><a href="admin.php?f=categories" id="<?php print $cat_funcs[$f]?>">Categories</a></li>    
                <li><a href="admin.php?f=24" id="default">Log out</a></li>  
                <li><a href="admin.php?f=clean" id="<?php print $clean_funcs[$f]?>">Clean</a> </li>
                <li><a href="admin.php?f=database" id="<?php print $database_funcs[$f]?>">Database</a></li>
                -->
            </div>
            <div id="main">

                <?php          

                    switch ($f)	{
                        case 1:
                            $message = addsite($url, $title, $short_desc, $cat);
                            $compurl=parse_url($url);
                            if ($compurl['path']=='')
                                $url=$url."/";

                            $result = mysql_query("select site_id from ".$mysql_table_prefix."sites where url='$url'");
                            echo mysql_error();
                            $row = mysql_fetch_row($result);
                            if ($site_id != "")
                                siteScreen($site_id, $message);
                            else
                                showsites($message);
                            break;   
                        case edit_site:
                            editsiteform($site_id);
                            break;
                        case 4:
                            if (!isset($domaincb))
                                $domaincb = 0;
                            if (!isset($cat))
                                $cat = "";
                            if ($soption =='full') {
                                $depth = -1;
                            } 
                            $message = editsite ($site_id, $url, $title, $short_desc, $depth, $in, $out,  $domaincb, $cat);
                            showsites($message);
                            break;
                        case 5:
                            deletesite ($site_id);
                            showsites();
                            break;
                        case add_cat:
                            if (!isset($parent))
                                $parent = "";
                            addcatform ($parent);
                            break;
                        case 7:
                            if (!isset($parent)) {
                                $parent = "";
                            }
                            $message = addcat ($category, $parent);
                            list_cats (0, 0, "white", $message);
                            break;
                        case categories:
                            list_cats (0, 0, "white", "");
                            break;
                        case edit_cat;
                            editcatform($cat_id);
                            break;
                        case 10;
                            $message = editcat ($cat_id, $category);
                            list_cats (0, 0, "white", $message);
                            break;
                        case 11;
                            deletecat($cat_id);
                            list_cats (0, 0, "white");
                            break;
                        case index;
                        submenuIndex($type);
                        switch($type){ 
                            case index:
                                if (!isset($url)) $url = "";
                                if (!isset($reindex)) $reindex = "";
                                if (isset($adv)) $_SESSION['index_advanced']=$adv;
                                indexscreen($url, $reindex);
                                break;
                            case sites:
                                showsites();
                                break;
                            case clean:
                                cleanForm();
                                break;
                            case settings:
                                include('configset.php');
                                break;
                        }  
                        break;
                        case add_site;
                            addsiteform();
                            break;

                        case 15;
                            cleanKeywords();
                            break;
                        case 16;
                            cleanLinks();
                            break;

                        case 17;
                            cleanTemp();
                            break;

                        case statistics;
                            if (!isset($type))
                                $type = "";
                            statisticsForm($type);
                            break;

                        case 19;
                            siteStats($site_id);
                            break;
                        case 20;
                            siteScreen($site_id);
                            break;
                        case 21;
                            if (!isset($start))
                                $start = 1;
                            if (!isset($filter))
                                $filter = "";
                            if (!isset($per_page))
                                $per_page = 10;

                            browsePages($site_id, $start, $filter, $per_page);
                            break;
                        case 22;
                            deletePage($link_id);
                            if (!isset($start))
                                $start = 1;
                            if (!isset($filter))
                                $filter = "";
                            if (!isset($per_page))
                                $per_page = 10;
                            browsePages($site_id, $start, $filter, $per_page);
                            break;
                        case 23;
                            clearLog();
                            break;
                        case 24;
                            session_destroy();
                            header("Location: admin.php");
                            break;
                        case 25;
                            if (!isset($limit_affiche))
                                $limit_affiche = 1;           
                            explorePages($site_id,$limit_affiche);
                            break;
                        case 26;
                            cleanImages();
                            break;
                        case database;
                        /*submenuIndex($type);*/
			                      	 include "affiche_database_header.php";
                            				
			                        switch($type){ 
			                            case selection:
			                                 	$show = $type;
			                                 	include "affiche_proto.php";  
			                                break;
			                            case tabledetails:
			                                 	$show = $type;
			                                 	include "affiche_proto.php";  
			                                break;
			                            case databasedetails:
			                                include "choice_db.php";
                            				include "db_main.php";			                                
                            				break;
			                        
			                        }                              
			            break;
                        case prototype;
                            $show = $type;
                             include "affiche_proto_header.php";
                            include "affiche_proto.php";        
                            break;
                        case search;
                            $show = $type;
                            include "affiche_search.php";        
                            break;
                        case template;
                            $show = $type;
                            include "affiche_template.php";        
                            break;
                        case production;
                            $show = $type;
                            include "affiche_production.php";        
                            break;
                        case extract;
                            $show = $type;
                            include "affiche_extract.php";
                            if ($type=='selection') extractScreen();        
                            else if ($type=='edition'){
                                    include "configSearch/progressbar.php";
                                    initProgress(5,5,600,30,'#fff','#444','#990000');
                                    geoScreen();
                                    
                                    if (isset($modify)) update_address($modify);
                                    if (isset($status)) list_status($status);
                                    if (isset($action)) localize();
                            }        
                            break;  
                        case delete_log;
                            unlink($log_dir."/".$file);
                            statisticsForm('spidering_log');
                            break;
                        case '':
                            showsites();
                            break;
                    }
                                   ?>
            </div>
        </div>

        <?php 
        
        	$stats = getStatistics();
            if ($stats==null) print "<center>Currently in database: no statistics available.</center>";
            else print "<br/><br/><center>Currently in database: ".$stats['sites']." sites, ".$stats['links']." links, ".$stats['categories']." categories and ".$stats['keywords']." keywords.<br/><br/></center>\n";


            $img = array("yes"=>"images/ok.jpg","no"=>"images/ko1.jpg","empty"=>"images/ko2.jpg");
            $info = "<b>$nomBase</b>";
            $info .= " > ".(tableExiste($nomTable)? "<b>$nomTable</b>": "<a href='?f=prototype&type=selection' style='color:red;' title='Table introuvable.'>$nomTable</a>");
            $info .= " > ".(colonneExiste($nomColonne)? "<b>$nomColonne</b>": "<a href='?f=prototype&type=selection' style='color:red;' title='Colonne introuvable.'>$nomColonne</a>");
            $col = array($info);
            foreach ($col as &$t) $t="&nbsp;$t&nbsp;";
            echo "<div id=\"topstats\"><table border='1' id='info'><tr><td style='padding-right:20px;padding-right:15px;'>".implode("</td><td>",$col)."</td>";

            $etatBase="yes";
            if (!colonneExiste($nomColonne)) $etatBase="no";
            else if (tableSize($nomTable)==0) $etatBase="empty";
                echo "<td align='center' width='65px'><img src='$img[$etatBase]'>";
            etatInfo("y_".$nomTable."_".$nomColonne."_stats",$etatBase,"stage=subtables","Création des sous-tables");

            echo "</td><td align='center' width='65px'>";
            $tableMot = "y_".$nomTable."_".$nomColonne."_keyword";
            $etat = etatInfo($tableMot,$etatBase,"stage=indexmot","Création de l'index mot");
            etatInfo("y_".$tableMot."_".$nomColonne."_stats",$etat,"stage=subtables&methode=word","Création des sous-tables de l'index mot");

            echo "</td><td align='center' width='65px'>";
            $tablePhrase = "y_".$nomTable."_".$nomColonne."_keyphrase";  
            $etat = etatInfo($tablePhrase,$etatBase,"stage=indexphrase","Création de l'index phrase");
            etatInfo("y_".$tablePhrase."_".$nomColonne."_stats",$etat,"stage=subtables&methode=phrase","Création des sous-tables de l'index phrase");
            echo "</td></tr></table></div>";

            function etatInfo($table,$requis,$action,$text){
                $img = array("yes"=>"images/ok.jpg","no"=>"images/ko1.jpg","empty"=>"images/ko2.jpg");
                $etat="yes";  
                if (!tableExiste($table)) $etat="no";
                else if (tableSize($table)==0) $etat="empty";
                    $info = "<img src='$img[$etat]'>";
                if ($etat=="no" && $requis=="yes") $info = "<a href='?$action' title=\"$text\">$info</a>";
                echo $info;
                return $etat;
            }
        ?>

    </body>
</html>