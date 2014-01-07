<?php 
///echo 'j';
error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);

    include "configSearch/cookie.php";
    $prefix = readCookie($_POST,"prefix","");
    $db = readCookie($_POST,"db","");
    if (isset($_POST['create_db'])){
        $nomBase = $prefix."_".$db;
        saveCookie("nomBase",$nomBase);
    }
//echo 'j';
 $urldata="temp.php?db=".$nomBase;

echo "<a href='../../".$urldata."' target='new'>OPEN SELECTOR</a><br><br>
db :".$db."<br>";
echo "prefix :".$prefix."<br>";
echo "create_db :".$_POST['create_db']."<br>";


    set_time_limit (0);
    $include_dir = "../include";
    include "auth.php";
    require_once ("$include_dir/commonfuncs.php");
    $all = 0; 
    extract (getHttpVars());
    $settings_dir =  "../settings";
  //echo 'j';  
  require_once ("$settings_dir/conf.php");



    include "messages.php";
    include "spider_funcs.php";
    include "conversion_funcs.php";
    include "install_funcs.php";
    include "capture.php";
    include "image.class.php";
    
    
    $success = mysql_pconnect ($DbHost, $DbUser, $DbPassword);
    if (!$success)
        die ("<b>Cannot connect to database, check if username, password and host are correct.</b>");

    $newdb = false;
    if (isset($create_db)){
        $newdb = mysql_query("CREATE DATABASE IF NOT EXISTS $nomBase");
    }

    $success = mysql_select_db ($nomBase);
    if (!$success) {
        print "<b>Cannot choose database, check if database name is correct.";
        die();
    }

    if ($newdb) install();


    $delay_time = 0;

    $reindex = 0;
    $command_line = 0;

    if (isset($_SERVER['argv']) && $_SERVER['argc'] >= 2) {
        $command_line = 1;
        $ac = 1; //argument counter
        while ($ac < (count($_SERVER['argv']))) {
            $arg = $_SERVER['argv'][$ac];

            if ($arg  == '-all') {
                $all = 1;
                break;
            } else if ($arg  == '-u') {
                    $url = $_SERVER['argv'][$ac+1];
                    $ac= $ac+2;
                } else if ($arg  == '-f') {
                        $soption = 'full';
                        $ac++;
                    } else if ($arg == '-d') {
                            $soption = 'level';
                            $maxlevel =  $_SERVER['argv'][$ac+1];;
                            $ac= $ac+2;
                        } else if ($arg == '-l') {
                                $domaincb = 1;
                                $ac++;
                            } else if ($arg == '-r') {
                                    $reindex = 1;
                                    $ac++;
                                } else if ($arg  == '-m') {
                                        $in =  str_replace("\\n", chr(10), $_SERVER['argv'][$ac+1]);
                                        $ac= $ac+2;
                                    } else if ($arg  == '-n') {
                                            $out =  str_replace("\\n", chr(10), $_SERVER['argv'][$ac+1]);
                                            $ac= $ac+2;
                                        } else {
                                            commandline_help();
                                            die();
                 
            }
            
           

        }
    }
    
 /*   
echo "all :".$all."<br>";
echo "url :".$url."<br>";
echo "soption :".$soption."<br>";
echo "maxlevel :".$maxlevel."<br>";
echo "domaincb :".$domaincb."<br>";
echo "reindex :".$reindex."<br>";
echo "in :<textarea cols='30' rows='20'>".$in."</textarea><br>";
echo "out :<textarea cols='30' rows='20'>".$out."</textarea><br>";
*/

if($soption=='full'){
		$fullchecked="checked";
}
?>



 <div class="indexoptions"><table>
            <form action="spider.php" method="post">
            <tr><td><b>Database:</b></td><td><input type="text" name="prefix" size="10" value="<?php print "crawl"; ?>">
                    <input type="text" name="db" id="db" size="34" value="<?php print str_replace("crawl_","",$nomBase); ?>"><input type="checkbox" name="create_db">Create DB</td></tr>
            <tr><td><b>Address:</b></td><td> <input type="text" name="url" size="64" onkeyup="javascript:text_transform(this.value,'db');" onkeydown="javascript:text_transform(this.value,'db');" oninput="javascript:text_transform(this.value,'db');" value=<?php print "\"$url\"";?>></td></tr>
            <tr><td><b>Indexing options:</b></td><td>
                    <input type="radio" name="soption" value="full" <?php print $fullchecked;?>> Full<br/>
                    <input type="radio" name="soption" value="level" <?php print $levelchecked;?>>To depth: <input type="text" name="maxlevel" size="2" value="<?php print $maxlevel;?>"><br/>
                    <?php if ($reindex==1) $check="checked"?>
                    <input type="checkbox" name="reindex" value="1" <?php print $check;?>> Reindex<br/>
                    <input type="checkbox" name="save_keywords" value="1"> Save keywords<br/>
                    <input type="checkbox" name="show_images" value="1"> Show images<br/>
                    <input type="checkbox" name="save_images" value="1"> Save images<br/>  
                    <input type="checkbox" name="capture_pages" value="1"> Capture pages<br/>  
                </td></tr>
            <tr><td></td><td><input type="checkbox" name="domaincb" value="1" <?php print $checkcan;?>> CRAWLER can leave domain <!--a href="javascript:;" onClick="window.open('hmm','newWindow','width=300,height=300,left=600,top=200,resizable');" >?</a--><br/></td></tr>
            <tr><td>&nbsp;</td><td><table><tr><td valign="top"><b>URL must include:</b><br><textarea name="in" cols=20 rows=10 wrap="virtual"><?php print $in;?></textarea></td><td valign="top"><b>URL must not include:</b><br><textarea name="out" cols=20 rows=12 wrap="virtual" ><?php print $out; echo $out;?></textarea></td></tr></table></td></tr></table>
        <center><input type="submit" id="submit" value="Start indexing"></center>
        </form></div>




<?php







    if (isset($soption) && $soption == 'full') {
        $maxlevel = -1;

    }

    if (!isset($domaincb)) {
        $domaincb = 0;

    }

    if(!isset($reindex)) {
        $reindex=0;
    }

    if(!isset($save_keywords)) {
        $save_keywords=0;
    }

    if(!isset($save_images)) {
        $save_images=0;
    }

    if(!isset($show_images)) {
        $show_images=0;
    }

    if(!isset($capture_pages)) {
        $capture_pages=0;
    }

    if(!isset($maxlevel)) {
        $maxlevel=0;
    }


    if ($keep_log) {
        if ($log_format=="html") {
            $log_file =  $log_dir."/".Date("ymdHi").".html";
        } else {
            $log_file =  $log_dir."/".Date("ymdHi").".log";
        }

        if (!$log_handle = fopen($log_file, 'w')) {
            die ("Logging option is set, but cannot open file for logging.");
        }
    }

    if ($all ==  1) {          
        index_all();
    } else {

        if ($reindex == 1 && $command_line == 1) {               
            $result=mysql_query("select url, spider_depth, required, disallowed, can_leave_domain from ".$mysql_table_prefix."sites where url='$url'");
            echo mysql_error();
            if($row=mysql_fetch_row($result)) {
                $url = $row[0];
                $maxlevel = $row[1];
                $in= $row[2];
                $out = $row[3];
                $domaincb = $row[4];
                if ($domaincb=='') {
                    $domaincb=0;
                }
                if ($maxlevel == -1) {
                    $soption = 'full';
                } else {
                    $soption = 'level';
                }
            }

        }
        if (!isset($in)) {
            $in = "";
        }
        if (!isset($out)) {
            $out = "";
        }                                 

        index_site($url, $reindex, $maxlevel, $soption, $in, $out, $domaincb, $save_keywords, $save_images, $show_images, $capture_pages);

    }

    $tmp_urls  = Array();


    function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }


    function index_url($url, $level, $site_id, $md5sum, $domain, $indexdate, $sessid, $can_leave_domain, $reindex, $save_keywords, $save_images, $show_images, $capture_pages) { 

        global $entities, $min_delay;
        global $command_line;
        global $min_words_per_page;
        global $supdomain;
        global $mysql_table_prefix, $user_agent, $tmp_urls, $delay_time, $domain_arr;
        $needsReindex = 1;
        $deletable = 0;

        $url_status = url_status($url);
        $thislevel = $level - 1;
        $print_images = "";


        if ($capture_pages){
            global $nomBase;
            capture($url,"C:/Users/Antoine/altuslogic-performance/capture/$nomBase/");
        }


        if (strstr($url_status['state'], "Relocation")) {
            $url = preg_replace("/ /", "", url_purify($url_status['path'], $url, $can_leave_domain));

            if ($url <> '') {						//where link='$url' && id = '$sessid'
                $result = mysql_query("select link from ".$mysql_table_prefix."temp ORDER BY `link` DESC");
                echo mysql_error();
                $rows = mysql_numrows($result);
                if ($rows == 0) {
                    mysql_query ("insert into ".$mysql_table_prefix."temp (link, level, id) values ('$url', '$level', '$sessid')");
                    echo mysql_error();
                }
            }

            $url_status['state'] == "redirected";
        }

        /*
        if ($indexdate <> '' && $url_status['date'] <> '') {
        if ($indexdate > $url_status['date']) {
        $url_status['state'] = "Date checked. Page contents not changed";
        $needsReindex = 0;
        }
        }*/
        ini_set("user_agent", $user_agent);
        if ($url_status['state'] == 'ok') {
            $OKtoIndex = 1;
            $file_read_error = 0;

            if (time() - $delay_time < $min_delay) {
                sleep ($min_delay- (time() - $delay_time));
            }
            $delay_time = time();
            if (!fst_lt_snd(phpversion(), "4.3.0")) {
                $file = file_get_contents($url);
                if ($file === FALSE) {
                    $file_read_error = 1;
                }
            } else {
                $fl = @fopen($url, "r");
                if ($fl) {
                    while ($buffer = @fgets($fl, 4096)) {
                        $file .= $buffer;
                    }
                } else {
                    $file_read_error = 1;
                }

                fclose ($fl);
            }
            if ($file_read_error) {
                $contents = getFileContents($url);
                $file = $contents['file'];
            }                           

            $pageSize = number_format(strlen($file)/1024, 2, ".", "");
            printPageSizeReport($pageSize);

            if ($url_status['content'] != 'text') {
                $file = extract_text($file, $url_status['content']);
            }

            printStandardReport('starting', $command_line);                


            $newmd5sum = md5($file);


            if ($md5sum == $newmd5sum) {
                printStandardReport('md5notChanged',$command_line);
                $OKtoIndex = 0;
            } else if (isDuplicateMD5($newmd5sum)) {
                    $OKtoIndex = 0;
                    printStandardReport('duplicate',$command_line);
                }

                if (($md5sum != $newmd5sum || $reindex ==1) && $OKtoIndex == 1) {
                $urlparts = parse_url($url);
                $newdomain = $urlparts['host'];
                $type = 0;



                // remove link to css file
                //get all links from file
                $data = clean_file($file, $url, $url_status['content']); 

              /*  if ($data['noindex'] == 1) {
                    $OKtoIndex = 1;
                    $deletable = 1;
                    printStandardReport('metaNoindex',$command_line);
                }
*/

                $wordarray = unique_array(explode(" ", $data['content']));   

                if ($data['nofollow'] != 1) {
                    $links = get_links($file, $url, $can_leave_domain, $data['base']);
                    $links = distinct_array($links);
                    $all_links = count($links);
                    $numoflinks = 0;
                    //if there are any, add to the temp table, but only if there isnt such url already
                    if (is_array($links)) {
                        reset ($links);

                        while ($thislink = each($links)) {
                            if (!array_key_exists($thislink[1], $tmp_urls)) {
                                $tmp_urls[$thislink[1]] = 1;
                                $numoflinks++;
                                mysql_query ("insert into ".$mysql_table_prefix."temp (link, level, id) values ('$thislink[1]', '$level', '$sessid')");
                                echo mysql_error();
                            }
                        }
                    }
                } else {
                    printStandardReport('noFollow',$command_line);
                }

                if ($OKtoIndex == 1) {

                    $title = $data['title'];            
                    $host = $data['host'];
                    $path = $data['path'];    
                    $fullhtml = $data['fullhtml'];
                    $fulltxt = $data['fulltext'];
                    $desc = substr($data['description'], 0,254);
                    $url_parts = parse_url($url);
                    $domain_for_db = $url_parts['host'];

                    if (isset($domain_arr[$domain_for_db])) {
                        $dom_id = $domain_arr[$domain_for_db];
                    } else {
                        mysql_query("insert into ".$mysql_table_prefix."domains (domain) values ('$domain_for_db')");
                        $dom_id = mysql_insert_id();
                        $domain_arr[$domain_for_db] = $dom_id;
                    }

                    $wordarray = calc_weights ($wordarray, $title, $host, $path, $data['keywords']);

                    //if there are words to index, add the link to the database, get its id, and add the word + their relation
                    if ((is_array($wordarray) && count($wordarray) > $min_words_per_page) || ($show_images || $save_images)) {
                        if ($md5sum == '') {
                            mysql_query ("insert into ".$mysql_table_prefix."links (site_id, url, title, description, fullhtml, fulltxt, indexdate, size, md5sum, level) values ('$site_id', '$url', '$title', '$desc', '$fullhtml', '$fulltxt', curdate(), '$pageSize', '$newmd5sum', $thislevel)");
                            echo mysql_error();
                            $link_id = mysql_insert_id();

                            if ($save_keywords) save_keywords($wordarray, $link_id, $dom_id);

                            printStandardReport('indexed', $command_line);
                        }else if (($md5sum <> '') && ($md5sum <> $newmd5sum)) { //if page has changed, start updating

                                $result = mysql_query("select link_id from ".$mysql_table_prefix."links where url='$url'");
                                echo mysql_error();
                                $row = mysql_fetch_row($result);
                                $link_id = $row[0];
                                for ($i=0;$i<=15; $i++) {
                                    $char = dechex($i);
                                    mysql_query ("delete from ".$mysql_table_prefix."link_keyword$char where link_id=$link_id");
                                    echo mysql_error();
                                }
                                if ($save_keywords) save_keywords($wordarray, $link_id, $dom_id);
                                $query = "update ".$mysql_table_prefix."links set title='$title', description ='$desc', fulltxt = '$fulltxt', indexdate=now(), size = '$pageSize', md5sum='$newmd5sum', level=$thislevel where link_id=$link_id";
                            mysql_query($query);
                            echo mysql_error();
                            printStandardReport('re-indexed', $command_line); 

                        }
                        
                        if ($show_images || $save_images) $print_images = find_images($fullhtml, $host, $link_id, $save_images, $show_images);
                        
                    }else {
                        printStandardReport('minWords', $command_line);

                    }

                }
            }
        } else {
            $deletable = 1;
            printUrlStatus($url_status['state'], $command_line);

        }
        if ($reindex ==1 && $deletable == 1) {
            check_for_removal($url); 
        } else if ($reindex == 1) {

            }
            if (!isset($all_links)) {
            $all_links = 0;
        }
        if (!isset($numoflinks)) {
            $numoflinks = 0;
        }
        printLinksReport($numoflinks, $all_links, $command_line);
        echo $print_images,"<br>";
    }


    function index_site($url, $reindex, $maxlevel, $soption, $url_inc, $url_not_inc, $can_leave_domain, $save_keywords, $save_images, $show_images, $capture_pages) {

        if ($capture_pages){
            global $nomBase;
            mkdir("C:/Users/Antoine/altuslogic-performance/capture/$nomBase");
        }

        global $mysql_table_prefix, $command_line, $mainurl,  $tmp_urls, $domain_arr, $all_keywords;
        if (!isset($all_keywords)) {
            $result = mysql_query("select keyword_ID, keyword from ".$mysql_table_prefix."keywords");
            echo mysql_error();
            while($row=mysql_fetch_array($result)) {
                $all_keywords[addslashes($row[1])] = $row[0];
            }
        }
        $compurl = parse_url($url);
        if (!isset($compurl['path']) or $compurl['path'] == '')
            $url = $url . "/";

        $t = microtime();
        $a =  getenv("REMOTE_ADDR");
        $sessid = md5 ($t.$a);


        $urlparts = parse_url($url);

        $domain = $urlparts['host'];
        if (isset($urlparts['port'])) {
            $port = (int)$urlparts['port'];
        }else {
            $port = 80;
        }        

        $result = mysql_query("select site_id from ".$mysql_table_prefix."sites where url='$url'");
        echo mysql_error();
        $row = mysql_fetch_row($result);
        $site_id = $row[0];                                                             

        if ($site_id != "" && $reindex == 1) {
            mysql_query ("insert into ".$mysql_table_prefix."temp (link, level, id) values ('$url', 0, '$sessid')");
            echo mysql_error();
            $result = mysql_query("select url, level from ".$mysql_table_prefix."links where site_id = $site_id");
            while ($row = mysql_fetch_array($result)) {
                $site_link = $row['url'];
                $link_level = $row['level'];
                if ($site_link != $url) {
                    mysql_query ("insert into ".$mysql_table_prefix."temp (link, level, id) values ('$site_link', $link_level, '$sessid')");
                }
            }

            $qry = "update ".$mysql_table_prefix."sites set indexdate=now(), spider_depth = $maxlevel, required = '$url_inc'," .
            "disallowed = '$url_not_inc', can_leave_domain=$can_leave_domain where site_id=$site_id";
            mysql_query ($qry);
            echo mysql_error();
        } else if ($site_id == '') {
                mysql_query ("insert into ".$mysql_table_prefix."sites (url, indexdate, spider_depth, required, disallowed, can_leave_domain) " .
                "values ('$url', now(), $maxlevel, '$url_inc', '$url_not_inc', $can_leave_domain)");
                echo mysql_error();
                $result = mysql_query("select site_ID from ".$mysql_table_prefix."sites where url='$url'");
                $row = mysql_fetch_row($result);
                $site_id = $row[0];
            } else {
                mysql_query ("update ".$mysql_table_prefix."sites set indexdate=now(), spider_depth = $maxlevel, required = '$url_inc'," .
                "disallowed = '$url_not_inc', can_leave_domain=$can_leave_domain where site_id=$site_id");
                echo mysql_error();
        }


        $result = mysql_query("select site_id, temp_id, level, count, num from ".$mysql_table_prefix."pending where site_id='$site_id'");
        echo mysql_error();
        $row = mysql_fetch_row($result);
        $pending = $row[0];
        $level = 0;
        $domain_arr = get_domains();
        if ($pending == '') {
            mysql_query ("insert into ".$mysql_table_prefix."temp (link, level, id) values ('$url', 0, '$sessid')");
            echo mysql_error();
        } else if ($pending != '') {
                printStandardReport('continueSuspended',$command_line);
                mysql_query("select temp_id, level, count from ".$mysql_table_prefix."pending where site_id='$site_id'");
                echo mysql_error();
                $sessid = $row[1];
                $level = $row[2];
                $pend_count = $row[3] + 1;
                $num = $row[4];
                $pending = 1;
                $tmp_urls = get_temp_urls($sessid);
            }

            if ($reindex != 1) {
            mysql_query ("insert into ".$mysql_table_prefix."pending (site_id, temp_id, level, count) values ('$site_id', '$sessid', '0', '0')");
            echo mysql_error();
        }


        $time = time();


        $omit = check_robot_txt($url);

        printHeader ($omit, $url, $command_line);
        if ($show_images){
            echo "<input type='button' value='show images' onclick='var list=document.getElementsByClassName(\"groupeImg\"); for (var i=0; i<list.length; i++) list[i].style.display=\"block\";'>
            <input type='button' value='hide images' onclick='var list=document.getElementsByClassName(\"groupeImg\"); for (var i=0; i<list.length; i++) list[i].style.display=\"none\";'><br><br>";
        }

        $mainurl = $url;
        $num = 0;

        while (($level <= $maxlevel && $soption == 'level') || ($soption == 'full')) {
            if ($pending == 1) {
                $count = $pend_count;
                $pending = 0;
            } else
                $count = 0;

            $links = array();

            $result = mysql_query("select distinct link from ".$mysql_table_prefix."temp where level=$level && id='$sessid' order by link DESC");
            echo mysql_error();
            $rows = mysql_num_rows($result);

            if ($rows == 0) {
                break;
            }

            $i = 0;

            while ($row = mysql_fetch_array($result)) {
                $links[] = $row['link'];
            }

            reset ($links);


            while ($count < count($links)) {

                $num++;
                $thislink = $links[$count];
                $urlparts = parse_url($thislink);
                reset ($omit);
                $forbidden = 0;
                foreach ($omit as $omiturl) {
                    $omiturl = trim($omiturl);

                    $omiturl_parts = parse_url($omiturl);
                    if ($omiturl_parts['scheme'] == '') {
                        $check_omit = $urlparts['host'] . $omiturl;
                    } else {
                        $check_omit = $omiturl;
                    }

                    if (strpos($thislink, $check_omit)) {
                        printRobotsReport($num, $thislink, $command_line);
                        check_for_removal($thislink); 
                        $forbidden = 1;
                        break;
                    }
                }

                if (!check_include($thislink, $url_inc, $url_not_inc )) {
                    printUrlStringReport($num, $thislink, $command_line);
                    check_for_removal($thislink); 
                    $forbidden = 1;
                } 

                if ($forbidden == 0) {
                    printRetrieving($num, $thislink, $command_line);
                    $query = "select md5sum, indexdate from ".$mysql_table_prefix."links where url='$thislink'";
                    $result = mysql_query($query);
                    echo mysql_error();
                    $rows = mysql_num_rows($result);
                    if ($rows == 0) {
                        index_url($thislink, $level+1, $site_id, '',  $domain, '', $sessid, $can_leave_domain, $reindex, $save_keywords, $save_images, $show_images, $capture_pages);

                        mysql_query("update ".$mysql_table_prefix."pending set level = $level, count=$count, num=$num where site_id=$site_id");
                        echo mysql_error();
                    }else if ($rows <> 0 && $reindex == 1) {
                            $row = mysql_fetch_array($result);
                            $md5sum = $row['md5sum'];
                            $indexdate = $row['indexdate'];
                            index_url($thislink, $level+1, $site_id, $md5sum,  $domain, $indexdate, $sessid, $can_leave_domain, $reindex, $save_keywords, $save_images, $show_images, $capture_pages);
                            mysql_query("update ".$mysql_table_prefix."pending set level = $level, count=$count, num=$num where site_id=$site_id");
                            echo mysql_error();
                        }else {
                            printStandardReport('inDatabase',$command_line);
                            echo "<br>";
                    }

                }
                $count++;
            }
            $level++;
        }

        mysql_query ("delete from ".$mysql_table_prefix."temp where id = '$sessid'");
        echo mysql_error();
        mysql_query ("delete from ".$mysql_table_prefix."pending where site_id = '$site_id'");
        echo mysql_error();
        printStandardReport('completed',$command_line);


    }

    function index_all() {          
        global $mysql_table_prefix;
        $result=mysql_query("select url, spider_depth, required, disallowed, can_leave_domain from ".$mysql_table_prefix."sites");
        echo mysql_error();
        while ($row=mysql_fetch_row($result)) {
            $url = $row[0];
            $depth = $row[1];
            $include = $row[2];
            $not_include = $row[3];
            $can_leave_domain = $row[4];
            if ($can_leave_domain=='') {
                $can_leave_domain=0;
            }
            if ($depth == -1) {
                $soption = 'full';
            } else {
                $soption = 'level';
            }
            index_site($url, 1, $depth, $soption, $include, $not_include, $can_leave_domain, 1, 0, 1, 0);
        }
    }			

    function get_temp_urls ($sessid) {
        global $mysql_table_prefix;
        $result = mysql_query("select link from ".$mysql_table_prefix."temp where id='$sessid' LIMIT 0,100");
        echo mysql_error();
        $tmp_urls = Array();
        while ($row=mysql_fetch_row($result)) {
            $tmp_urls[$row[0]] = 1;
        }
        return $tmp_urls;

    }

    function get_domains () {
        global $mysql_table_prefix;
        $result = mysql_query("select domain_id, domain from ".$mysql_table_prefix."domains");
        echo mysql_error();
        $domains = Array();
        while ($row=mysql_fetch_row($result)) {
            $domains[$row[1]] = $row[0];
        }
        return $domains;

    }

    function commandline_help() {
        print "Usage: php spider.php <options>\n\n";
        print "Options:\n";
        print " -all\t\t Reindex everything in the database\n";
        print " -u <url>\t Set url to index\n";
        print " -f\t\t Set indexing depth to full (unlimited depth)\n";
        print " -d <num>\t Set indexing depth to <num>\n";
        print " -l\t\t Allow spider to leave the initial domain\n";
        print " -r\t\t Set spider to reindex a site\n";
        print " -m <string>\t Set the string(s) that an url must include (use \\n as a delimiter between multiple strings)\n";
        print " -n <string>\t Set the string(s) that an url must not include (use \\n as a delimiter between multiple strings)\n";
    }

    printStandardReport('quit',$command_line);
    if ($email_log) {
        $indexed = ($all==1) ? 'ALL' : $url;
        $log_report = "";
        if ($log_handle) {
            $log_report = "Log saved into $log_file";
        }
        mail($admin_email, "CRAWL indexing report", "CRAWL has finished indexing $indexed at ".date("y-m-d H:i:s").". ".$log_report);
    }
    if ( $log_handle) {
        fclose($log_handle);
    }

?>
