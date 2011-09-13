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
                        <p align='center'><input type="submit" name="action" value="localize"></p> 
                    </form>
                </div>
            </td><td valign="top">
                <div class="divNoScroll">
                    <h2>Stats</h2>
                    <?php echo localizeStats(); ?>
                </div>
            </td></tr>
    </table>

    <?php
    }


    function localizeStats(){
        global $nomTable;

        $print = "";
        $address = "";

        $liste = getParam();
        foreach ($liste as $key=>$val){     
            $result = mysql_query("SELECT COUNT($val) FROM $nomTable WHERE $val != ''");
            $row = mysql_fetch_row($result);
            $print .= "$key : ".$row[0]."<br>";
            $address .= ($address==''?"":", ").$val;
        }
        $result = mysql_query("SELECT geo_status,COUNT(*) AS compte FROM $nomTable WHERE CONCAT($address)!='' GROUP BY geo_status ORDER BY compte DESC");
        $print .= "<br><table border=1><tr><td>status</td><td>count</td></tr>";
        while ($tab = mysql_fetch_array($result)){
            $status = $tab['geo_status'];
            if ($status=='') $status="à faire"; 
            $print .= "<tr><td><a href='?status=$status'>$status</a></td><td>".$tab['compte']."</td></tr>";
        }
        $print .= "</table>";
        return $print;
    }

    function getParam(){
        global $location,$street,$city,$province,$country; 
        $liste = array("location","street","city","province","country");
        $res = array();
        foreach ($liste as $val){
            if ($$val!='') $res[$val] = $$val;
        }
        return $res;
    }


    function localize(){
        global $nomTable;

        mysql_query("ALTER TABLE $nomTable ADD geo_xml TEXT, ADD geo_adresse VARCHAR(255), ADD geo_codePays VARCHAR(3), ADD geo_pays VARCHAR(30), ADD geo_region VARCHAR(30), ADD geo_departement VARCHAR(30),
        ADD geo_ville VARCHAR(30), ADD geo_codePostal VARCHAR(10), ADD geo_quartier VARCHAR(30), ADD geo_rue VARCHAR(100), ADD geo_latitude DOUBLE(10,7), ADD geo_longitude DOUBLE(10,7), ADD geo_status ENUM('ok','multiple','error')");

        $liste = getParam();
        $address = implode(",",$liste);      

        if ($address=="") return;            
        $primary = getPrimaryKey($nomTable);
        $result = mysql_query("SELECT $primary,$address,CONCAT_WS(' ',$address) as monAdresse FROM $nomTable WHERE geo_status IS NULL AND CONCAT($address)!='' LIMIT 10");
        echo mysql_error();                                                                                                                 
        startProgress("Localisation");

        $total = mysql_num_rows($result);
        $cpt = 0; $progress = 0;
        echo "<table align='center' border=1 style='width:98%'><tr><td><b>$primary<b></td><td><b>status</b></td>";
        foreach ($liste as $val) echo "<td><b>$val</b></td>";
        echo "<td><b>résultat</b></td><td><b>latitude</b></td><td><b>longitude</b></td><td><b>XML</b></td></tr>"; 

        while ($tab = mysql_fetch_array($result)){  

            $res = geolocate($tab['monAdresse']);
            echo "<tr><td>$tab[$primary]</td><td>$res[status]</td>";
            foreach ($liste as $val) echo "<td><input type='text' value=\"".$tab[$val]."\" style='width:100%'></td>"; 
            echo "<td>$res[adresse]</td><td>$res[latitude]</td><td>$res[longitude]</td><td><a href='".createRequest($tab['monAdresse'])."'>XML</a></td></tr>";                                                               

            unset($res['request']);
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
        echo "</table>";   
    }


    function createRequest($location){
        $location = urlencode(utf8_encode($location));          

        $request  = 'http://maps.google.com/maps/geo?';
        $request .= 'q='.$location.'&';
        $request .= 'key=ABQIAAAAnNatFxH2LX42s_QZL51BihQcqy5cK-7cNBpUzh59xdkTYzbsHhShgAXTgLmpp66o1LxsNI_TMDlROQ'.'&';
        $request .= 'output=xml'.$config_ga['format'].'&';
        $request .= 'oe=utf8';

        return $request;
    }


    function geoLocate($location) {

        $request = createRequest($location);
        $response  = file_get_contents($request);
        $res = array("request" => $request,"xml" => addslashes($response));

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

            if (!isset($coord[1][0])){
                $res["status"]="error";
                break;
            }
            $res["status"]="ok"; 
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


    function list_status($status){
        global $nomTable;

        $liste = getParam();
        $address = implode(",",$liste);
        if ($address=="") return;            
        $primary = getPrimaryKey($nomTable);
        $where = $status=="à faire"? "geo_status IS NULL AND CONCAT($address)!=''" :"geo_status = '$status'";

        $result = mysql_query("SELECT $primary,$address,CONCAT_WS(' ',$address) as monAdresse,geo_status,geo_adresse,geo_latitude,geo_longitude FROM $nomTable WHERE $where LIMIT 1000");

        echo "<table align='center' border=1 style='width:98%'><tr><td><b>$primary</b></td><td><b>status</b></td>";
        foreach ($liste as $val) echo "<td><b>$val</b></td>";
        echo "<td><b>update</b></td><td><b>résultat</b></td><td><b>latitude</b></td><td><b>longitude</b></td><td><b>XML</b></td></tr>";
        while ($tab = mysql_fetch_array($result)){  
            echo "<tr><form method='post'><input type='hidden' name='modify' value='$tab[$primary]'><input type='hidden' name='status' value='$status'><td>$tab[$primary]</td><td>$tab[geo_status]</td>";
            foreach ($liste as $val) echo "<td><input type='text' name='$val' value=\"".$tab[$val]."\" style='width:100%'></td>"; 
            echo "<td><input type='submit' value='ok'></td><td>$tab[geo_adresse]</td><td>$tab[geo_latitude]</td><td>$tab[geo_longitude]</td><td><a href='".createRequest($tab['monAdresse'])."'>XML</a></td></form></tr>"; 
        }
        echo "</table>";

    }


    function update_address($modify){
        global $nomTable;
        $tab = array();
        foreach ($_POST as $key => $val){
            if ($key != "action" && $key != "modify" && $key != "status"){
                $tab[] = $key." = '$val'";
            }
        }
        $primary = getPrimaryKey($nomTable);
        mysql_query("UPDATE $nomTable SET geo_status=NULL,".implode(",",$tab)." WHERE $primary=$modify") or die("UPDATE $nomTable SET geo_status=NULL,".implode(",",$tab)." WHERE $primary=$modify");
    }

?>
