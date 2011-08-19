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
include "clean_funcs.php"; 
include "statistics.php";        

set_time_limit (0);

   
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>CRAWL admin</title>
<link rel="stylesheet" href="admin.css" type="text/css" />
</head>
<body>
<?php 

$site_funcs = Array (25=> "default", 22=> "default",21=> "default",4=> "default", 19=> "default", 1=> "default", 2 => "default", "add_site" => "default", 20=> "default", "edit_site" => "default", 5=>"default");
$stat_funcs = Array ("statistics" => "default",  "delete_log"=> "default");
$settings_funcs = Array ("settings" => "default");
$index_funcs = Array ("index" => "default");
$index_funcs = Array ("index" => "default");
$clean_funcs = Array ("clean" => "default", 15=>"default", 16=>"default", 17=>"default", 23=>"default");
$cat_funcs = Array (11=> "default", 10=> "default", "categories" => "default", "edit_cat"=>"default", "delete_cat"=>"default", "add_cat" => "default", 7=> "default");
$database_funcs = Array ("database" => "default");
$database_funcs = Array ("database" => "default");
$choosedatabase_funcs = Array ("choosedatabase" => "default");
$prototype_funcs = Array ("prototype" => "default");
?>
		<?php 
        
			  //die ("a;:$DbHost, $DbUser, $DbPassword");
              $success = mysql_pconnect ($DbHost, $DbUser, $DbPassword);
            
			if (!$success)
				die ("<b>Cannot connect to database, check if username, password and host are correct.</b>");
		    $success = mysql_select_db ($nomBase);
			if (!$success) {
				print "<b>Cannot choose database, check if database name is correct.<br>".list_db();
				die();
			}
			$success = mysql_select_db ($nomBase); 
		?>

<div id="admin"> 
	<div id="tabs">
		<ul>
		<?php
        $liste = array("stat_funcs","site_funcs","settings_funcs","index_funcs","cat_funcs","clean_funcs","database_funcs","choosedatabase_funcs","prototype_funcs");
        for ($i=0; $i<sizeof($liste); $i++){
            if (${$liste[$i]}[$f]) ${$liste[$i]}[$f] = "selected";
            else ${$liste[$i]}[$f] = "default";
        }      
		?>
			
		<li><a href="admin.php?f=2" id="<?php print $site_funcs[$f]?>">Sites</a>  </li>
		<li><a href="admin.php?f=categories" id="<?php print $cat_funcs[$f]?>">Categories</a></li> 
		<li><a href="admin.php?f=index" id="<?php print $index_funcs[$f]?>">Crawl</a></li>
		<li><a href="admin.php?f=clean" id="<?php print $clean_funcs[$f]?>">Clean</a> </li>
		<li><a href="admin.php?f=settings" id="<?php print $settings_funcs[$f]?>">Settings</a></li>
		<li><a href="admin.php?f=statistics" id="<?php print $stat_funcs[$f]?>">Statistics</a> </li>
		<li><a href="admin.php?f=database" id="<?php print $database_funcs[$f]?>">Database</a></li>
		<li><a href="admin.php?f=choosedatabase" id="<?php print $choosedatabase_funcs[$f]?>">Choose db</a></li>
		<li><a href="admin.php?f=prototype" id="<?php print $prototype_funcs[$f]?>">Prototype</a></li>
		<li><a href="admin.php?f=24" id="default">Log out</a></li>
		</ul>
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
		case 2:
			showsites();
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
			if (!isset($url))
				$url = "";
			if (!isset($reindex))
				$reindex = "";
			if (isset($adv)) {	
					$_SESSION['index_advanced']=$adv;
			}
			indexscreen($url, $reindex);
		break;
		case add_site;
			addsiteform();
		break;
		case clean;
			cleanForm();
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
		case database;
			include "db_main.php";
		break;
		case choosedatabase;
			include "choice_db.php";
		break;
		case prototype;         
            $show = $type;
            include "affiche_proto.php";        
		break;
		case settings;
			include('configset.php');
		break;
		case delete_log;
			unlink($log_dir."/".$file);
			statisticsForm('spidering_log');
		break;
		case '':
			showsites();
		break;
	}
	$stats = getStatistics();
	print "<br/><br/>	<center>Currently in database: ".$stats['sites']." sites, ".$stats['links']." links, ".$stats['categories']." categories and ".$stats['keywords']." keywords.<br/><br/></center>\n";

?>
</div>
</div>
</body>
</html>