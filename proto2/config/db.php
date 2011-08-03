<?php
   		$SESSION_NAME = "expos";
        session_name($SESSION_NAME);
        session_start();
        $MAX_UPLOAD_SIZE = 100000;
        $MD5_PREFIX = "astrochew_is_king_of_security";

		
		$DbHost     = "localhost"; 	 
		$DbUser     = "antoine";
		$DbPassword = "klm417al"; 


          if(!mysql_connect($DbHost,$DbUser,$DbPassword))
                  {
          $NOTCONNECTED = TRUE;
                  }
         if(!mysql_select_db($DbDatabase))
                  {
          $NOTCONNECTED = TRUE;
                  }
?>