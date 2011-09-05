<?php  
    include "configSearch/cookie.php";
    include "install_funcs.php";
?>
<html>
    <head>		
        <title>
            Spider installation script.
        </title>
        <LINK REL=STYLESHEET HREF="admin.css" TYPE="text/css">
    </head>
    <body>
        <h2>CRAWL installation script.</h2>
        <?php
            error_reporting(E_ALL);

            $settings_dir = "../settings";
            include "$settings_dir/database.php";

            $success = mysql_pconnect ($DbHost, $DbUser, $DbPassword);
            if (!$success)
                die ("<b>Cannot connect to database, check if username, password and host are correct.</b>");
            $success = mysql_select_db ($nomBase);
            if (!$success) {
                print "<b>Cannot choose database, check if database name is correct.";
                die();
            }
            $success = mysql_select_db ($nomBase); 

            install();
        ?>
    </body>
</html>