<?php

    function install(){
        global $mysql_table_prefix;
        
        $error = 0;
        mysql_query("create table `".$mysql_table_prefix."sites`(
        site_id int auto_increment not null primary key,
        url varchar(500),
        title varchar(255),
        short_desc text,
        indexdate date,
        spider_depth int default 2,
        required text,
        disallowed text,
        can_leave_domain bool)");
        if (mysql_errno() > 0) {
            print "Error: ";
            print mysql_error();
            print "<br>\n";
            $error += mysql_errno();
        }
        
            	
        mysql_query("create table `".$mysql_table_prefix."links` (
        link_id int auto_increment primary key not null,
        site_id int,
        url varchar(500) not null,
        title varchar(200),
        description varchar(255),
        fullhtml mediumtext,
        fulltxt mediumtext,
        indexdate date,
        size float(2),
        StumbleUpon int,
        Reddit int,
        Facebook_commentsbox_count int,
        Facebook_click_count int,
        Facebook_total_count int,
        Facebook_comment_count int,
        Facebook_like_count int,
        Facebook_share_count int,
        Delicious int,
        GooglePlusOne int,
        Buzz int,
        Twitter int,
        Diggs int,
        Pinterest int,
        LinkedIn int,
        md5sum varchar(32),
        key url (url),
        key md5key (md5sum),
        visible int default 0, 
        level int)");

        if (mysql_errno() > 0) {
            print "Error: ";
            print mysql_error();
            print "<br>\n";
            $error += mysql_errno();
        }
        mysql_query("create table `".$mysql_table_prefix."keywords`    (
        keyword_id int primary key not null auto_increment,
        keyword varchar(30) not null,
        unique kw (keyword),
        key keyword (keyword(10)))");

        if (mysql_errno() > 0) {
            print "Error: ";
            print mysql_error();
            print "<br>\n";
            $error += mysql_errno();
        }

        for ($i=0;$i<=15; $i++) {
            $char = dechex($i);
            mysql_query("create table `".$mysql_table_prefix."link_keyword$char` (
            link_id int not null,
            keyword_id int not null,
            weight int(3),
            domain int(4),
            key linkid(link_id),
            key keyid(keyword_id))");

            if (mysql_errno() > 0) {
                print "Error: ";
                print mysql_error();
                print "<br>\n";
                $error += mysql_errno();
            }
        }

        mysql_query("create table `".$mysql_table_prefix."images`    (
        path varchar(500) primary key not null,
        link_id int not null,
        width int not null,
        height int not null,
        size int not null)");

        if (mysql_errno() > 0) {
            print "Error: ";
            print mysql_error();
            print "<br>\n";
            $error += mysql_errno();
        }
        
       mysql_query("create table `".$mysql_table_prefix."extract`    (
               id int primary key not null auto_increment,
               site mediumtext,
               actual_column mediumtext,
               in mediumtext,
               out mediumtext,
               column mediumtext,
               keep_html mediumtext,
               tag_name mediumtext,
               attrib_name mediumtext,
               attrib_mode mediumtext,
               attrib_value mediumtext,
               start_text mediumtext,
               end_text mediumtext,
               size int not null)");
       
               if (mysql_errno() > 0) {
                   print "Error: ";
                   print mysql_error();
                   print "<br>\n";
                   $error += mysql_errno();
               }
               
       mysql_query("create table `".$mysql_table_prefix."crawl`    (
                  id int primary key not null auto_increment,
                  prefix mediumtext,
                  db mediumtext,
                  in mediumtext,
                  out mediumtext,
                  maxlevel int,
                  reindex int,
                  save_keywords int,
                  show_images int,
                  save_images int,
                  capture_pages int,
                  domaincb int,
                  create_db int,
                  soption mediumtext)");
          
                  if (mysql_errno() > 0) {
                      print "Error: ";
                      print mysql_error();
                      print "<br>\n";
                      $error += mysql_errno();
                  }
                  
       mysql_query("create table `".$mysql_table_prefix."rule`    (
                  id int primary key not null auto_increment,
                  link_id int not null,
                  width int not null,
                  height int not null,
                  size int not null)");
          
                  if (mysql_errno() > 0) {
                      print "Error: ";
                      print mysql_error();
                      print "<br>\n";
                      $error += mysql_errno();
                  }
          
                 
        mysql_query("create table `".$mysql_table_prefix."categories` (
        category_id integer not null auto_increment primary key, 
        category text,
        parent_num integer
        )");

        if (mysql_errno() > 0) {
            print "Error: ";
            print mysql_error();
            print "<br>\n";
            $error += mysql_errno();
        }

        mysql_query("create table `".$mysql_table_prefix."site_category` (
        site_id integer,
        category_id integer
        )");

        if (mysql_errno() > 0) {
            print "Error: ";
            print mysql_error();
            print "<br>\n";
            $error += mysql_errno();
        }

        mysql_query("create table `".$mysql_table_prefix."temp` (
        link varchar(500),
        level integer,
        id varchar (32)
        )");

        if (mysql_errno() > 0) {
            print "Error: ";
            print mysql_error();
            print "<br>\n";
            $error += mysql_errno();
        }

        mysql_query("create table `".$mysql_table_prefix."pending` (
        site_id integer,
        temp_id varchar(32),
        level integer,
        count integer,
        num integer
        )");

        if (mysql_errno() > 0) {
            print "Error: ";
            print mysql_error();
            print "<br>\n";
            $error += mysql_errno();
        }

        mysql_query("create table `".$mysql_table_prefix."query_log` (
        query varchar(500),
        time timestamp,
        elapsed float(2),
        results int, 
        primary key (query))");

        if (mysql_errno() > 0) {
            print "Error: ";
            print mysql_error();
            print "<br>\n";
            $error += mysql_errno();
        }

        mysql_query("create table `".$mysql_table_prefix."domains` (
        domain_id int auto_increment primary key not null,    
        domain varchar(255))");

        if (mysql_errno() > 0) {
            print "Error: ";
            print mysql_error();
            print "<br>\n";
            $error += mysql_errno();
        }


        if ($error >0) {
            print "<b>Creating tables failed. Consult the above error messages.</b>";
        } else {
            print "<b>Creating tables successfully completed. Go to <a href=\"admin.php\">admin.php</a> to start indexing.</b>";
        }
    }

?>
