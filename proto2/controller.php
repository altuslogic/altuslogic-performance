<?php

    function tableExiste($nomTable){
        $sql = "SELECT COUNT(*) FROM $nomTable";
        return mysql_query($sql);
    }

    function creeLog(){
        global $nomBase; 
        $temps = start_timer();
        $sql = "CREATE TABLE y_".$nomBase."_log (
        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `action` varchar(255) NOT NULL,
        `heure` datetime NOT NULL,
        `temps` float NOT NULL DEFAULT '0', 
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        $result = mysql_query($sql);
        if ($result) updateLog("Cr�ation du log",end_timer($temps));
    }

    function updateLog($action,$temps){
        global $nomBase;
        $sql = "INSERT INTO y_".$nomBase."_log SET action='$action', heure=NOW(), temps='$temps'";
        mysql_query($sql); 
    }

    function creeTables(){
        global $nomBase, $nomColonne, $ordreMax, $thres;
        $temps = start_timer();
        creeSousTables("");    
        for ($i=2; $i<=$ordreMax; $i++){
            $sql = "SELECT name FROM y_".$nomBase."_".$nomColonne."_stats WHERE ordre='$i'-1 AND nombre>'$thres'";
            $result = mysql_query($sql) or die(mysql_error());
            if (mysql_num_rows($result)==0) break;
            while ($tab = mysql_fetch_array($result)){
                creeSousTables($tab['name']);
            } 
        }
        progressBar("Cr�ation des tables termin�e",100); 
        updateLog("Cr�ation des tables ".$nomColonne,end_timer($temps));           
    }

    function clearTables(){
        global $nomColonne;      
        operation("clearTable","R�initialisation tables ".$nomColonne); 
    }

    function deleteTables(){  
        global $nomColonne;     
        operation("deleteTable","Suppression tables ".$nomColonne); 
    }

    function performances(){
        operation("performance","Performances");
    }

    function creeStats(){
        global $nomBase,$nomColonne,$ordreMax; 
        clearStats();
        $sql = "CREATE TABLE IF NOT EXISTS y_".$nomBase."_".$nomColonne."_stats (
        `name` varchar($ordreMax) NOT NULL,
        `ordre` tinyint(3) UNSIGNED NOT NULL,
        `nombre` int(11) UNSIGNED NOT NULL DEFAULT '0',
        `temps` float NOT NULL DEFAULT '0', 
        PRIMARY KEY (`name`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        mysql_query($sql); 
    }

    function phase1(){
        mysql_query("RESET QUERY CACHE");           
        operation("methode1","M�thode 1"); 
    }

    function phase2(){
        mysql_query("RESET QUERY CACHE");            
        operation("methode2","M�thode 2");
    }

    /***
    * R�alise une op�ration quelconque sur tous les mots de la table Stats
    * @param mixed $op : le nom de l'op�ration
    * @param mixed $comm : le texte � afficher dans la barre de progression
    * */
    function operation($op,$comm){ 
        global $nomBase,$nomColonne;
        $temps = start_timer();
        $sql = "SELECT name FROM y_".$nomBase."_".$nomColonne."_stats";
        $result = mysql_query($sql);
        $count_table=mysql_num_rows($result);  //Richard
        $cpt=0;
        $progress=0;
        while ($tab = mysql_fetch_array($result)){
            $op($tab['name']);
            echo $tab['name']," "; //Richard
            $cpt++;
            $pourcent = round(100*$cpt/$count_table);
            if ($pourcent > $progress){
                $progress = $pourcent;
                progressBar($comm,$progress);
            }                               
        } 
        updateLog($comm,end_timer($temps));
    }

    /***
    * Cr�ation et remplissage des sous-tables contenant
    * tous les �l�ments de la table d'origine dont le nom contient un certain mot 
    * @param mixed $debut : le mot
    */
    function creeSousTables($debut){
        global $nomBase,$nomColonne;

        $pourcent = 0;
        for ($i=0; $i<strlen($debut); $i++){
            $pourcent += (ord(substr($debut,$i,1))-ord("A"))*100/pow(26,$i+1);
        }
        $pourcent = round($pourcent);
        progressBar("Cr�ation des tables $debut"."x",$pourcent);

        for ($lettre=ord("A"); $lettre<=ord("Z"); $lettre++){ 

            $mot = $debut.chr($lettre);
            $table; 
            if (strlen($mot)==1) $table = $nomBase;
            else {
                $table = "z_".$nomBase."_".$nomColonne."_".getTable($mot,strlen($mot)-1);
            }

            $sql = "SELECT $nomColonne,id FROM $table WHERE $nomColonne LIKE '%$mot%' LIMIT 1";
            $result = mysql_query($sql) or die($sql."<br>[[SKIP DIED '%$mot%']]<br>");
            $estVide = mysql_num_rows($result)==0;

            if (!$estVide){
                echo " $mot";
                flush();
                //$sql1 = "CREATE TABLE z_".$nomBase."_".$nomColonne."_".$mot." LIKE y_original_index"; //new thing .. copy table from model
                $sql1 = "CREATE TABLE z_".$nomBase."_".$nomColonne."_".$mot." (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                $nomColonne varchar(255) NOT NULL
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1";
                $sql2 = "INSERT INTO z_".$nomBase."_".$nomColonne."_".$mot." (SELECT id,$nomColonne FROM $table WHERE $nomColonne LIKE '%$mot%')";
                mysql_query($sql1);   
                mysql_query($sql2); 
                initStat($mot);       
            }                   

        }

    }     

    function clearTable($mot){
        global $nomBase,$nomColonne;  
        $sql = "TRUNCATE TABLE z_".$nomBase."_".$nomColonne."_".$mot;
        mysql_query($sql);
    }

    function deleteTable($mot){
        global $nomBase,$nomColonne;  
        $sql = "DROP TABLE z_".$nomBase."_".$nomColonne."_".$mot;
        mysql_query($sql);
    }

    /***
    * Remplissage initial de la table Stats
    * @param mixed $mot : le mot
    */
    function initStat($mot){
        global $nomBase,$nomColonne; 
        $ordre = strlen($mot);
        $sql = "SELECT COUNT(*) FROM z_".$nomBase."_".$nomColonne."_".$mot;
        $taille = mysql_result(mysql_query($sql),0);                         
        $sql = "INSERT INTO y_".$nomBase."_".$nomColonne."_stats SET ordre='$ordre', name='$mot', nombre='$taille'";
        mysql_query($sql);
    }

    /***
    * Remplissage partiel de la table Stats
    * avec la dur�e de la recherche d'un mot gr�ce � la m�thode 2
    * (recherche dans la sous-table la moins remplie)
    * @param mixed $mot : le mot � rechercher
    */
    function performance($mot){
        global $nomBase,$nomColonne;
        $debut = start_timer();
        recherche($mot,"milieu");
        $temps = end_timer($debut);       
        $sql = "UPDATE y_".$nomBase."_".$nomColonne."_stats SET temps='$temps' WHERE $nomColonne='$mot'";
        mysql_query($sql);
    }

    function clearStats(){
        global $nomBase,$nomColonne;
        $sql = "TRUNCATE TABLE y_".$nomBase."_".$nomColonne."_stats";
        mysql_query($sql);
    }

    function deleteStats(){
        global $nomBase,$nomColonne;
        $sql = "DROP TABLE y_".$nomBase."_".$nomColonne."_stats";
        mysql_query($sql);
    }

    function deleteLog(){
        global $nomBase,$nomColonne;
        $sql = "DROP TABLE y_".$nomBase."_log";
        mysql_query($sql);
    }

    /***
    * Effectue la recherche d'une cha�ne de caract�res  
    * @param mixed $text : la cha�ne � chercher
    * @param mixed $option : l'option de recherche
    */
    function recherche($text, $mode){

        global $nomBase,$nomColonne,$ordreMax;
        $temps = start_timer();
        $print="";

        if ($mode=="tout"){
            // mode expression r�guli�re
            $sql = "SELECT $nomColonne FROM ".$nomBase." WHERE $nomColonne RLIKE '$text' LIMIT 100";                                    
            $result = mysql_query($sql);
            while ($tab = mysql_fetch_array($result)){ 
                $print .= ($print==""?"":",").$tab[$nomColonne];      
            }
        }

        else {
            $temps1 = start_timer();
            $mot = explode(" ",$text);
            $tab = tailleMax($mot);
            $long = $tab['taille'];
            $nb = $tab['nombre'];

            if ($long>=1){

                $table = "";
                $truc = $long>$ordreMax?$ordreMax:$long;
                while ($table==""){
                    $table = getTable($text,$truc--);
                }
                echo "1) Choix de la table : ",end_timer($temps1),"<br>";  
                $temps2 = start_timer();

                $sql = "SELECT $nomColonne FROM z_".$nomBase."_".$nomColonne."_".$table." WHERE";
                $debut = $mode=="debut"?"":"%";
                $fin = $mode=="fin"?"":"%";
                $and = "";

                for ($i=0; $i<sizeof($mot); $i++){
                    if (strlen($mot[$i])>0){
                        $sql .= $and." $nomColonne LIKE '".$debut."$mot[$i]".$fin."'";
                        $and = " AND"; 
                        if ($i==0){
                            $debut = $fin = "%";
                        }
                    }
                }                                     

                $sql .= " LIMIT 250";       

                $result = mysql_query($sql);
                echo "2) Requ�te : ",end_timer($temps2),"<br><br>";
                $temps4 = start_timer();
                while ($tab = mysql_fetch_array($result)){ 
                    $print .= ($print==""?"":",").$tab[$nomColonne];      
                } 
                echo $sql,"<br>";               
            }
        }
        updateLog("Recherche ".$text,$temps=end_timer($temps));
        return array("resultats" => ($print==""?"Pas de r�sultats.":$print), "temps" => $temps);
    }  

    /**
    * Renvoie la longueur maximale et le nombre de mots
    * qui composent un tableau de mots
    * @param mixed $mot : le tableau
    */
    function tailleMax($mot){                             
        $max = max(array_map("strlen",$mot));
        return array("taille" => $max, "nombre" => count($mot));               
    }

    /**
    * Renvoie la table appropri�e dans laquelle chercher
    * une cha�ne de caract�res (la sous-table la moins remplie)
    * @param mixed $text : la cha�ne � chercher
    * @param mixed $long : l'ordre de la table
    */
    function getTable($text,$long){

        global $nomBase,$nomColonne;       
        $sql = "SELECT name,MIN(nombre) FROM y_".$nomBase."_".$nomColonne."_stats WHERE";   
        $liste = array();   
        $or="";
        for ($i=0; $i<strlen($text)-$long+1; $i++){
            $t = strtoupper(substr($text,$i,$long));
            // ignore les suites de caract�res contenant un espace
            if (strpos($t," ")===FALSE && array_search($t,$liste)===FALSE){
                array_unshift($liste,$t);
                $sql .=  $or." name = '".$t."'";
                $or = " OR";
            }
        }                                     
        //echo $sql,"<br>";   
        $result = mysql_query($sql);
        $tab = mysql_fetch_array($result);   
        return $tab['name'];
    } 

    function existe($text){

        global $nomBase,$nomColonne,$ordreMax;
        // test d'appartenance � la table d'origine (en fait � la sous-table appropri�e)
        $table = "";
        $truc = $ordreMax;
        while ($table=="" && $truc>0){
            $table = getTable($text,$truc--);
        }
        if ($table == "") return false;
        $sql = "SELECT $nomColonne FROM z_".$nomBase."_".$nomColonne."_".$table." WHERE $nomColonne='$text' LIMIT 1";
        $result = mysql_query($sql);      
        return (mysql_num_rows($result)!=0);
    } 

    function insertion($text){

        global $nomBase;
        $temps = start_timer();
        // mise � jour de la table principale
        $sql = "INSERT INTO ".$nomBase." SET name='$text'";
        echo "Insertion du mot ",$text," dans la table ".$nomBase."<br>"; 
        mysql_query($sql);
        $id = mysql_insert_id(); 
        // mise � jour des sous-tables et de la table Stats
        $liste = array();
        for ($long=2; $long<=3; $long++){   
            for ($i=0; $i<strlen($text)-$long+1; $i++){
                $t = strtoupper(substr($text,$i,$long));
                if (array_search($t,$liste)===FALSE){
                    array_unshift($liste,$t);  
                    $sql = "INSERT INTO z_".$nomBase."_".$nomColonne."_".$t." SET name='$text', id='$id'";
                    mysql_query($sql);
                    $sql = "UPDATE y_".$nomBase."_".$nomColonne."_stats SET nombre=nombre+1 WHERE name='$t'";
                    echo "Insertion du mot ",$text," dans la sous-table ".$t."<br>";
                    mysql_query($sql);  
                }           
            }
        }
        updateLog("Insertion ".$text,$temps=end_timer($temps)); 
    } 

    function suppression($text){

        global $nomBase;
        $temps = start_timer();
        // mise � jour de la table principale
        $sql = "DELETE FROM ".$nomBase." WHERE name='$text' LIMIT 1";
        echo $sql."<br>";
        $result = mysql_query($sql);          
        // mise � jour des sous-tables et de la table Stats
        $liste = array();
        for ($long=2; $long<=3; $long++){   
            for ($i=0; $i<strlen($text)-$long+1; $i++){
                $t = strtoupper(substr($text,$i,$long));
                if (array_search($t,$liste)===FALSE){
                    array_unshift($liste,$t);  
                    $sql = "DELETE FROM z_".$nomBase."_".$nomColonne."_".$t." WHERE name='$text' LIMIT 1";
                    echo $sql."<br>";
                    mysql_query($sql);
                    $sql = "UPDATE y_".$nomBase."_".$nomColonne."_stats SET nombre=nombre-1 WHERE name='$t'";
                    echo $sql."<br>";
                    mysql_query($sql);  
                }           
            }
        }
        updateLog("Suppression ".$text,$temps=end_timer($temps));
    }

    function creeIndex(){
        global $nomBase,$nomColonne; 
        $temps = start_timer(); 
        $sql = "CREATE TABLE IF NOT EXISTS y_".$nomBase."_".$nomColonne."_keyword (
        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,    
        `name` varchar(30) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        mysql_query($sql); 
        // FOREIGN KEYS
        $sql = "CREATE TABLE IF NOT EXISTS y_".$nomBase."_".$nomColonne."_index (
        `id` int(11) UNSIGNED NOT NULL,                                
        `keyword` int(11) UNSIGNED NOT NULL,
        PRIMARY KEY (`id`,`keyword`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        mysql_query($sql); 
        $sql = "TRUNCATE TABLE y_".$nomBase."_".$nomColonne."_index";
        mysql_query($sql);               

        $sql = "SELECT id,$nomColonne FROM $nomBase LIMIT 50";
        $result = mysql_query($sql);
        while ($tab = mysql_fetch_array($result)){
            $mot = explode(" ",$tab[$nomColonne]);
            for ($i=0; $i<sizeof($mot); $i++){
                $t = strtoupper($mot[$i]);
                if ($t!=""){
                    $sql = "SELECT id FROM y_".$nomBase."_".$nomColonne."_keyword WHERE name='$t' LIMIT 1";
                    $res = mysql_query($sql);
                    if (mysql_num_rows($res)>0){
                        $ligne = mysql_fetch_array($res);
                        $sql = "INSERT INTO y_".$nomBase."_".$nomColonne."_index SET id='$tab[id]', keyword='$ligne[id]'";
                        mysql_query($sql);
                    }
                    else {
                        $sql = "INSERT INTO y_".$nomBase."_".$nomColonne."_keyword SET name='$t'"; 
                        mysql_query($sql);
                        $id = mysql_insert_id();
                        $sql = "INSERT INTO y_".$nomBase."_".$nomColonne."_index SET id='$tab[id]', keyword='$id'";
                        mysql_query($sql);
                    }
                } 
            }
        } 
        updateLog("Index ".$nomColonne,end_timer($temps)); 
    }

    /***
    * Affiche quelques d�tails sur la table Stats
    */
    function getDetails(){
        global $nomBase,$nomColonne;  
        $tableStats = "y_".$nomBase."_".$nomColonne."_stats";

        $sql = "SELECT COUNT(*) FROM $tableStats";     
        $result = mysql_query($sql);
        $taille = mysql_result($result,0);
        $print .= "Nombre de sous-tables : ".$taille."<br>";
        $i=1;
        while ($i<10){
            $sql = "SELECT COUNT(*) FROM $tableStats WHERE ordre=$i";     
            $result = mysql_query($sql);
            $taille = mysql_result($result,0);
            if ($taille==0) break;
            $print .= "Nombre de sous-tables d'ordre ".$i." : ".$taille."<br>"; 
            $i++;
        }
        $max = $i;                                

        $sql = "SELECT AVG(temps) AS moyenne FROM $tableStats WHERE nombre>0";
        $result = mysql_query($sql);
        $tab = mysql_fetch_array($result);
        $print .= "Temps moyen : ".$tab['moyenne']." seconde(s).<br>";

        for ($i=1; $i<$max; $i++){
            $print .= "<br><b>Ordre ".$i."</b><br>";
            $sql = "SELECT nombre,name,temps FROM $tableStats WHERE ordre=$i ORDER BY nombre DESC LIMIT 25";
            $result = mysql_query($sql);
            $print .= "<table><tr><td>Nom</td><td>Nombre</td><td>Proportion</td></tr>";
            while ($tab = mysql_fetch_array($result)){
                if ($i==1){
                    $sql = "SELECT COUNT(*) FROM $nomBase";
                    $prop = mysql_result(mysql_query($sql),0);
                    $txt = round(100*$tab['nombre']/$prop)."%"; 
                }
                else {                
                    $sup = substr($tab['name'],0,strlen($tab['name'])-1);
                    $sql = "SELECT nombre FROM $tableStats WHERE name='$sup' LIMIT 1";
                    $prop = mysql_fetch_array(mysql_query($sql));
                    $txt = round(100*$tab['nombre']/$prop[0])."% - ";
                    $sup = substr($tab['name'],1,strlen($tab['name'])-1);
                    $sql = "SELECT nombre FROM $tableStats WHERE name='$sup' LIMIT 1";
                    $prop = mysql_fetch_array(mysql_query($sql));
                    $txt .= round(100*$tab['nombre']/$prop[0])."%"; 
                }
                $prop = mysql_fetch_array(mysql_query($sql));
                $print .= "<tr><td>".$tab['name']."</td><td>".$tab['nombre']."</td><td>".$txt."</td/></tr>"; 
            }
            $print .= "</table>";

            $sql = "SELECT AVG(nombre) AS moyenne FROM $tableStats WHERE ordre=$i AND nombre>0";
            $result = mysql_query($sql);
            $tab = mysql_fetch_array($result);
            $print .= "Moyenne : ".$tab['moyenne']."<br>";

            $sql = "SELECT STD(nombre) AS ecart FROM $tableStats WHERE ordre=$i AND nombre>0";
            $result = mysql_query($sql);
            $tab = mysql_fetch_array($result);
            $print .= "Ecart-type : ".$tab['ecart']."<br>";
        }

        /* $print .= "<br><b>Liste des 100 premiers �l�ments les plus nombreux</b>";
        $sql = "SELECT name,COUNT(*) AS compte FROM $nomBase GROUP BY name ORDER BY compte DESC LIMIT 100";
        $result = mysql_query($sql);
        while ($tab = mysql_fetch_array($result)){
        $print .= "<br>".$tab[name]." - ".$tab[compte]; 
        }  */
        return $print;
    } 

?>
