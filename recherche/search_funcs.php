<?php

    include "../crawl/admin/configSearch/time_function.php";

    function tableExiste($table){
        $sql = "SELECT COUNT(*) FROM $table";
        return mysql_query($sql);
    }

    function updateLog($action,$temps){
        global $nomMaitre,$nomBase,$nomTable;  
        mysql_select_db($nomMaitre);  
        $sql = "INSERT INTO log SET action='$action', heure=NOW(), temps='$temps'";
        mysql_query($sql); 
        mysql_select_db($nomBase);     
    }

    /***
    * Effectue la recherche d'une chaîne de caractères  
    * @param mixed $text : la chaîne à chercher
    * @param mixed $mode : le mode de recherche (début,milieu,fin)
    * @param mixed $methode : la méthode utilisée (directe,sous-tables,index)
    * @param mixed $selecCol : les colonnes qui nous intéressent
    * @param mixed $limite : le nombre de résultats à renvoyer
    * @param mixed $coord : les coordonnées (latitude,longitude)
    */
    function recherche($text, $mode, $methode, $selecCol, $limite, $coord){             

        global $nomTable, $nomColonne,$ordreMax;
        $temps = start_timer();
        $result;

        if ($mode=="tout"){
            // mode expression régulière (à changer)
            //$sql = "SELECT $nomColonne FROM $nomTable WHERE $nomColonne RLIKE '$text' LIMIT ".$limite;                                    
            //$result = mysql_query($sql) or die($sql."<br>".mysql_error());
        }

        else {       
            $table;

            if ($methode=='tables'){
                $table = getSousTable($text);
                if ($table!="") $table = "z_".$nomTable."_".$nomColonne."_".$table; 
            }
            else $table = getTable($methode);

            if ($table!=""){

                $selecCol = str_replace($nomColonne,"$table.".$nomColonne,$selecCol);
                // cas où mode=index ???
                $selecTables = "$table".($nomTable==$table?"":", $nomTable");
                $jointure = ($nomTable==$table?" ":"$nomTable.id=$table.id AND ");
                $sql;

                if ($coord!=null){
                    /*$lat = $coord[0];
                    $lon = $coord[1];
                    $rayon = 6370;
                    $dist = "(ACOS( SIN($lat*PI()/180) * SIN(latitude*PI()/180) + COS($lat*PI()/180) * COS(latitude*PI()/180) * COS(($lon-longitude)*PI()/180)) * $rayon) AS distance";
                    if ($table==$nomTable) $sql = "SELECT $nomColonne,latitude,longitude,".$dist." FROM $nomTable WHERE latitude NOT LIKE '' AND";
                    else $sql = "SELECT $table.$nomColonne,latitude,longitude,".$dist." FROM $nomTable, $table WHERE $nomTable.id = $table.id AND latitude NOT LIKE '' AND";*/
                }
                else {                                                                   
                    $sql = "SELECT $selecCol FROM $selecTables WHERE $jointure";      
                }
                $debut = $mode=="debut"?"":"%";
                $fin = $mode=="fin"?"":"%";
                $and = "";

                $mot = explode(" ",$text);
                for ($i=0; $i<sizeof($mot); $i++){
                    if (strlen($mot[$i])>0){
                        $sql .= $and." $table.$nomColonne LIKE '".$debut."$mot[$i]".$fin."'";
                        $and = " AND"; 
                        if ($i==0){
                            $debut = $fin = "%";
                        }
                    }
                }                                     

                $sql .= ($coord!=null? " ORDER BY distance": ((strpos($selecCol,"nombre")!==false)? " ORDER BY nombre DESC": ""))." LIMIT ".$limite;   
                $result = mysql_query($sql) or die($sql."<br>".mysql_error());  

            }
        }

        $array=array(); 
        while ($tab = mysql_fetch_array($result)){ 
            array_push($array,$tab);                                                                     
        }                

        updateLog("Recherche ".$text,$temps=end_timer($temps));
        return array("resultats" => $array, "temps" => $temps);
    }  

    /**
    * Renvoie la table appropriée dans laquelle faire une recherche
    * @param mixed $methode : la méthode de recherche
    */
    function getTable($methode){

        global $nomTable,$nomColonne;

        if ($methode=='direct') return $nomTable;
        if ($methode=='mot') return "y_".$nomTable."_".$nomColonne."_keyword";
        if ($methode=='tout') return "y_".$nomTable."_".$nomColonne."_keyphrase";
        return; 

    }

    function getSousTable($text){
        global $ordreMax;

        $table = "";
        $mot = explode(" ",$text);
        $long = max(array_map("strlen",$mot));
        $truc = $long>$ordreMax?$ordreMax:$long;
        while ($table=="" && $truc>0){
            $table = getSousTableLong($text,$truc--);
        }
        
        return $table;

    }

    /**
    * Renvoie la sous-table appropriée dans laquelle chercher
    * une chaîne de caractères (la moins remplie)
    * @param mixed $text : la chaîne à chercher
    * @param mixed $long : l'ordre de la table
    */
    function getSousTableLong($text,$long){

        global $nomTable,$nomColonne;       
        $sql = "SELECT name,MIN(nombre) FROM y_".$nomTable."_".$nomColonne."_stats WHERE";   
        $liste = array();   
        $or="";
        for ($i=0; $i<strlen($text)-$long+1; $i++){
            $t = strtoupper(substr($text,$i,$long));
            // ignore les suites de caractères contenant un espace
            if (strpos($t," ")===FALSE && array_search($t,$liste)===FALSE){
                array_unshift($liste,$t);
                $sql .=  $or." name = '".$t."'";
                $or = " OR";
            }
        }                                     
        //echo $sql,"<br>";   
        $result = mysql_query($sql) or die(mysql_error());
        $tab = mysql_fetch_array($result);   
        return $tab['name'];
    } 


?>
