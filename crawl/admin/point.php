<?php

    $location=$_POST[location];
    if($location){geoLocate($location);}

?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head> 
<form method="POST">
    <input type="text" size="20" name="location">
    <input type="submit" value="Get Coordinates"> 
</form>

<?php 

    function geoLocate($location) {

        $location = urlEncode($location.""); 

        $request  = 'http://maps.google.com/maps/geo?';
        $request .= 'q='.$location.'&';
        $request .= 'key=ABQIAAAAnNatFxH2LX42s_QZL51BihQcqy5cK-7cNBpUzh59xdkTYzbsHhShgAXTgLmpp66o1LxsNI_TMDlROQ'.'&';
        $request .= 'output=xml'.$config_ga['format'].'&';
        $request .= 'oe=utf8';


        $response  = file_get_contents(''.$request.'');
        echo   "<a href=\"".$request."\">Get Coordinate</a>";


        preg_match_all( "/\<Response\>(.*?)\<\/Response\>/s",$response, $bookblocks );
        foreach( $bookblocks[1] as $block )
        {
            preg_match_all( "/\<coordinates\>(.*?)\<\/coordinates\>/",$block,$title );
            preg_match_all( "/\<address\>(.*?)\<\/address\>/",$block,$add );  
            preg_match_all( "/\<ThoroughfareName\>(.*?)\<\/ThoroughfareName\>/",$block,$fare );
            preg_match_all( "/\<PostalCode\>(.*?)\<\/PostalCode\>/",$block,$pc );                    
            preg_match_all( "/\<LocalityName\>(.*?)\<\/LocalityName\>/",$block,$loc );
            preg_match_all( "/\<SubAdministrativeAreaName\>(.*?)\<\/SubAdministrativeAreaName\>/",$block,$adm );
            $pi2=explode(',',$title[1][0]);
            $lat=addslashes($pi2[1]);
            $long=addslashes($pi2[0]);
            $adma=addslashes($adm[1][0]);
            $loca=addslashes($loc[1][0]);
            $farea=addslashes($fare[1][0]);
            $pca=addslashes($pc[1][0]);
            $addaa=addslashes($add[1][0]);

            if($lat=='')$lat="skip";

            echo " (latitude : ".$lat.")(longitude : ".$long.") <br><br>".$title[1][0]."<br>$location<br>$farea <br>$pca <br>$addaa <br>$adma" ;         

        }  

        return $latLong;
    }                           

?>   
