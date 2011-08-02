<?php

    $SESSION_NAME = "expo";
    session_name($SESSION_NAME);
    session_start();
    $MAX_UPLOAD_SIZE = 100000;
    $MD5_PREFIX = "astrochew_is_king_of_security";

    $host = "localhost";
    $user = "antoine"; 
    $pass = "klm417al";
    $nom1 = "antoine_intro";
    $nom2 = "antoine_dev";
    $table = "business_entry_10k";

    mysql_connect($host,$user,$pass);
    mysql_select_db($nom1);   
    mysql_select_db($nom2);  

    mysql_query("CREATE TABLE ".$nom2.".".$table." AS SELECT * FROM ".$nom1.".".$table);

?>
