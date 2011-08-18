<?php

    function tableExiste($table){
        $sql = "SELECT COUNT(*) FROM $table";
        return mysql_query($sql);
    }

    function creeLog(){          
        $temps = start_timer();    
        $sql = "CREATE TABLE log (
        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `action` varchar(255) NOT NULL,
        `heure` datetime NOT NULL,
        `temps` float NOT NULL, 
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        $result = mysql_query($sql);
        if ($result) updateLog("Création du log",end_timer($temps));
    }

    function updateLog($action,$temps){
        global $nomMaitre,$nomBase,$nomTable;  
        mysql_select_db($nomMaitre);  
        $sql = "INSERT INTO log SET action='$action', heure=NOW(), temps='$temps'";
        mysql_query($sql); 
        mysql_select_db($nomBase);     
    }

    function initChamps(){
        $sql = "CREATE TABLE IF NOT EXISTS champs_recherche (
        `hash` char(32) NOT NULL,
        `nomBase` char(30) NOT NULL,
        `nomTable` char(50) NOT NULL,
        `nomColonne` char(20) NOT NULL, 
        `mode` enum('debut','milieu','fin','tout') NOT NULL,
        `methode` enum('direct','tables','mot','tout') NOT NULL,
        `visuel` enum('suggest','result') NOT NULL,
        `resume` tinyint(1) NOT NULL,
        `limite` smallint(5) UNSIGNED NOT NULL,
        `nomDiv` char(20) NOT NULL,
        `afficheDiv` tinyint(1) NOT NULL,
        `containerAll` text NOT NULL,
        `containerResult` text NOT NULL,
        `containerDetails` text NOT NULL,
        PRIMARY KEY (`hash`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1";
        mysql_query($sql); 
    }

    /**
    * Crée les sous-tables jusqu'à l'ordre max
    */
    function creeTables(){
        global $nomTable, $nomColonne, $ordreMax, $thres;
        $temps = start_timer();
        creeSousTables("");    
        for ($i=2; $i<=$ordreMax; $i++){
            $sql = "SELECT name FROM y_".$nomTable."_".$nomColonne."_stats WHERE ordre='$i'-1 AND nombre>'$thres'";
            $result = mysql_query($sql) or die(mysql_error());
            if (mysql_num_rows($result)==0) break;
            while ($tab = mysql_fetch_array($result)){
                creeSousTables($tab['name']);
            } 
        }
        progressBar("Création des tables terminée",100); 
        updateLog("Création des tables ".$nomColonne,end_timer($temps));           
    }

    function clearTables(){
        global $nomColonne;      
        operation("clearTable","Réinitialisation tables ".$nomColonne); 
    }

    function deleteTables(){  
        global $nomColonne;     
        operation("deleteTable","Suppression tables ".$nomColonne); 
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
        progressBar("Création des tables $debut"."x",$pourcent);

        for ($lettre=ord("A"); $lettre<=ord("Z"); $lettre++){ 

            $mot = $debut.chr($lettre);
            $table; 
            if (strlen($mot)==1) $table = $nomTable;
            else {
                $table = "z_".$nomTable."_".$nomColonne."_".getSousTable($mot,strlen($mot)-1);
            }

            $sql = "SELECT $nomColonne,id FROM $table WHERE $nomColonne LIKE '%$mot%' LIMIT 1";
            $result = mysql_query($sql) or die(mysql_error());
            $estVide = mysql_num_rows($result)==0;

            if (!$estVide){
                echo " $mot";
                flush();
                //$sql1 = "CREATE TABLE z_".$nomTable."_".$nomColonne."_".$mot." LIKE y_original_index"; //new thing .. copy table from model
                $sql1 = "CREATE TABLE z_".$nomTable."_".$nomColonne."_".$mot." (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                $nomColonne varchar(255) NOT NULL
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1";
                $sql2 = "INSERT INTO z_".$nomTable."_".$nomColonne."_".$mot." (SELECT id,$nomColonne FROM $table WHERE $nomColonne LIKE '%$mot%')";
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
        $sql = "SELECT COUNT(*) FROM z_".$nomTable."_".$nomColonne."_".$mot;
        $taille = mysql_result(mysql_query($sql),0);                         
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
        recherche($mot,$nomTable,$nomColonne,"milieu","tout","result");
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
        updateLog("Suppression stats ".$nomColonne,end_timer($temps));
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
        updateLog("Suppression index ".$nomColonne,end_timer($temps));
    }

    function deleteLog(){
        global $nomMaitre,$nomBase;      
        mysql_select_db($nomMaitre); 
        $sql = "DROP TABLE log";
        mysql_query($sql);
        mysql_select_db($nomBase); 
    } 

    /**
    * Renvoie la sous-table appropriée dans laquelle chercher
    * une chaîne de caractères (la moins remplie)
    * @param mixed $text : la chaîne à chercher
    * @param mixed $long : l'ordre de la table
    */
    function getSousTable($text,$long){

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

    /**
    * Teste l'existence d'un mot
    * @param mixed $text : le mot à chercher
    */
    function existe($text){

        global $nomTable,$nomColonne,$ordreMax;
        // test d'appartenance à la table d'origine (en fait à la sous-table appropriée)
        $table = "";
        $truc = $ordreMax;
        while ($table=="" && $truc>0){
            $table = getSousTable($text,$truc--);
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
        $id = mysql_insert_id(); 
        // mise à jour des sous-tables et de la table Stats
        $liste = array();
        for ($long=2; $long<=3; $long++){   
            for ($i=0; $i<strlen($text)-$long+1; $i++){
                $t = strtoupper(substr($text,$i,$long));
                if (array_search($t,$liste)===FALSE){
                    array_unshift($liste,$t);  
                    $sql = "INSERT INTO z_".$nomTable."_".$nomColonne."_".$t." SET name='$text', id='$id'";
                    mysql_query($sql);
                    $sql = "UPDATE y_".$nomTable."_".$nomColonne."_stats SET nombre=nombre+1 WHERE name='$t'";
                    echo "Insertion du mot ",$text," dans la sous-table ".$t."<br>";
                    mysql_query($sql);  
                }           
            }
        }
        updateLog("Insertion ".$text,$temps=end_timer($temps)); 
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
        updateLog("Suppression ".$text,$temps=end_timer($temps));
    }

    /**
    * Crée un index pour la colonne actuelle
    * @param mixed $motParMot : détermine si la chaîne doit être découpée en mots
    */
    function creeIndex($motParMot){
        global $nomTable,$nomColonne; 
        $temps = start_timer(); 
        $key = $motParMot? "word": "phrase";
        $sql = "CREATE TABLE IF NOT EXISTS y_".$nomTable."_".$nomColonne."_key".$key." (
        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,    
        $nomColonne varchar(".($motParMot?30:100).") NOT NULL,
        `nombre` int(11) UNSIGNED NOT NULL,
        `ignored` tinyint(1) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        mysql_query($sql); 
        // FOREIGN KEYS
        $sql = "CREATE TABLE IF NOT EXISTS y_".$nomTable."_".$nomColonne."_index".$key." (
        `id` int(11) UNSIGNED NOT NULL,                                
        `keyword` int(11) UNSIGNED NOT NULL,
        PRIMARY KEY (`id`,`keyword`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        mysql_query($sql); 
        $sql = "TRUNCATE TABLE y_".$nomTable."_".$nomColonne."_index".$key;
        mysql_query($sql);               

        $sql = "SELECT id,$nomColonne FROM $nomTable";
        $result = mysql_query($sql);
        $total = mysql_num_rows($result);
        $cpt = 0; $progress = 0;
        while ($tab = mysql_fetch_array($result)){
            $mot = $motParMot? explode(" ",$tab[$nomColonne]): array($tab[$nomColonne]);
            for ($i=0; $i<sizeof($mot); $i++){
                $t = strtoupper($mot[$i]);
                if ($t!=""){
                    $sql = "SELECT id FROM y_".$nomTable."_".$nomColonne."_key".$key." WHERE $nomColonne='$t' LIMIT 1";
                    $res = mysql_query($sql);                   
                    if (mysql_num_rows($res)>0){
                        // Le mot-clé est déjà présent dans la table     
                        $ligne = mysql_fetch_array($res);
                        $sql = "UPDATE y_".$nomTable."_".$nomColonne."_key".$key." SET nombre=nombre+1 WHERE id='$ligne[id]'";
                        mysql_query($sql) or die($sql);
                        $sql = "INSERT INTO y_".$nomTable."_".$nomColonne."_index".$key." SET id='$tab[id]', keyword='$ligne[id]'";
                        mysql_query($sql);
                    }
                    else {
                        // Le mot-clé est absent de la table   
                        $sql = "INSERT INTO y_".$nomTable."_".$nomColonne."_key".$key." SET $nomColonne='$t', nombre='1'"; 
                        mysql_query($sql) or die($sql);
                        $id = mysql_insert_id();
                        $sql = "INSERT INTO y_".$nomTable."_".$nomColonne."_index".$key." SET id='$tab[id]', keyword='$id'";
                        mysql_query($sql);
                    }
                } 
            }

            //echo $tab[$nomColonne]," "; 
            $cpt++; 
            $pourcent = round(100*$cpt/$total);                      
            if ($pourcent > $progress){
                $progress = $pourcent;                         
                progressBar("Création de l\'index ".$nomColonne,$progress);
            }   

        } 
        updateLog("Index ".$nomColonne,end_timer($temps)); 
    }

    /***
    * Affiche quelques détails sur la table Stats
    */
    function getDetails(){
        global $nomTable,$nomColonne;  
        $tableStats = "y_".$nomTable."_".$nomColonne."_stats";

        $sql = "SELECT COUNT(*) FROM $tableStats";     
        $result = mysql_query($sql);
        $taille = mysql_result($result,0);
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
                    $sql = "SELECT COUNT(*) FROM $nomTable";
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

        /* $print .= "<br><b>Liste des 100 premiers éléments les plus nombreux</b>";
        $sql = "SELECT name,COUNT(*) AS compte FROM $nomTable GROUP BY name ORDER BY compte DESC LIMIT 100";
        $result = mysql_query($sql);
        while ($tab = mysql_fetch_array($result)){
        $print .= "<br>".$tab[name]." - ".$tab[compte]; 
        }  */
        return $print;
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
        global $nomBase,$nomTable,$nomColonne;

        mysql_select_db($nomBase);
        $sql = "SHOW COLUMNS FROM ".$nomTable;
        $result = mysql_query($sql) or die(mysql_error()."<br>".$sql);
        $print = "<form action='?stage=index' method='post'><table><tr><td>Nom</td><td>Type</td><td>Nombre</td><td>Tables</td><td>Mot</td><td>Phrase</td>";

        while ($ligne=mysql_fetch_array($result)){
            $nom = $ligne['Field'];

            $array = etatTables($nom);

            $tables = $array['tables']? "disabled='disabled'" : "";
            $mot = $array['mot'] || strpos($nomTable,"y_")===0? "disabled='disabled'" : "";   
            $phrase = $array['phrase'] || strpos($nomTable,"y_")===0? "disabled='disabled'" : "";

            $print .= "<tr><td>";
            if ($nom==$nomColonne){
                $print .= "<b>".$nom."</b>";  
            }
            else $print .= "<a href='?nomColonne=".$nom."'>".$nom."</a>";
            $print .= "</td><td>".$ligne['Type']."</td>";
            $sql = "SELECT COUNT(DISTINCT $nom) FROM ".$nomTable;
            $nb = "X";//mysql_result(mysql_query($sql),0);
            $print .= "<td>".$nb."</td><td><p align='center'><input type='checkbox' ".$tables." id='t_".$nom."' name='t_".$nom."'></p></td>
            <td><p align='center'><input type='checkbox' ".$mot." id='i_".$nom."' name='i_".$nom."'></p></td>
            <td><p align='center'><input type='checkbox' ".$phrase." id='j_".$nom."' name='j_".$nom."'></p></td></tr>";    
        }                                                                                                
        $print .= "</table><p align='center'><input type='submit' value='apply'></p></form>";
        return $print;
    }


    function list_log(){
        global $nomMaitre,$nomBase;

        mysql_select_db($nomMaitre);
        $sql = "SELECT action,temps,heure FROM log ORDER BY id DESC LIMIT 10";
        $result = mysql_query($sql) or die(mysql_error());
        $print = "";

        while ($ligne=mysql_fetch_array($result)){ 
            $print = "<tr><td>".$ligne['heure']."</td><td>".$ligne['action']."</td><td>".$ligne['temps']."</td></tr>".$print;  
        }
        $print = "<table><tr><td>Heure</td><td>Action</td><td>Durée</td></tr>".$print."</table>";
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
        mysql_select_db($nomBase);
        return $print."</table>";
    }


    function stats(){                     
        global $nomBase,$nomTable,$nomColonne;                                                                

        $print = "<table><tr>";
        for ($i=ord("A"); $i<=ord("Z"); $i++){ 
            $lettre = chr($i);                                               
            $print .= "<td><table><tr><td><b>".$lettre."</b></td></tr>";

            $table = "y_".$nomTable."_".$nomColonne."_keyword";                                                               
            $sql = "SELECT $nomColonne,id,nombre,ignored FROM $table WHERE $nomColonne LIKE '$lettre%' ORDER BY nombre DESC LIMIT 25";   
            $result = mysql_query($sql) or die($sql."<br>".mysql_error());  

            while ($tab = mysql_fetch_array($result)){
                $word = $tab['ignored']==0? $tab[$nomColonne]: "<i>".$tab[$nomColonne]."</i>";
                $print .= "<tr><td><a href=\"javascript:getStats('$tab[id]','$nomBase','$nomTable','$nomColonne');\" title='$tab[nombre]'>".$word."</a></td></tr>";
            }

            $print .= "</table></td>";
        }

        $print .= "</tr></table>";
        return $print;
    }

    function expressions(){                     
        global $nomTable,$nomColonne;   

        $table = "y_".$nomTable."_".$nomColonne."_keyword"; 
        $sql = "SELECT $nomColonne,nombre FROM $table ORDER BY nombre DESC LIMIT 50"; 
        $result = mysql_query($sql) or die($sql."<br>".mysql_error());  
        $print = "";

        while ($tab = mysql_fetch_array($result)){ 
            $tout = find_expression($tab[$nomColonne],$tab['nombre'],1);
            $print .= "<tr><td><b>$tab[$nomColonne]</b> <font class='chiffres'>($tab[nombre])</font></td>".$tout['affiche']."</tr>";    
        }

        return "<table border='1'>$print</table>";
    }

    function find_expression($expr,$freq,$nbMots){
        global $nomTable,$nomColonne;   

        $avant = array();
        $apres = array();

        $table = "y_".$nomTable."_".$nomColonne."_keyphrase";
        $sql = "SELECT $nomColonne,nombre FROM $table WHERE $nomColonne RLIKE '(^| )$expr(\$| )'";                     
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

        $print1 = affiche_expr($avant,"~KEY~ ".$expr,$freq,$nbMots);
        $print2 = affiche_expr($apres,$expr." ~KEY~",$freq,$nbMots);

        return array("affiche"=>$print1.$print2, "entier"=>(sizeof($avant)==1&&!array_key_exists("~",$avant) || sizeof($apres)==1&&!array_key_exists("~",$apres)));
    }

    function affiche_expr($liste,$truc,$freq,$nbMots){
        $print = "<td>";
        foreach ($liste as $key=>$val){
            $pourcent = round(100*$val/$freq);
            if ($pourcent>5){
                $blabla = str_replace("~KEY~",$key,$truc);
                $txt = "$blabla<font class='chiffres'> (".$pourcent."%)</font><br>";
                if ($pourcent>20 && $key!="~"){
                    $res = find_expression($blabla,$freq,$nbMots+1);
                    $txt = ($res['entier']?"":"<b>$txt</b>")."<table border='1'><tr>".$res['affiche']."</tr></table>";
                }
                $print .= $txt;
            }
            else break;
        }
        return $print."</td>";
    }


?>
