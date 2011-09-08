<?php

    include "../crawl/admin/configSearch/time_funcs.php";

    function tableExiste($table){
        $sql = "SELECT COUNT(*) FROM $table";
        return mysql_query($sql);
    }

    function updateLog($action,$details,$hash,$temps){
        global $nomMaitre,$nomBase;
        $ip = $_SERVER['REMOTE_ADDR'];
        $sql = "INSERT INTO $nomMaitre.log SET action='$action', details='$details', hash='$hash', heure=NOW(), temps='$temps', ip='$ip'";
        mysql_query($sql);   
    }

    /***
    * Effectue la recherche d'une cha�ne de caract�res  
    * @param mixed $text : la cha�ne � chercher
    * @param mixed $mode : le mode de recherche (d�but,milieu,fin)
    * @param mixed $methode : la m�thode utilis�e (directe,sous-tables,index)
    * @param mixed $tabCol : les colonnes qui nous int�ressent
    * @param mixed $limite : le nombre de r�sultats � renvoyer
    * @param mixed $source : 1 pour le clavier (autocomplete), 0 pour la souris (bouton recherche)
    * @param mixed $coord : les coordonn�es (latitude,longitude)
    */
    function recherche($text, $hash, $mode, $methode, $tabCol, $page, $limite, $source, $coord){             

        global $nomTable,$nomColonne,$ordreMax;
        // debug                               
        // return array("resultats" => array(array("name"=>$hash)), "temps" => 0);
        $temps = start_timer();

        if ($methode=='tables'){
            $table = getSousTable($nomTable,$nomColonne,$text);
            if ($table!="") $table = "z_".$nomTable."_".$nomColonne."_".$table; 
        }
        else $table = getTable($methode);

        if ($table==""){
            $table = $nomTable;
            echo "Sous-tables introuvables : utilisation de la table principale.<br><br>";
        }

        foreach ($tabCol as $key=>$col){
            $tabCol[$key] = $table.".".$col;
        }
        $selecCol = strtolower(implode(", ",$tabCol));

        if ($coord!=null){
            /*$lat = $coord[0];
            $lon = $coord[1];
            $rayon = 6370;
            $dist = "(ACOS( SIN($lat*PI()/180) * SIN(latitude*PI()/180) + COS($lat*PI()/180) * COS(latitude*PI()/180) * COS(($lon-longitude)*PI()/180)) * $rayon) AS distance";
            if ($table==$nomTable) $sql = "SELECT $nomColonne,latitude,longitude,".$dist." FROM $nomTable WHERE latitude NOT LIKE '' AND";
            else $sql = "SELECT $table.$nomColonne,latitude,longitude,".$dist." FROM $nomTable, $table WHERE $nomTable.id = $table.id AND latitude NOT LIKE '' AND";*/
        }
        else {
            if ($methode=='mot' || $methode=='phrase'){
                // pas de jointure � faire dans ce cas
                $sql = "SELECT $selecCol FROM $table WHERE ";
                $mot = array($text);
            }
            else {
                $selecTables = "$table".($nomTable==$table?"":", $nomTable");
                $jointure = ($nomTable==$table?"":"$nomTable.id=$table.id AND ");
                $sql = "SELECT $selecCol FROM $selecTables WHERE $jointure";
                $mot = explode(" ",$text);
            }
            if ($mode=='regexp'){
                // mode expression r�guli�re
                $mot = array($text);
                $like = "RLIKE";
            }
            else $like = "LIKE";
        }
        $debut = ($mode=="debut" || $mode=='regexp')?"":"%";
        $fin = ($mode=="fin" || $mode=='regexp')?"":"%";
        $and = "";

        for ($i=0; $i<sizeof($mot); $i++){
            if (strlen($mot[$i])>0){
                // pas toujours n�cessaire (d�pend si la table principale est bien encod�e)
                $mot[$i] = addslashes(utf8_encode($mot[$i]));
                $sql .= $and." $table.$nomColonne $like '".$debut."$mot[$i]".$fin."'";
                $and = " AND"; 
                if ($i==0){
                    $debut = $fin = "%";
                }
            }
        }                                     

        $sql .= ($coord!=null? " ORDER BY distance": ((strpos($selecCol,"nombre")!==false)? " ORDER BY nombre DESC": "")).($page>0? "": " LIMIT ".$limite);
        $result = mysql_query($sql);  
        //return array("resultats" => array(array("name"=>$sql."<br>".mysql_error())), "temps" => 0);

        $array=array();
        
        $cpt = 0;
        if ($page>1){
            while ($tab = mysql_fetch_array($result)){
                if (++$cpt==($page-1)*$limite) break;   
            }
        }                                         
        $cpt=0;
        while ($tab = mysql_fetch_array($result)){ 
            array_push($array,$tab);
            if (++$cpt==$limite) break;                                                                     
        }              

        updateLog($source?"Suggestion":"Recherche",$text,$hash,$temps=end_timer($temps));

        if ($page>0){
            return array("resultats" => $array, "nombre" => mysql_num_rows($result), "temps" => $temps);
        }
        return array("resultats" => $array, "temps" => $temps);
    }  

    /**
    * Renvoie la table appropri�e dans laquelle faire une recherche
    * @param mixed $methode : la m�thode de recherche
    */
    function getTable($methode){

        global $nomTable,$nomColonne;

        if ($methode=='direct') return $nomTable;
        if ($methode=='mot') return "y_".$nomTable."_".$nomColonne."_keyword";
        if ($methode=='phrase') return "y_".$nomTable."_".$nomColonne."_keyphrase";
        return; 

    }  


    function getSousTable($table,$colonne,$text){

        $ordreMax = getOrdreMax($table,$colonne);
        if ($ordreMax=="erreur") return "";
        $soustable = "";
        $mot = explode(" ",$text);
        $long = max(array_map("strlen",$mot));
        $truc = $long>$ordreMax?$ordreMax:$long;
        while ($soustable=="" && $truc>0){
            $soustable = getSousTableLong($table,$colonne,$text,$truc--);
        }

        return $soustable;

    }

    /**
    * Renvoie la sous-table appropri�e dans laquelle chercher
    * une cha�ne de caract�res (la moins remplie)
    * @param mixed $table : la table principale ou d'index  
    * @param mixed $colonne : la colonne  
    * @param mixed $text : la cha�ne � chercher
    * @param mixed $long : l'ordre de la table
    */
    function getSousTableLong($table,$colonne,$text,$long){

        $sql = "SELECT name,MIN(nombre) FROM y_".$table."_".$colonne."_stats WHERE";   
        $liste = array();   
        $or="";
        for ($i=0; $i<strlen($text)-$long+1; $i++){
            $t = strtoupper(substr($text,$i,$long));
            // ignore les suites de caract�res contenant un espace
            if (strpos($t," ")===FALSE && array_search($t,$liste)===FALSE){
                array_unshift($liste,$t);
                $sql .=  $or." name = '".addslashes($t)."'";
                $or = " OR";
            }
        }                                     
        //echo $sql,"<br>";   
        $result = mysql_query($sql) or die($sql."<br>".mysql_error());
        $tab = mysql_fetch_array($result);   
        return $tab['name'];
    }

    function getOrdreMax($table,$colonne){
        $result = mysql_query("SELECT MAX(ordre) FROM y_".$table."_".$colonne."_stats");
        if (!$result) return "error";
        return mysql_result($result,0);
    }


    function decodeUTF($string){
        if (mb_detect_encoding($string,"UTF-8",true)) return utf8_decode($string);
        return $string;
    }

?>
