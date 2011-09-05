<?php

    function tableExiste($table){
        $sql = "SELECT COUNT(*) FROM $table";
        return mysql_query($sql);
    }

    function tableSize($table){
        $sql = "SELECT COUNT(*) FROM $table";
        return mysql_result(mysql_query($sql),0);
    }

    function colonneExiste($colonne){
        global $nomTable;
        $sql = "SELECT $colonne FROM $nomTable";
        return mysql_query($sql);
    }

    function creeLog(){          
        $temps = start_timer();    
        $sql = "CREATE TABLE log (
        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `action` varchar(255) NOT NULL,
        `details` varchar(50) NOT NULL,
        `hash` varchar(32) NOT NULL,
        `heure` datetime NOT NULL,
        `temps` float NOT NULL, 
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        $result = mysql_query($sql);
        if ($result) updateLog("Création du log","",end_timer($temps));
    }

    function updateLog($action,$details,$temps){
        global $nomMaitre;   
        $sql = "INSERT INTO $nomMaitre.log SET action='$action', details='$details', heure=NOW(), temps='$temps'";
        mysql_query($sql);     
    }

    function creeChamps(){
        $sql = "CREATE TABLE IF NOT EXISTS champs_recherche (
        `hash` char(32) NOT NULL,
        `nomBase` char(30) NOT NULL,
        `nomTable` char(50) NOT NULL,
        `nomColonne` char(20) NOT NULL,
        `visuel` enum('suggest','result') NOT NULL,
        `resume` tinyint(1) NOT NULL,
        `nomDiv` char(20) NOT NULL,
        `afficheDiv` tinyint(1) NOT NULL,
        `description` text NOT NULL,
        `mode_suggest` enum('debut','milieu','fin') NOT NULL,
        `mode_result` enum('debut','milieu','fin') NOT NULL,
        `methode_suggest` enum('direct','tables','mot','phrase') NOT NULL,
        `methode_result` enum('direct','tables','mot','phrase') NOT NULL,
        `limite_suggest` smallint(5) UNSIGNED NOT NULL,
        `limite_result` smallint(5) UNSIGNED NOT NULL,
        `containerAll` text NOT NULL,
        `containerResult` text NOT NULL,
        `containerDetails` text NOT NULL,
        `containerSuggest` text NOT NULL,
        PRIMARY KEY (`hash`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1";
        mysql_query($sql); 
    }

    function creeProjets(){
        $sql = "CREATE TABLE IF NOT EXISTS projets (
        `name` varchar(30) NOT NULL PRIMARY KEY
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1";
        mysql_query($sql);
        mysql_query("INSERT INTO projets SET name='global'"); 
    }

    function creeCorrec(){
        $sql = "CREATE TABLE IF NOT EXISTS corrections (
        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `action` enum('correct','split','merge','ignore','no_index','expression') NOT NULL,
        `type` enum('word','phrase') NOT NULL,
        `word` varchar(200) NOT NULL,
        `project` varchar(30) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1";
        mysql_query($sql); 
    }

    function updateProjets($project){
        global $nomMaitre;  
        mysql_query("INSERT INTO $nomMaitre.projets SET name='$project'") or die(mysql_error());  
    }

    function updateCorrec($action,$type,$word,$project){
        global $nomMaitre;  
        $word = addslashes($word);
        $sql = "INSERT INTO $nomMaitre.corrections SET action='$action', type='$type', word='$word', project='$project'";    
        mysql_query($sql) or die(mysql_error()); 
    }

    /**
    * Crée les sous-tables jusqu'à l'ordre max
    */
    function creeTables(){
        global $nomTable, $nomColonne, $ordreMax, $thres;
        $temps = start_timer();
        creeStats();
        startProgress(""); 
        creeSousTables("");    
        for ($i=2; $i<=$ordreMax; $i++){
            $sql = "SELECT name FROM y_".$nomTable."_".$nomColonne."_stats WHERE ordre='$i'-1 AND nombre>'$thres'";
            $result = mysql_query($sql) or die(mysql_error());
            if (mysql_num_rows($result)==0) break;
            startProgress("");
            while ($tab = mysql_fetch_array($result)){
                creeSousTables($tab['name']);
            } 
        }
        updateProgress("Création des tables terminée",100);
        updateLog("Création des sous-tables ",$nomTable.".".$nomColonne,end_timer($temps));           
    }

    function clearTables(){
        global $nomColonne;      
        operation("clearTable","Réinitialisation sous-tables"); 
    }

    function deleteTables(){  
        global $nomColonne;     
        operation("deleteTable","Suppression sous-tables"); 
    }

    function performances(){
        operation("performance","Performances");
    }

    function creeStats(){
        global $nomTable,$nomColonne,$ordreMax; 
        clearStats();
        $sql = "CREATE TABLE IF NOT EXISTS y_".$nomTable."_".$nomColonne."_stats (
        `name` varchar($ordreMax) NOT NULL,
        `ordre` tinyint(3) UNSIGNED NOT NULL,
        `nombre` int(11) UNSIGNED NOT NULL DEFAULT '0',
        `temps` float NOT NULL DEFAULT '0', 
        PRIMARY KEY (`name`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        mysql_query($sql); 
    }

    /***
    * Réalise une opération quelconque sur tous les mots de la table Stats
    * @param mixed $op : le nom de l'opération
    * @param mixed $comm : le texte à afficher dans la barre de progression
    * */
    function operation($op,$comm){ 
        global $nomTable,$nomColonne;
        $temps = start_timer();
        $sql = "SELECT name FROM y_".$nomTable."_".$nomColonne."_stats";
        $result = mysql_query($sql);
        if (!$result) return;
        $count_table=mysql_num_rows($result);  //Richard
        $cpt=0;
        $progress=0;
        startProgress($comm);
        while ($tab = mysql_fetch_array($result)){
            $op($tab['name']);
            echo $tab['name']," "; //Richard
            $cpt++;
            $pourcent = round(100*$cpt/$count_table);
            if ($pourcent > $progress){
                $progress = $pourcent;
                updateProgress($comm,$progress);
            }                               
        } 
        updateLog($comm,$nomTable.".".$nomColonne,end_timer($temps));
    }

    /***
    * Création et remplissage des sous-tables contenant
    * tous les éléments de la table d'origine dont le nom contient un certain mot 
    * @param mixed $debut : le mot
    */
    function creeSousTables($debut){
        global $nomTable,$nomColonne;

        $pourcent = 0;
        for ($i=0; $i<strlen($debut); $i++){
            $pourcent += (ord(substr($debut,$i,1))-ord("A"))*100/pow(26,$i+1);
        }
        $pourcent = round($pourcent);
        updateProgress("Création des tables $debut"."x",$pourcent);

        for ($lettre=ord("A"); $lettre<=ord("Z"); $lettre++){ 

            $mot = $debut.chr($lettre);
            $table; 
            if (strlen($mot)==1) $table = $nomTable;
            else {
                $table = "z_".$nomTable."_".$nomColonne."_".getSousTableLong($nomTable,$nomColonne,$mot,strlen($mot)-1);
            }

            $id = getPrimaryKey($table);
            $sql = "SELECT $nomColonne,$id FROM $table WHERE $nomColonne LIKE '%$mot%' LIMIT 1";
            $result = mysql_query($sql) or die(mysql_error());
            $estVide = mysql_num_rows($result)==0;

            if (!$estVide){
                echo " $mot";
                flush();
                //$sql1 = "CREATE TABLE z_".$nomTable."_".$nomColonne."_".$mot." LIKE y_original_index"; //new thing .. copy table from model
                $sql1 = "CREATE TABLE z_".$nomTable."_".$nomColonne."_".$mot." (
                `id` int(11) UNSIGNED NOT NULL PRIMARY KEY,
                $nomColonne varchar(255) NOT NULL
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1";
                $sql2 = "INSERT INTO z_".$nomTable."_".$nomColonne."_".$mot." (SELECT $id,$nomColonne FROM $table WHERE $nomColonne LIKE '%$mot%')";
                mysql_query($sql1);   
                mysql_query($sql2); 
                initStat($mot);       
            }                   

        }

    }     

    function clearTable($mot){
        global $nomTable,$nomColonne;  
        $sql = "TRUNCATE TABLE z_".$nomTable."_".$nomColonne."_".$mot;
        mysql_query($sql);
    }

    function deleteTable($mot){
        global $nomTable,$nomColonne;  
        $sql = "DROP TABLE z_".$nomTable."_".$nomColonne."_".$mot;
        mysql_query($sql);
    }

    /***
    * Remplissage initial de la table Stats
    * @param mixed $mot : le mot
    */
    function initStat($mot){
        global $nomTable,$nomColonne; 
        $ordre = strlen($mot);
        $taille = tableSize("z_".$nomTable."_".$nomColonne."_".$mot);                         
        $sql = "INSERT INTO y_".$nomTable."_".$nomColonne."_stats SET ordre='$ordre', name='$mot', nombre='$taille'";
        mysql_query($sql);
    }

    /***
    * Remplissage partiel de la table Stats
    * avec la durée de la recherche d'un mot grâce à la méthode 2
    * (recherche dans la sous-table la moins remplie)
    * @param mixed $mot : le mot à rechercher
    */
    function performance($mot){
        global $nomTable,$nomColonne;
        $debut = start_timer();
        recherche($mot,"",$nomTable,$nomColonne,"milieu","tout","result");
        $temps = end_timer($debut);       
        $sql = "UPDATE y_".$nomTable."_".$nomColonne."_stats SET temps='$temps' WHERE $nomColonne='$mot'";
        mysql_query($sql);
    }

    function clearStats(){
        global $nomTable,$nomColonne;
        $sql = "TRUNCATE TABLE y_".$nomTable."_".$nomColonne."_stats";
        mysql_query($sql);
    }

    function deleteStats(){
        global $nomTable,$nomColonne;
        $temps = start_timer();
        $sql = "DROP TABLE y_".$nomTable."_".$nomColonne."_stats";
        mysql_query($sql);
        updateLog("Suppression des stats",$nomTable.".".$nomColonne,end_timer($temps));
    }

    function deleteIndex(){
        global $nomTable,$nomColonne;
        $temps = start_timer();
        $sql = "DROP TABLE y_".$nomTable."_".$nomColonne."_indexword";
        mysql_query($sql);
        $sql = "DROP TABLE y_".$nomTable."_".$nomColonne."_indexphrase";
        mysql_query($sql);
        $sql = "DROP TABLE y_".$nomTable."_".$nomColonne."_keyword";
        mysql_query($sql);
        $sql = "DROP TABLE y_".$nomTable."_".$nomColonne."_keyphrase";
        mysql_query($sql);
        updateLog("Suppression des index",$nomTable.".".$nomColonne,end_timer($temps));
    }

    function videLog(){    // clearLog déjà pris
        global $nomMaitre;      
        $temps = start_timer(); 
        mysql_query("TRUNCATE TABLE $nomMaitre.log");
        updateLog("Réinitialisation du log","",end_timer($temps)); 
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
    * Renvoie la sous-table appropriée dans laquelle chercher
    * une chaîne de caractères (la moins remplie)
    * @param mixed $table : la table principale ou d'index  
    * @param mixed $colonne : la colonne  
    * @param mixed $text : la chaîne à chercher
    * @param mixed $long : l'ordre de la table
    */
    function getSousTableLong($table,$colonne,$text,$long){

        $sql = "SELECT name,MIN(nombre) FROM y_".$table."_".$colonne."_stats WHERE";   
        $liste = array();   
        $or="";
        for ($i=0; $i<strlen($text)-$long+1; $i++){
            $t = strtoupper(substr($text,$i,$long));
            // ignore les suites de caractères contenant un espace
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

    function getPrimaryKey($table){
        $result = mysql_query("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'"); 
        $tab = mysql_fetch_array($result);
        return $tab['Column_name'];
    }

    /**
    * Teste l'existence d'un mot
    * @param mixed $text : le mot à chercher
    */
    function existe($text){

        global $nomTable,$nomColonne;

        $ordreMax = getOrdreMax($nomTable,$nomColonne);
        // test d'appartenance à la table d'origine (en fait à la sous-table appropriée)
        $table = "";
        $truc = $ordreMax;
        while ($table=="" && $truc>0){
            $table = getSousTableLong($nomTable,$text,$truc--);
        }
        if ($table == "") return false;
        $sql = "SELECT $nomColonne FROM z_".$nomTable."_".$nomColonne."_".$table." WHERE $nomColonne='$text' LIMIT 1";
        $result = mysql_query($sql);      
        return (mysql_num_rows($result)!=0);
    } 

    function insertion($text){

        global $nomTable;
        $temps = start_timer();
        // mise à jour de la table principale
        $sql = "INSERT INTO ".$nomTable." SET name='$text'";
        echo "Insertion du mot ",$text," dans la table ".$nomTable."<br>"; 
        mysql_query($sql);
        $last_id = mysql_insert_id(); 
        // mise à jour des sous-tables et de la table Stats
        $liste = array();
        for ($long=2; $long<=3; $long++){   
            for ($i=0; $i<strlen($text)-$long+1; $i++){
                $t = strtoupper(substr($text,$i,$long));
                if (array_search($t,$liste)===FALSE){
                    array_unshift($liste,$t);  
                    $sql = "INSERT INTO z_".$nomTable."_".$nomColonne."_".$t." SET name='$text', id='$last_id'";
                    mysql_query($sql);
                    $sql = "UPDATE y_".$nomTable."_".$nomColonne."_stats SET nombre=nombre+1 WHERE name='$t'";
                    echo "Insertion du mot ",$text," dans la sous-table ".$t."<br>";
                    mysql_query($sql);  
                }           
            }
        }
        updateLog("Insertion de ".$text,$nomTable,$temps=end_timer($temps)); 
    } 

    function suppression($text){

        global $nomTable;
        $temps = start_timer();
        // mise à jour de la table principale
        $sql = "DELETE FROM ".$nomTable." WHERE name='$text' LIMIT 1";
        echo $sql."<br>";
        $result = mysql_query($sql);          
        // mise à jour des sous-tables et de la table Stats
        $liste = array();
        for ($long=2; $long<=3; $long++){   
            for ($i=0; $i<strlen($text)-$long+1; $i++){
                $t = strtoupper(substr($text,$i,$long));
                if (array_search($t,$liste)===FALSE){
                    array_unshift($liste,$t);  
                    $sql = "DELETE FROM z_".$nomTable."_".$nomColonne."_".$t." WHERE name='$text' LIMIT 1";
                    echo $sql."<br>";
                    mysql_query($sql);
                    $sql = "UPDATE y_".$nomTable."_".$nomColonne."_stats SET nombre=nombre-1 WHERE name='$t'";
                    echo $sql."<br>";
                    mysql_query($sql);  
                }           
            }
        }
        updateLog("Suppression de ".$text,$nomTable,$temps=end_timer($temps));
    }

    /**
    * Crée ou recrée le(s) index en appliquant les corrections définies dans la table corrections
    * @param mixed $projet : un tableau dont les clés sont les projets à appliquer
    * @param mixed $motParMot : mode d'indexation (1: par mot, 2: par phrase, 3: les deux)
    */
    function creeIndex($projet,$motParMot){
        global $nomMaitre,$nomTable,$nomColonne;

        $temps = start_timer();

        $cpt = 0; $progress = 0;
        startProgress("Création de l\'index ".$nomColonne);

        if ($motParMot!=1) creeTablesIndex('phrase');
        if ($motParMot!=2) creeTablesIndex('word');            

        $correction = array();
        if ($projet!=null){
            $result = mysql_query("SELECT * FROM $nomMaitre.corrections");
            while ($tab = mysql_fetch_array($result)){
                if (isset($projet['global']) || isset($projet[$tab['project']])){
                    $correction[] = $tab;
                }
            }
        }

        $id = getPrimaryKey($nomTable);
        $sql = "SELECT $id,$nomColonne FROM $nomTable";
        $result = mysql_query($sql) or die($sql);
        $total = mysql_num_rows($result);

        while ($tab = mysql_fetch_array($result)){

            $txt = $tab[$nomColonne];
            if (mb_detect_encoding($txt,"UTF-8",true)) $txt = utf8_decode($txt); 
            $txt = strtoupper(trim($txt));

            // Application des corrections (correct_mot + correct_phrase + split)
            foreach ($correction as $corr){
                if ($corr['action']=='correct'){
                    $t = explode(" => ",$corr['word']);
                    $txt = str_replace($t[0],$t[1],$txt);
                }
                else if ($corr['action']=='split'){
                        $t = explode(" | ",$corr['word']);
                        $txt = str_replace($t[0].$t[1],$t[0]." ".$t[1],$txt);
                    }
            }

            if ($motParMot!=1){
                $txtPhrase = preg_replace("/\([^\)]+\)/","",$txt);
                $ignore = 0;
                foreach ($correction as $corr){
                    if ($corr['action']=='ignore' && sansAccents($corr['word'])==sansAccents($txtPhrase)){
                        $ignore = 1;
                    }
                }
                updateIndex("phrase",$txtPhrase,$tab[$id],$ignore);
            }

            if ($motParMot!=2){
                $txtMot = preg_replace("/[^A-ZÀÂÇÈÉÊËÎÏÔÙÛÜ'-]+/"," ",$txt);
                $listeMot = explode(" ",$txtMot);
                $tailleMot = sizeof($listeMot);
                $compteMot = array_fill(0,$tailleMot,true);
                $listeOK = array();
                
                // Recherche d'expressions
                foreach ($correction as $corr){
                    if ($corr['action']=='expression'){
                        $expr = explode(" ",sansAccents($corr['word']));
                        $tailleExpr = sizeof($expr);
                        for ($i=0; $i<$tailleMot-$tailleExpr; $i++){
                            $match = true;
                            for ($j=0; $j<$tailleExpr; $j++){
                                if ($expr[$j]!=sansAccents($listeMot[$i+$j])){
                                    $match = false;
                                    break;
                                }
                            }
                            if ($match){
                                for ($j=0; $j<$tailleExpr; $j++){
                                    $compteMot[$i+$j] = false;
                                }
                                $listeOK[] = $corr['word'];
                            }
                        }
                    }
                }

                for ($i=0; $i<$tailleMot; $i++){
                    if ($compteMot[$i]) $listeOK[] = $listeMot[$i];
                }
                
                foreach ($listeOK as $mot){
                    $ignore = 0; $no_index = false;
                    foreach ($correction as $corr){
                        if ($corr['action']=='no_index' && sansAccents($corr['word'])==sansAccents($mot)){
                            $no_index = true;
                            break;
                        }
                        else if ($corr['action']=='ignore' && sansAccents($corr['word'])==sansAccents($mot)){
                                $ignore = 1;
                            }
                    }
                    if (!$no_index) updateIndex("word",$mot,$tab[$id],$ignore);
                }

                $pourcent = round(100*(++$cpt)/$total);                      
                if ($pourcent > $progress){
                    $progress = $pourcent;                         
                    updateProgress("Création de l\'index ".$nomColonne,$progress);
                }

            }
        }
        $s = $motParMot==1? "mot": ($motParMot==2? "phrase": "mot+phrase");
        updateLog("Index ($s)",$nomTable.".".$nomColonne,end_timer($temps));
    }

    function creeTablesIndex($type){
        global $nomTable,$nomColonne; 

        $taille = $type=='word'? 50:200;
        $sql = "CREATE TABLE IF NOT EXISTS y_".$nomTable."_".$nomColonne."_key".$type." (
        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,    
        $nomColonne varchar($taille) NOT NULL,
        `nombre` int(11) UNSIGNED NOT NULL,
        `ignored` tinyint(1) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        mysql_query($sql);
        mysql_query("TRUNCATE TABLE y_".$nomTable."_".$nomColonne."_key".$type);

        $sql = "CREATE TABLE IF NOT EXISTS y_".$nomTable."_".$nomColonne."_index".$type." (
        `id` int(11) UNSIGNED NOT NULL,                                
        `keyword` int(11) UNSIGNED NOT NULL,
        PRIMARY KEY (`id`,`keyword`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        mysql_query($sql); 
        mysql_query("TRUNCATE TABLE y_".$nomTable."_".$nomColonne."_index".$type);   
    }

    function updateIndex($type,$mot,$idTable,$ignore){
        global $nomTable,$nomColonne; 

        $mot = addslashes($mot);                           
        if (strlen($mot)>1){ 
            $sql = "SELECT id FROM y_".$nomTable."_".$nomColonne."_key".$type." WHERE $nomColonne='$mot' LIMIT 1";
            $res = mysql_query($sql);                   
            if (mysql_num_rows($res)>0){
                // Le mot-clé est déjà présent dans la table     
                $ligne = mysql_fetch_array($res);
                $sql = "UPDATE y_".$nomTable."_".$nomColonne."_key".$type." SET nombre=nombre+1 WHERE id='$ligne[id]'";
                mysql_query($sql) or die($sql);
                $sql = "INSERT INTO y_".$nomTable."_".$nomColonne."_index".$type." SET id='$idTable', keyword='$ligne[id]'";
                mysql_query($sql);
            }
            else {
                // Le mot-clé est absent de la table   
                $sql = "INSERT INTO y_".$nomTable."_".$nomColonne."_key".$type." SET $nomColonne='$mot', nombre='1', ignored='$ignore'"; 
                mysql_query($sql) or die($sql);
                $last_id = mysql_insert_id();
                $sql = "INSERT INTO y_".$nomTable."_".$nomColonne."_index".$type." SET id='$idTable', keyword='$last_id'";
                mysql_query($sql) or die(mysql_error()."<br>$sql");
            }
        } 
    }

    /***
    * Affiche quelques détails sur la table Stats
    */
    function getDetails(){
        global $nomTable,$nomColonne;  
        $tableStats = "y_".$nomTable."_".$nomColonne."_stats";

        $taille = tableSize($tableStats);
        $print = "Nombre de sous-tables : ".$taille."<br>";
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
                    $prop = tableSize($nomTable);
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

        /* $print .= "<br><b>Liste des 100 premiers éléments les plus nombreux</b>";
        $sql = "SELECT name,COUNT(*) AS compte FROM $nomTable GROUP BY name ORDER BY compte DESC LIMIT 100";
        $result = mysql_query($sql);
        while ($tab = mysql_fetch_array($result)){
        $print .= "<br>".$tab[name]." - ".$tab[compte]; 
        }  */
        return $print;
    } 


    function getOrdreMax($table,$colonne){
        $result = mysql_query("SELECT MAX(ordre) FROM y_".$table."_".$colonne."_stats");
        if (!$result) return "error";
        return mysql_result($result,0);
    }


    function list_db(){

        global $DbUser,$DbPassword,$DbHost,$nomBase;

        $link = mysql_connect($DbHost, $DbUser, $DbPassword);
        $db_list = mysql_list_dbs($link);
        $out_print = "";

        while ($row = mysql_fetch_object($db_list)) {
            if ($row->Database==$nomBase){
                $out_print .= "<b>".$row->Database."</b><br>";  
            }
            else $out_print.= "<a href=\"?nomBase=".$row->Database."\">".$row->Database . "</a><br>";
        } 
        return $out_print;

    } 


    function list_tables(){
        global $nomBase,$nomTable,$print_details;

        $sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = '$nomBase'
        AND (table_name NOT LIKE 'y\_%' OR table_name LIKE '%word' OR table_name LIKE '%phrase') AND table_name NOT LIKE 'z\_%'";
        $result = mysql_query($sql);
        $print = "";

        while ($ligne=mysql_fetch_array($result)){
            if ($ligne[0]==$nomTable){
                $print .= "<b>".$ligne[0]."</b><br>";  
            }
            else $print .= "<a href='?nomTable=".$ligne[0]."'>".$ligne[0]."</a><br>";
        }                                                            
        return $print.$print_details; 
    }


    function list_colonnes(){
        global $nomBase,$nomTable,$nomColonne;

        mysql_select_db($nomBase);
        $sql = "SHOW COLUMNS FROM ".$nomTable;
        $result = mysql_query($sql) or die(mysql_error()."<br>".$sql);                                                                                      
        $print = "";

        while ($ligne=mysql_fetch_array($result)){
            if ($ligne['Field']==$nomColonne){
                $print .= "<b>".$ligne['Field']."</b><br>";  
            }
            else $print .= "<a href='?nomColonne=".$ligne['Field']."'>".$ligne['Field']."</a><br>";
        }                                                                                                     
        return $print;
    }


    function info_colonnes(){
        global $nomTable,$nomColonne;

        $sql = "SHOW COLUMNS FROM ".$nomTable;
        $result = mysql_query($sql) or die(mysql_error()."<br>".$sql);
        $print = "<form action='?stage=index' method='post'><table class='tableStyle'><tr><td>Nom</td><td>Type</td><td>Nombre</td><td>Tables</td><td>Mot</td><td>Phrase</td>";

        while ($ligne=mysql_fetch_array($result)){
            $nom = $ligne['Field'];

            $array = etatTables($nom);

            $tables = $array['tables']? "disabled" : "";
            $mot = $array['mot'] || strpos($nomTable,"y_")===0? "disabled" : "";   
            $phrase = $array['phrase'] || strpos($nomTable,"y_")===0? "disabled" : "";

            $print .= "<tr><td>";
            if ($nom==$nomColonne){
                $print .= "<b>".$nom."</b>";  
            }
            else $print .= "<a href='?nomColonne=".$nom."'>".$nom."</a>";
            $print .= "</td><td>".$ligne['Type']."</td>";
            $sql = "SELECT COUNT(DISTINCT $nom) FROM ".$nomTable;
            $nb = "X";//mysql_result(mysql_query($sql),0);
            $print .= "<td>".$nb."</td><td align='center'><input type='checkbox' ".$tables." id='t_".$nom."' name='t_".$nom."'></td>
            <td align='center'><input type='checkbox' ".$mot." id='i_".$nom."' name='i_".$nom."'></td>
            <td align='center'><input type='checkbox' ".$phrase." id='j_".$nom."' name='j_".$nom."'></td></tr>";    
        }                                                                                                
        $print .= "</table><p align='center'><input type='submit' value='apply'></p></form>";
        return $print;
    }


    function list_log(){
        global $nomMaitre;          

        $sql = "SELECT action,details,temps,heure FROM $nomMaitre.log ORDER BY id";
        $result = mysql_query($sql) or die(mysql_error());
        $print = "";

        while ($ligne=mysql_fetch_array($result)){ 
            $print = "<tr><td>".$ligne['heure']."</td><td>".$ligne['action']."</td><td>".$ligne['details']."</td><td>".$ligne['temps']."</td></tr>".$print;  
        }
        $print = "<table class='tableStyle'><tr><td><b>Heure</b></td><td><b>Action</b></td><td><b>Détails</b></td><td><b>Durée</b></td></tr>".$print."</table>";
        return $print;  
    }

    function list_correc(){
        global $nomMaitre,$nomProjet,$correc_id;

        $where = $nomProjet=="global"? "ORDER BY project": "WHERE project='$nomProjet'";
        $sql = "SELECT * FROM $nomMaitre.corrections ".$where;
        $result = mysql_query($sql) or die(mysql_error()."<br>".$sql);
        $print = "";

        while ($ligne=mysql_fetch_array($result)){
            $print .= "<tr><td>".$ligne['project']."</td><td>".$ligne['action']."</td><td>".$ligne['type']."</td><td>";
            if (isset($correc_id) && $correc_id==$ligne['id']){
                $print .= "<b>".$ligne['word']."</b>";  
            }
            else $print .= "<a href='?stage=load_correc&correc_id=$ligne[id]'>".$ligne['word']."</a>";
            $print .= "</td></tr>";  
        }
        $print = "<table class='tableStyle'><tr align='center'><td><b>Projet</b></td><td><b>Action</b></td><td><b>Type</b></td><td><b>Mot</b></td></tr>".$print."</table>";
        return $print;  
    } 

    function list_config(){
        global $nomMaitre,$nomBase,$nomTable,$nomColonne,$hash;

        $sql = "SELECT hash,description,visuel FROM $nomMaitre.champs_recherche WHERE nomBase='$nomBase' AND nomTable='$nomTable' AND nomColonne='$nomColonne'";
        $result = mysql_query($sql) or die(mysql_error());
        $print = "";

        while ($ligne=mysql_fetch_array($result)){ 
            $desc = $ligne['description'];
            if ($desc=="") $desc = "Pas de description.";
            if ($hash==$ligne['hash']) $desc = "<b>$desc</b>";
            else $desc = "<a href='?hash=$ligne[hash]&stage=load_param'>".$desc."</a>";
            $print = "<tr><td>$desc</td><td>0</td><td>0</td><td>".$ligne['visuel']."</td></tr>".$print;  
        }
        $print = "<table class='tableStyle'><tr><td align='center' width='150'><b>Description</b></td><td colspan='3' align='center'><b>Paramètres</b></td></tr>".$print."</table>";
        return $print; 
    }

    function list_projets(){
        global $nomMaitre,$nomProjet;          

        $result = mysql_query("SELECT name FROM $nomMaitre.projets") or die(mysql_error());
        $print = "";
        while ($ligne=mysql_fetch_array($result)){
            $print .= "<br><input type='checkbox' name='$ligne[0]'".($ligne[0]=='global'?" checked disabled":"").">";
            if ($ligne[0]==$nomProjet){
                $print .= "<b>".$ligne[0]."</b>";  
            }
            else $print .= "<a href='?nomProjet=".$ligne[0]."'>".$ligne[0]."</a>";
        }   
        return $print;  
    }


    function etatTables($nom){
        global $nomTable;
        $sql = "SHOW TABLES LIKE 'z\_".$nomTable."\_".$nom."\_%'";
        $tables = mysql_num_rows(mysql_query($sql))>0;    
        $sql = "SHOW TABLES LIKE 'y\_".$nomTable."\_".$nom."\_keyword'";
        $mot = mysql_num_rows(mysql_query($sql))>0;
        $sql = "SHOW TABLES LIKE 'y\_".$nomTable."\_".$nom."\_keyphrase'";
        $phrase = mysql_num_rows(mysql_query($sql))>0;
        return array("tables"=>$tables, "mot"=>$mot, "phrase"=>$phrase);
    }


    function analyse(){
        global $nomBase,$nomTable,$nomColonne;

        $sql = "SELECT * FROM y_".$nomTable."_".$nomColonne."_stats PROCEDURE ANALYSE(3,24)";
        $result = mysql_query($sql);
        $print = "<table><tr><td>Field name</td><td>Min value</td><td>Max value</td><td>Min length</td><td>Max length</td><td>Empties or zeros</td><td>Nulls</td><td>Optimal field type</td></tr>";
        while ($ligne=mysql_fetch_array($result)){
            $print .= "<tr><td>".$ligne[0]."</td><td>".$ligne[1]."</td><td>".$ligne[2]."</td><td>".$ligne[3]."</td><td>".$ligne[4]."</td><td>".$ligne[5]."</td><td>".$ligne[6]."</td><td>".$ligne[9]."</td></tr>";  
        }
        return $print."</table>";
    }


    function stats($limite){                     
        global $nomBase,$nomTable,$nomColonne;                                                                

        $print = "<table><tr>";
        for ($i=ord("A"); $i<=ord("Z"); $i++){ 
            $lettre = chr($i);                                               
            $print .= "<td valign='top'><b>".$lettre."</b>";

            $table = "y_".$nomTable."_".$nomColonne."_keyword";                                                               
            $sql = "SELECT $nomColonne,id,nombre FROM $table WHERE $nomColonne LIKE '$lettre%' ORDER BY nombre DESC LIMIT $limite";   
            $result = mysql_query($sql) or die($sql."<br>".mysql_error());  

            while ($tab = mysql_fetch_array($result)){
                $word = $tab[$nomColonne];
                $print .= "<p><a href=\"javascript:getStats('stats_keywords','word','".encode($word)."','$tab[id]','$nomBase','$nomTable','$nomColonne');\" title='$tab[nombre]'>$word</a></p>";
            }

            $print .= "</td>";
        }

        $print .= "</tr></table>";
        return $print;
    }

    /**
    * Fonction qui affiche des propositions d'expressions (suites de mots) à insérer dans l'index
    * @param mixed $limite : limite du nombre de mots à étendre (0 si tous)
    * @param mixed $seuil : les 3 seuils utilisés dans affiche_expr
    */
    function expressions($limite,$seuil){                     
        global $nomBase,$nomTable,$nomColonne,$listeExpr;

        // tableau qui contient les expressions avec leur occurrence
        $listeExpr=array();   

        $table = "y_".$nomTable."_".$nomColonne."_keyword"; 
        $sql = "SELECT $nomColonne,nombre FROM $table ORDER BY nombre DESC".($limite>0?" LIMIT $limite":""); 
        $result = mysql_query($sql) or die($sql."<br>".mysql_error());  
        $print = "";
        $cpt = 0;
        startProgress("Expressions");

        while ($tab = mysql_fetch_array($result)){ 
            $tout = find_expression($tab[$nomColonne],$tab['nombre'],$tab['nombre'],1,$seuil);
            $print .= "<tr><td><b>$tab[$nomColonne]</b> <font class='chiffres'>($tab[nombre])</font></td>".$tout['affiche']."</tr>"; 
            updateProgress("Expressions",round(++$cpt*100/$limite)); 
        }

        // tableau qui contient les expressions avec leur taille
        $listeExpr2 = array();
        foreach ($listeExpr as $key=>$val){
            $listeExpr2[$key] = sizeof(explode(" ",$key));
        }  
        arsort($listeExpr);  
        arsort($listeExpr2);

        $printListe = "";

        // on parcourt les expressions (primaires) par taille décroissante
        foreach ($listeExpr2 as $key=>&$val){
            $name = encode($key);
            $printListe .= "<input type='checkbox' name='$name'>
            <a href=\"javascript:getStats('stats_expr','','$key','0','$nomBase','$nomTable','$nomColonne');\">$key</a>
            <font class='chiffres'> ($listeExpr[$key])</font>";
            $sousliste = "";
            foreach ($listeExpr as $k=>$v){   
                // si une expression (secondaire) est contenue dans l'expression (primaire) courante, on l'ajoute dans une sous-liste...
                if ($k!=$key && preg_match("/\b$k\b/",$key)){
                    $name = encode($k);
                    $sousliste .= str_repeat("&nbsp;",5)."<input type='checkbox' name='$name'>
                    <a href=\"javascript:getStats('stats_expr','','$k','0','$nomBase','$nomTable','$nomColonne');\">$k</a> 
                    <font class='chiffres'> ($v)</font><br>";
                    // ... et on la supprime des expressions primaires             
                    unset($listeExpr2[$k]);                             
                }
            }
            $divid = "deroule_".encode($key);
            if ($sousliste!="") $printListe .= "<input type='button' value='+' onclick='openclose(\"$divid\")'><br><div id='$divid' style='display:none;'>$sousliste</div>";
            else $printListe .= "<br>";
        }

        //return "$printListe<br><table border='1'>$print</table>";
        return $printListe;
    }

    /**
    * Fonction qui étend une expression vers avant ou après (<--expr-->)
    * @param mixed $expr : l'expression à étendre
    * @param mixed $freq : le nombre d'occurrences de l'expression
    * @param mixed $freqOrig : le nombre d'occurrences du mot d'origine
    * @param mixed $nbMots : la taille de l'expression
    * @param mixed $seuil : les 3 seuils utilisés dans affiche_expr + 4e seuil
    */
    function find_expression($expr,$freq,$freqOrig,$nbMots,$seuil){
        global $nomTable,$nomColonne;   

        $avant = array();
        $apres = array();                                            
        $tablePhrase = "y_".$nomTable."_".$nomColonne."_keyphrase";

        $table = getSousTable($tablePhrase,$nomColonne,$expr);
        if ($table==""){
            $table = $tablePhrase; echo "Sous-table introuvable : $expr<br>";
        }
        else $table = "z_".$tablePhrase."_".$nomColonne."_".$table; 

        //$sql;
        $expr = addslashes($expr);
        if ($table!=$tablePhrase) $sql = "SELECT $table.$nomColonne,nombre FROM $table,$tablePhrase WHERE $table.id=$tablePhrase.id AND $table.$nomColonne RLIKE '(^| )$expr(\$| )'";
        else $sql = "SELECT $nomColonne,nombre FROM $table WHERE $nomColonne RLIKE '(^| )$expr(\$| )'";                          
        $result = mysql_query($sql) or die($sql."<br>".mysql_error());                                            

        while ($tab = mysql_fetch_array($result)){ 
            $mot = explode(" ",$tab[$nomColonne]);
            for ($i=0; $i<sizeof($mot)-$nbMots+1; $i++){
                $sequence = array_slice($mot,$i,$nbMots);
                if (implode(" ",$sequence)==$expr){
                    $key = $i>0? $mot[$i-1]: "~";                                                     
                    if (array_key_exists($key,$avant)) $avant[$key]+=$tab['nombre'];
                    else $avant[$key]=$tab['nombre'];
                    $key = $i<sizeof($mot)-$nbMots? $mot[$i+$nbMots]: "~";          
                    if (array_key_exists($key,$apres)) $apres[$key]+=$tab['nombre'];
                    else $apres[$key]=$tab['nombre']; 
                }
            } 
        }

        arsort($avant);
        arsort($apres);

        $print1 = affiche_expr($avant,"~KEY~ ".$expr,$freqOrig,$nbMots,$seuil);
        $print2 = affiche_expr($apres,$expr." ~KEY~",$freqOrig,$nbMots,$seuil);

        // Seuil de 95% par rapport au niveau inférieur au-dessus duquel on n'affiche pas l'expression
        $freq1 = round(100*reset($avant)/$freq);
        $freq2 = round(100*reset($apres)/$freq);
        return array("affiche"=>$print1.$print2, "entier"=> (($freq1>=$seuil[3] && key($avant)!="~") || ($freq2>=$seuil[3] && key($apres)!="~")));
        //(sizeof($avant)==1&&!array_key_exists("~",$avant) || sizeof($apres)==1&&!array_key_exists("~",$apres)));
    }

    function affiche_expr($liste,$truc,$freqOrig,$nbMots,$seuil){
        global $listeExpr;

        $print = "<td>";
        foreach ($liste as $key=>$val){
            $pourcent = round(100*$val/$freqOrig);
            // Seuil de 5% au-dessous duquel on n'affiche plus l'expression
            if ($pourcent>$seuil[0]){
                $blabla = str_replace("~KEY~",$key,$truc);
                $txt = "$blabla<font class='chiffres'> (".$pourcent."%)</font><br>";
                if ($key!="~"){
                    // Seuil de 20% au-dessous duquel on ne cherche plus à étendre l'expression 
                    if ($pourcent>$seuil[2]){
                        $res = find_expression($blabla,$val,$freqOrig,$nbMots+1,$seuil);
                        if (!$res['entier']){
                            $txt = ($res['entier']?"":"<b>$txt</b>")."<table border='1'><tr>".$res['affiche']."</tr></table>"; 
                            $listeExpr[$blabla]=$val;
                        }              
                    }
                    // Seuil de 15% au-dessous duquel on ne propose plus l'expression 
                    else if ($pourcent>$seuil[1]){
                            $listeExpr[$blabla]=$val;
                        }
                }
                $print .= $txt;
            }
            else break;
        }
        return $print."</td>";
    }       


    function corrections($meth,$limite,$tauxMin){

        global $nomBase,$nomTable,$nomColonne;
        $print = "";
        $tableMot = "y_".$nomTable."_".$nomColonne."_key".$meth;

        $dico = array();
        $result = mysql_query("SELECT $nomColonne FROM $tableMot");
        while ($row = mysql_fetch_row($result)){
            $dico[sansAccents("$row[0]")]=1; 
        }

        $sql = "SELECT $nomColonne FROM $tableMot ORDER BY nombre DESC LIMIT $limite";
        $result1 = mysql_query($sql);
        $progress = 0;
        startProgress("Corrections");

        while ($tab = mysql_fetch_array($result1)){
            $mot1 = $tab[$nomColonne];
            $sql = "SELECT $nomColonne,id,nombre FROM $tableMot WHERE ignored=0";
            $result2 = mysql_query($sql);       
            $sousliste = array();

            while ($tab2 = mysql_fetch_array($result2)){
                $mot2 = $tab2[$nomColonne];
                if ($mot1!=$mot2){
                    if ($mot1!=$mot2."S" && $mot2!=$mot1."S"){
                        $dist = levenshtein($mot1,$mot2);
                        $taux = 1-$dist/max(strlen($mot1),strlen($mot2));
                        if ($taux>$tauxMin){
                            $name = encode("correct**$mot2##$mot1**$mot2");         
                            $sousliste[$name."|".$mot2."|".$tab2['id']."|".$tab2['nombre']."|blue"] = $taux;     
                        } 
                        $pos = strpos($mot2,$mot1);            
                        if ($meth=='word' && $pos===0 || $pos===strlen($mot2)-strlen($mot1)){   
                            if ($pos===0){
                                $mot3 = substr($mot2,strlen($mot1));
                                $split = $mot1."##".$mot3;
                            }
                            else {
                                $mot3 = substr($mot2,0,$pos);
                                $split = $mot3."##".$mot1;
                            }
                            if (isset($dico[sansAccents($mot3)])){
                                $name = encode("split**$split**$mot2");
                                $sousliste[$name."|".$mot2."|".$tab2['id']."|".$tab2['nombre']."|green"] = 1-1/strlen($mot2);
                            }     
                        }              
                    }
                }
            }
            $taille = sizeof($sousliste);
            if ($taille>0){
                $divid = "deroule_".encode($mot1);                 
                $print .= "<li>$mot1<font class='chiffres'> ($taille)</font><input type='button' value='+' onclick='openclose(\"$divid\")'>
                <div id='$divid' style='display:none;'>";
                arsort($sousliste);
                foreach ($sousliste as $key=>$val){
                    $t = explode("|",$key);
                    $pourcent = round(100*$val);                                  
                    $print .= "<input type='checkbox' name='$t[0]'>
                    <a href=\"javascript:getStats('stats_correc','$meth','".encode($t[1])."','$t[2]','$nomBase','$nomTable','$nomColonne');\" title='$t[3]' style='color:$t[4];'>$t[1]</a>
                    <font class='chiffres'> ($pourcent%)</font><br>";
                }
                $print .= "</div></li>";
            }
            updateProgress("Corrections",round(++$progress*100/$limite));
        }
        return $print;
    }


    function getIdFromWord($table,$colonne,$name){
        $name = addslashes($name);
        $result = mysql_query("SELECT id FROM $table WHERE $colonne='$name' LIMIT 1") or die(mysql_error());
        $row = mysql_fetch_row($result);
        return $row[0];
    }


?>                                                                                                                                               