<?php
   $SESSION_NAME = "expo";
        session_name($SESSION_NAME);
        session_start();
        $MAX_UPLOAD_SIZE = 100000;
        $MD5_PREFIX = "astrochew_is_king_of_security";

$DbHost     = "localhost"; // The host where the MySQL server resides
$DbDatabase = "antoine_dev"; // The database you are going to use
$DbUser     = "antoine"; // Username
$DbPassword = "klm417al"; // Password


                  if(!mysql_connect($DbHost,$DbUser,$DbPassword))
                  {
          $NOTCONNECTED = TRUE;
                  }
                  if(!mysql_select_db($DbDatabase))
                  {
          $NOTCONNECTED = TRUE;
                  }
?>