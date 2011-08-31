<?php

    function htmlToISO($text){

        $badchars=array(
        "\xe2\x80\x98", // left single quote
        "\xe2\x80\x99", // right single quote
        "\xe2\x80\x9c", // left double quote
        "\xe2\x80\x9d", // right double quote
        "\xe2\x80\x94", // em dash
        "\xe2\x80\xa6" // ellipses
        );
        $fixedchars=array("'","'",'"','"',"-","...");                     
        $text=str_replace($badchars,$fixedchars,$text);

        $text = str_replace("&amp;","&",$text); // r�sout les probl�mes du type &amp;eacute;   
        $text = html_entity_decode($text,ENT_QUOTES,"ISO-8859-1");   

        if (mb_detect_encoding($text,"UTF-8",true)) $text = utf8_decode($text);      
        return addslashes($text);
    }
    
    
    function sansAccent($text){
        $accent = '�������������';  
        $sansaccent = 'AACEEEEIIOUUU';
        //$accent = '��������������������������������������������������������������'; 
        //$sansaccent = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyyby'; 
        return strtr($text,$accent,$sansaccent); 
    }

?>
