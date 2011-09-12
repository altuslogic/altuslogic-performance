<?php

    function extractScreen(){
        global $mysql_table_prefix, $site, $in, $out, $column, $tag_name, $attrib_name, $attrib_mode, $attrib_value, $start_text, $end_text;
        $result = mysql_query("SELECT site_id,url FROM ".$mysql_table_prefix."sites");
        echo mysql_error();
        $printSites = "";
        while ($tab = mysql_fetch_array($result)){
            $selected = ($tab['site_id']==$site? "selected": "");
            $printSites .= "<option $selected value='$tab[site_id]'>$tab[url]</option>";
        }
    ?>
    <br><div class="indexoptions"><table>
            <form action="extract.php" method="post"> 
            <tr><td><b>Site:</b></td><td><select name="site">
                        <?php echo $printSites; ?>
                    </select></td></tr>
            <tr><td><b>URL must include:</b></td><td><input type="text" name="in" value="<?php print $in;?>"></td></tr> 
            <tr><td><b>URL must not include:</b></td><td><input type="text" name="out" value="<?php print $out;?>"></td></tr>
            <tr><td><b>Column to save in:</b></td><td><input type="text" name="column" value="<?php print $column;?>"></td></tr> 
            <tr><td><b>HTML Container:</b></td><td><input type="text" name="tag_name" value="<?php print $tag_name;?>"></td></tr>
            <tr><td><b>Attribute:</b></td><td>              
                    <input type="text" name="attrib_name" value="<?php print $attrib_name; ?>" style="width:100px;">
                    <select name="attrib_mode">
                        <option <?php if ($attrib_mode=='exact') echo 'selected' ?> value="exact">equals</option>
                        <option <?php if ($attrib_mode=='contains') echo 'selected' ?> value="contains">contains</option> 
                    </select>
                    <input type="text" name="attrib_value" value="<?php print $attrib_value; ?>">  
                </td></tr>
            <tr><td><b>Start text:</b></td><td><textarea name="start_text" cols=35 rows=2 wrap="virtual"><?php print $start_text;?></textarea></td></tr> 
            <tr><td><b>End text:</b></td><td><textarea name="end_text" cols=35 rows=2 wrap="virtual"><?php print $end_text;?></textarea></td></tr></table>
        <center><input type="submit" value="Start extracting"></center> 
        </form></div>
    <?php
    }

    function geoScreen(){
        global $nomBase,$nomTable,$location,$street,$city,$province,$country,$action;

        $sql = "SHOW COLUMNS FROM ".$nomTable;
        $result = mysql_query($sql);
        echo mysql_error();

        if ($result){
            $t = array("location","street","city","province","country");
            foreach ($t as $val){
                $printCol[$val] = "<option></option>";
            }
            while ($ligne=mysql_fetch_array($result)){ 
                foreach ($t as $val){
                    $printCol[$val] .= "<option".($$val==$ligne['Field']?" selected":"").">".$ligne['Field']."</option>";
                }
            }
        }

    ?>

    <table><tr><td valign="top">
                <div class="divNoScroll">
                    <h2>Localisation</h2>
                    <form method="post">
                        <table><tr><td>Adresse complète : </td><td><select name="location"><?php echo $printCol['location']; ?></select></td></tr>
                            <tr><td>Rue : </td><td><select name="street"><?php echo $printCol['street']; ?></select></td></tr>
                            <tr><td>Ville : </td><td><select name="city"><?php echo $printCol['city']; ?></select></td></tr>
                            <tr><td>Région : </td><td><select name="province"><?php echo $printCol['province']; ?></select></td></tr>
                            <tr><td>Pays : </td><td><select name="country"><?php echo $printCol['country']; ?></select></td></tr></table>
                        <p align='center'><input type="submit" value="validate" name="action"><input type="submit" name="action" value="localize"></p> 
                    </form>
                </div>
            </td><td valign="top">
                <div class="divNoScroll">
                    <h2>Stats</h2>
                    <?php if ($action=='validate') echo localizeStats(); ?>
                </div>
            </td></tr>
    </table>

    <?php
    }


    function localizeStats(){
        global $nomTable;

        $print = "";
        $address = "";
        foreach ($_POST as $key=>$val){
            if ($val!="" && $key!='action'){         
                $result = mysql_query("SELECT COUNT($val) FROM $nomTable WHERE $val != ''");
                $row = mysql_fetch_row($result);
                $print .= "$key : ".$row[0]."<br>";
                $address .= ($address==''?"":", ").$val;
            }
        }
        $result = mysql_query("SELECT COUNT(*) FROM $nomTable WHERE geo_adresse IS NULL AND CONCAT($address)!=''");
        if ($result) $print .= "<br>Restants : ".mysql_result($result,0);
        return $print;
    }


    function localize(){
        global $nomTable;

        mysql_query("ALTER TABLE $nomTable ADD geo_xml TEXT, ADD geo_adresse VARCHAR(255), ADD geo_codePays VARCHAR(3), ADD geo_pays VARCHAR(30), ADD geo_region VARCHAR(30), ADD geo_departement VARCHAR(30),
        ADD geo_ville VARCHAR(30), ADD geo_codePostal VARCHAR(10), ADD geo_quartier VARCHAR(30), ADD geo_rue VARCHAR(100), ADD geo_latitude DOUBLE(10,7), ADD geo_longitude DOUBLE(10,7), ADD geo_status ENUM('ok','multiple','error')");

        $address = "";
        foreach ($_POST as $key=>$val){         
            if ($val!="" && $key!='action') $address .= ($address==''?"":",").$val;
        }

        $primary = getPrimaryKey($nomTable);
        $result = mysql_query("SELECT $primary,CONCAT_WS(' ',$address) as adresse FROM $nomTable WHERE geo_adresse IS NULL AND CONCAT($address)!='' LIMIT 1");
        startProgress("Localisation");

        $total = mysql_num_rows($result);
        $cpt = 0; $progress = 0;
        while ($tab = mysql_fetch_array($result)){  

            $res = geolocate($tab['adresse']);
            foreach ($res as $key => $val){
                $res[$key] = "geo_".$key."='".$val."'";
            }
            $action = implode(", ",$res);
            mysql_query("UPDATE $nomTable SET $action WHERE $primary=$tab[$primary]") or die(mysql_error()."<br>".$action);

            $pourcent = round(100*(++$cpt)/$total);
            if ($pourcent>$progress){
                $progress = $pourcent;
                updateProgress("Localisation",$progress);
            }
        }    
    }


    function geoLocate($location) {

        $location = urlencode($location); 

        $request  = 'http://maps.google.com/maps/geo?';
        $request .= 'q='.$location.'&';
        $request .= 'key=ABQIAAAAnNatFxH2LX42s_QZL51BihQcqy5cK-7cNBpUzh59xdkTYzbsHhShgAXTgLmpp66o1LxsNI_TMDlROQ'.'&';
        $request .= 'output=xml'.$config_ga['format'].'&';
        $request .= 'oe=utf8';

        $response  = file_get_contents($request);

        preg_match_all( "/\<Response\>(.*?)\<\/Response\>/s",$response, $bookblocks );  
        foreach( $bookblocks[1] as $block )
        {
            preg_match_all( "/\<coordinates\>(.*?)\<\/coordinates\>/",$block,$coord );
            preg_match_all( "/\<address\>(.*?)\<\/address\>/",$block,$adresse );  
            preg_match_all( "/\<ThoroughfareName\>(.*?)\<\/ThoroughfareName\>/",$block,$rue );
            preg_match_all( "/\<DependentLocalityName\>(.*?)\<\/DependentLocalityName\>/",$block,$quartier );
            preg_match_all( "/\<PostalCodeNumber\>(.*?)\<\/PostalCodeNumber\>/",$block,$codePostal );                    
            preg_match_all( "/\<LocalityName\>(.*?)\<\/LocalityName\>/",$block,$ville );
            preg_match_all( "/\<SubAdministrativeAreaName\>(.*?)\<\/SubAdministrativeAreaName\>/",$block,$departement ); 
            preg_match_all( "/\<AdministrativeAreaName\>(.*?)\<\/AdministrativeAreaName\>/",$block,$region );
            preg_match_all( "/\<CountryName\>(.*?)\<\/CountryName\>/",$block,$pays );
            preg_match_all( "/\<CountryNameCode\>(.*?)\<\/CountryNameCode\>/",$block,$codePays );
            
            $res = array(); 
            if (!isset($coord[1][0])) return $res;
            $coord=explode(',',$coord[1][0]);
            $res['longitude']=addslashes($coord[0]);
            $res['latitude']=addslashes($coord[1]);        
            $t = array("adresse","rue","quartier","codePostal","ville","departement","region","pays","codePays");

            foreach ($t as $val){
                if (isset(${$val}[1][0])){
                    $res[$val] = addslashes(utf8_decode(${$val}[1][0]));
                }
            }             

        }  

        return $res;
    }

?>
