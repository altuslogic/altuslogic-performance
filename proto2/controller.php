<?php

    function creeLog(){
        global $nomBase; 
        $temps = start_timer();
        $sql = "CREATE TABLE IF NOT EXISTS y_".$nomBase."_log (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `action` varchar(255) NOT NULL,
        `heure` datetime NOT NULL,
        `temps` float NOT NULL DEFAULT '0', 
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        mysql_query($sql);
        $sql = "TRUNCATE TABLE y_".$nomBase."_log";
        mysql_query($sql); 
        updateLog("Création du log",end_timer($temps));
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
            $sql = "SELECT $nomColonne FROM y_".$nomBase."_stats WHERE ordre='$i'-1 AND nombre>'$thres'";
            $result = mysql_query($sql);
            if (mysql_num_rows($result)==0) break;
            while ($tab = mysql_fetch_array($result)){
                creeSousTables($tab[$nomColonne]);
            } 
        }
        progressBar("Création des tables terminée",100); 
        updateLog("Création des tables",end_timer($temps));           
    }

    function clearTables(){      
        operation("clearTable","Réinitialisation tables"); 
    }

    function deleteTables(){      
        operation("deleteTable","Suppression tables"); 
    }

    function creeStats(){
        global $nomBase; 
        clearStats();
        $sql = "CREATE TABLE IF NOT EXISTS y_".$nomBase."_stats (
        `name` varchar(255) NOT NULL,
        `ordre` int(11) NOT NULL,
        `nombre` int(11) NOT NULL DEFAULT '0',
        `goto` text NOT NULL,
        `temps` float NOT NULL DEFAULT '0', 
        PRIMARY KEY (`name`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        mysql_query($sql); 
    }

    function phase1(){
        mysql_query("RESET QUERY CACHE");           
        operation("methode1","Méthode 1"); 
    }

    function phase2(){
        mysql_query("RESET QUERY CACHE");            
        operation("methode2","Méthode 2");
    }

    /***
    * Réalise une opération quelconque sur tous les mots de la table Stats
    * @param mixed $op : le nom de l'opération
    * @param mixed $comm : le texte à afficher dans la barre de progression
    * */
    function operation($op,$comm){ 
        global $nomBase,$nomColonne;
        $temps = start_timer();
        $sql = "SELECT $nomColonne FROM y_".$nomBase."_stats";
        $result = mysql_query($sql);
        $count_table=mysql_num_rows($result);  //Richard
        $cpt=0;
        $progress=0;
        while ($tab = mysql_fetch_array($result)){
            $op($tab[$nomColonne]);
            echo $tab[$nomColonne]." "; //Richard
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
    * Création et remplissage d'une sous-table contenant
    * tous les éléments de la table d'origine dont le nom contient un certain mot 
    * @param mixed $mot : le mot
    * @param mixed $progress : la progression de l'opération 
    */
    function creeTable_old($mot,$progress){

        global $nomBase;

        $table;
        if (strlen($mot)==1) $table = $nomBase;
        else $table = "z_".$nomBase."_".getTable($mot,strlen($mot)-1);

        $sql = "SELECT name,id FROM $table WHERE name LIKE '%$mot%'";
        $result = mysql_query($sql) or die($sql."<br>[[SKIP DIED '%$mot%']]<br>");
        $count_table=mysql_num_rows($result);           $sql_echo=$sql;           //Richard

        $sql = "";
        $cpt = 0;
        $group=0;
        $sql_value=array();
        $new_bundle=1;/// splitting the insert in 4000 inserts
        while ($tab = mysql_fetch_array($result)){ 
            $sql_value[$group] .= ($new_bundle==1?"":",")."('$tab[id]','$tab[name]')";/// splitting the insert in 4000 inserts
            $cpt++;
            $new_bundle=0;/// splitting the insert in 4000 inserts
            if($cpt%4000==0){$new_bundle=1;$group++;} /// splitting the insert in 4000 inserts
        }
        if ($count_table>0){
            echo " $mot:".$count_table ." ";
            $sql2 = "CREATE TABLE z_".$nomBase."_".$mot." LIKE original_index" ; //new thing .. copy table from model
            /* $sql2 = "CREATE TABLE IF NOT EXISTS z".$nomBase."_".$mot." (
            `id` int(11) NOT NULL,
            `name` text NOT NULL,
            PRIMARY KEY  (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;*/
            mysql_query($sql2);   
            while($group>=0){
                mysql_query("INSERT INTO z_".$nomBase."_".$mot." (`id`,`name`) VALUES ".$sql_value[$group]);
                $group--;
            }
            initStat($mot,$cpt);       
        }else{ 
        }
        $pourcent = 0;
        for ($i=0; $i<strlen($mot); $i++){
            $pourcent += (ord(substr($mot,$i,1))-ord("A"))*100/pow(26,$i+1);
        }
        $pourcent = round($pourcent);
        // actualise la barre de progression lorsque sa valeur a changé
        if ($pourcent!=$progress){
            $progress = $pourcent;
            progressBar("Création des tables $mot:$count_table ",$pourcent);
        }
        // part to put in new code to automate the default cookie settings cookie
        global $thres;
        global $ordreMax;


        if ($cpt>$thres && strlen($mot)<$ordreMax){
            for ($i=ord("A"); $i<=ord("Z"); $i++){                    
                creeTable($mot.chr($i),$progress);
            }
        }

    }

    function creeSousTables($debut){
        global $nomBase,$nomColonne;

        $pourcent = 0;
        for ($i=0; $i<strlen($mot); $i++){
            $pourcent += (ord(substr($mot,$i,1))-ord("A"))*100/pow(26,$i+1);
        }
        $pourcent = round($pourcent);
        // actualise la barre de progression lorsque sa valeur a changé
        if ($pourcent!=$progress){
            $progress = $pourcent;
            progressBar("Création des tables $debut"."x",$pourcent);
        }

        for ($lettre=ord("A"); $lettre<=ord("Z"); $lettre++){ 

            $mot = $debut.chr($lettre);
            $table; 
            if (strlen($mot)==1) $table = $nomBase;
            else {
                $table = "z_".$nomBase."_".getTable($mot,strlen($mot)-1);
            }

            $sql = "SELECT $nomColonne,id FROM $table WHERE $nomColonne LIKE '%$mot%' LIMIT 1";
            $result = mysql_query($sql) or die($sql."<br>[[SKIP DIED '%$mot%']]<br>");
            $estVide = mysql_num_rows($result)==0;

            if (!$estVide){
                echo " $mot";
                flush();
                $sql1 = "CREATE TABLE z_".$nomBase."_".$mot." LIKE y_original_index"; //new thing .. copy table from model
                $sql2 = "INSERT INTO z_".$nomBase."_".$mot." (SELECT id,$nomColonne FROM $table WHERE $nomColonne LIKE '%$mot%')";
                mysql_query($sql1);   
                mysql_query($sql2); 
                initStat($mot);       
            }                   

        }

    }     


    function clearTable($mot){
        global $nomBase;  
        $sql = "TRUNCATE TABLE z_".$nomBase."_".$mot;
        mysql_query($sql);
    }

    function deleteTable($mot){
        global $nomBase;  
        $sql = "DROP TABLE z_".$nomBase."_".$mot;
        mysql_query($sql);
    }

    /***
    * Remplissage initial de la table Stats
    * @param mixed $mot : le mot
    */
    function initStat($mot){
        global $nomBase,$nomColonne; 
        $ordre = strlen($mot);
        $sql = "SELECT COUNT(*) FROM z_".$nomBase."_".$mot;
        $taille = mysql_result(mysql_query($sql),0);                         
        $sql = "INSERT INTO y_".$nomBase."_stats SET ordre='$ordre', $nomColonne='$mot', nombre='$taille'";
        mysql_query($sql);
    }

    /***
    * Remplissage partiel de la table Stats
    * avec la durée de la recherche d'un mot grâce à la méthode 2
    * (recherche dans la sous-table la moins remplie)
    * @param mixed $mot : le mot à rechercher
    */
    function performance($mot){
        global $nomBase,$nomColonne;
        $debut = start_timer();
        recherche($mot,"milieu");
        $temps = end_timer($debut);       
        $sql = "UPDATE y_".$nomBase."_stats SET temps='$temps' WHERE $nomColonne='$mot'";
        mysql_query($sql);
    }

    function clearStats(){
        global $nomBase;
        $sql = "TRUNCATE TABLE y_".$nomBase."_stats";
        mysql_query($sql);
    }

    function deleteStats(){
        global $nomBase;
        $sql = "DROP TABLE y_".$nomBase."_stats";
        mysql_query($sql);
    }

    function deleteLog(){
        global $nomBase;
        $sql = "DROP TABLE y_".$nomBase."_log";
        mysql_query($sql);
    }

    /***
    * Effectue la recherche d'une chaîne de caractères  
    * @param mixed $text : la chaîne à chercher
    * @param mixed $option : l'option de recherche
    */
    function recherche($text, $mode){

        global $nomBase,$nomColonne,$ordreMax;
        $temps = start_timer();
        $print="";

        if ($mode=="tout"){
            // mode expression régulière
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
            $long = $tab["taille"];
            $nb = $tab["nombre"];

            if ($long>=1){

                $table = "";
                $truc = $long>$ordreMax?$ordreMax:$long;
                while ($table==""){
                    $table = getTable($text,$truc--);
                }
                echo "1) Choix de la table : ",end_timer($temps1),"<br>";  
                $temps2 = start_timer();

                /*   
                echo "Choix : ".$tab[name]." (".$tab[nombre].")<br>"; 
                while ($tab = mysql_fetch_array($result)){ 
                echo "Autres : ".$tab[name]." (".$tab[nombre].")<br>";      
                }
                */ 

                $sql = "SELECT $nomColonne FROM z_".$nomBase."_".$table." WHERE";
                $debut = $mode=="debut"?"":"%";
                $fin = $mode=="fin"?"":"%";
                $and = "";

                for ($i=0; $i<sizeof($mot); $i++){
                    if (strlen($mot[$i])>0){
                        $sql .= $and." $nomColonne LIKE '".$debut."$mot[$i]".$fin."'";
                        $and = " AND"; 
                        if (i==0){
                            $debut = $fin = "%";
                        }
                    }
                }                                     

                $sql .= " LIMIT 250";       

                $result = mysql_query($sql);
                echo "2) Requête : ",end_timer($temps2),"<br><br>";
                $temps4 = start_timer();
                while ($tab = mysql_fetch_array($result)){ 
                    $print .= ($print==""?"":",").$tab[$nomColonne];      
                } 
                echo $sql,"<br>";               
            }
        }
        updateLog("Recherche ".$text,$temps=end_timer($temps));
        return array("resultats" => ($print==""?"Pas de résultats.":$print), "temps" => $temps);
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
    * Renvoie la table appropriée dans laquelle chercher
    * une chaîne de caractères (la sous-table la moins remplie)
    * @param mixed $text : la chaîne à chercher
    * @param mixed $long : l'ordre de la table
    */
    function getTable($text,$long){

        global $nomBase,$nomColonne;       
        $sql = "SELECT $nomColonne,MIN(nombre) FROM y_".$nomBase."_stats WHERE";   
        $liste = array();   
        $or="";
        for ($i=0; $i<strlen($text)-$long+1; $i++){
            $t = strtoupper(substr($text,$i,$long));
            // ignore les suites de caractères contenant un espace
            if (strpos($t," ")===FALSE && array_search($t,$liste)===FALSE){
                array_unshift($liste,$t);
                $sql .=  $or." $nomColonne = '".$t."'";
                $or = " OR";
            }
        }                                     
        //echo $sql,"<br>";   
        $result = mysql_query($sql);
        $tab = mysql_fetch_array($result);   
        return $tab[$nomColonne];
    } 

    function existe($text){

        global $nomBase,$nomColonne,$ordreMax;
        // test d'appartenance à la table d'origine (en fait à la sous-table appropriée)
        $table = "";
        $truc = $ordreMax;
        while ($table=="" && $truc>0){
            $table = getTable($text,$truc--);
        }
        if ($table == "") return false;
        $sql = "SELECT $nomColonne FROM z_".$nomBase."_".$table." WHERE $nomColonne='$text' LIMIT 1";
        $result = mysql_query($sql);      
        return (mysql_numrows($result)!=0);
    } 

    function insertion($text){

        global $nomBase;
        $temps = start_timer();
        // mise à jour de la table principale
        $sql = "INSERT INTO ".$nomBase." SET name='$text'";
        echo "Insertion du mot ",$text," dans la table ".$nomBase."<br>"; 
        mysql_query($sql);
        $id = mysql_insert_id(); 
        // mise à jour des sous-tables et de la table Stats
        $liste = array();
        for ($long=2; $long<=3; $long++){   
            for ($i=0; $i<strlen($text)-$long+1; $i++){
                $t = strtoupper(substr($text,$i,$long));
                if (array_search($t,$liste)===FALSE){
                    array_unshift($liste,$t);  
                    $sql = "INSERT INTO z_".$nomBase."_".$t." SET name='$text', id='$id'";
                    mysql_query($sql);
                    $sql = "UPDATE y_".$nomBase."_stats SET nombre=nombre+1 WHERE name='$t'";
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
        // mise à jour de la table principale
        $sql = "DELETE FROM ".$nomBase." WHERE name='$text' LIMIT 1";
        echo $sql."<br>";
        $result = mysql_query($sql);          
        // mise à jour des sous-tables et de la table Stats
        $liste = array();
        for ($long=2; $long<=3; $long++){   
            for ($i=0; $i<strlen($text)-$long+1; $i++){
                $t = strtoupper(substr($text,$i,$long));
                if (array_search($t,$liste)===FALSE){
                    array_unshift($liste,$t);  
                    $sql = "DELETE FROM z_".$nomBase."_".$t." WHERE name='$text' LIMIT 1";
                    echo $sql."<br>";
                    mysql_query($sql);
                    $sql = "UPDATE y_".$nomBase."_stats SET nombre=nombre-1 WHERE name='$t'";
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
        `id` int(11) NOT NULL AUTO_INCREMENT,    
        `name` varchar(30) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        mysql_query($sql); 
        // FOREIGN KEYS
        $sql = "CREATE TABLE IF NOT EXISTS y_".$nomBase."_".$nomColonne."_index (
        `id` int(11) NOT NULL,                                
        `keyword` int(11) NOT NULL,
        PRIMARY KEY (`id`,`keyword`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1" ;
        mysql_query($sql); 
        $sql = "TRUNCATE TABLE y_".$nomBase."_".$nomColonne."_index";
        mysql_query($sql);               

        $sql = "SELECT id,$nomColonne FROM $nomBase LIMIT 50";
        $result = mysql_query($sql);
        while ($tab = mysql_fetch_array($result)){
            $mot = explode(" ",$tab[$nomColonne]);
            for ($i=0; $i<strlen($mot); $i++){
                $t = strtoupper($mot[$i]);
                if ($t!=""){
                    $sql = "SELECT id FROM y_".$nomBase."_".$nomColonne."_keyword WHERE name='$t' LIMIT 1";
                    $res = mysql_query($sql);
                    if (mysql_numrows($res)>0){
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
    * Affiche quelques détails sur la table Stats
    */
    function getDetails(){
        global $nomBase;  

        $sql = "SELECT COUNT(*) FROM y_".$nomBase."_stats";     
        $result = mysql_query($sql);
        $taille = mysql_result($result,0);
        $print .= "Nombre de sous-tables : ".$taille."<br>";
        $i=1;
        while ($i<10){
            $sql = "SELECT COUNT(*) FROM y_".$nomBase."_stats WHERE ordre=$i";     
            $result = mysql_query($sql);
            $taille = mysql_result($result,0);
            if ($taille==0) break;
            $print .= "Nombre de sous-tables d'ordre ".$i." : ".$taille."<br>"; 
            $i++;
        }
        $max = $i;                                

        $sql = "SELECT AVG(temps) AS moyenne FROM y_".$nomBase."_stats WHERE nombre>0";
        $result = mysql_query($sql);
        $tab = mysql_fetch_array($result);
        $print .= "Temps moyen : ".$tab[moyenne]." seconde(s).<br>";

        for ($i=1; $i<$max; $i++){
            $print .= "<br><b>Ordre ".$i."</b><br>";
            $sql = "SELECT nombre,name,temps FROM y_".$nomBase."_stats WHERE ordre=$i ORDER BY nombre DESC LIMIT 25";
            $result = mysql_query($sql);
            $print .= "<table><tr><td>Nom</td><td>Nombre</td><td>Proportion</td></tr>";
            while ($tab = mysql_fetch_array($result)){
                if ($i==1){
                    $sql = "SELECT COUNT(*) FROM $nomBase";
                    $prop = mysql_result(mysql_query($sql),0);
                    $txt = round(100*$tab[nombre]/$prop)."%"; 
                }
                else {                
                    $sup = substr($tab[name],0,strlen($tab[name])-1);
                    $sql = "SELECT nombre FROM y_".$nomBase."_stats WHERE name='$sup' LIMIT 1";
                    $prop = mysql_fetch_array(mysql_query($sql));
                    $txt = round(100*$tab[nombre]/$prop[0])."% - ";
                    $sup = substr($tab[name],1,strlen($tab[name])-1);
                    $sql = "SELECT nombre FROM y_".$nomBase."_stats WHERE name='$sup' LIMIT 1";
                    $prop = mysql_fetch_array(mysql_query($sql));
                    $txt .= round(100*$tab[nombre]/$prop[0])."%"; 
                }
                $prop = mysql_fetch_array(mysql_query($sql));
                $print .= "<tr><td>".$tab[name]."</td><td>".$tab[nombre]."</td><td>".$txt."</td/></tr>"; 
            }
            $print .= "</table>";

            $sql = "SELECT AVG(nombre) AS moyenne FROM y_".$nomBase."_stats WHERE ordre=$i AND nombre>0";
            $result = mysql_query($sql);
            $tab = mysql_fetch_array($result);
            $print .= "Moyenne : ".$tab[moyenne]."<br>";

            $sql = "SELECT STD(nombre) AS ecart FROM y_".$nomBase."_stats WHERE ordre=$i AND nombre>0";
            $result = mysql_query($sql);
            $tab = mysql_fetch_array($result);
            $print .= "Ecart-type : ".$tab[ecart]."<br>";
        }

        /* $print .= "<br><b>Liste des 100 premiers éléments les plus nombreux</b>";
        $sql = "SELECT name,COUNT(*) AS compte FROM $nomBase GROUP BY name ORDER BY compte DESC LIMIT 100";
        $result = mysql_query($sql);
        while ($tab = mysql_fetch_array($result)){
        $print .= "<br>".$tab[name]." - ".$tab[compte]; 
        }  */
        return $print;
    } 

?>
