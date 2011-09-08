<?php

    header("Content-type: text/html; charset=ISO-8859-1");
    foreach ($_GET as $key=>$val){
        $$key = $val;
    }

    $nomMaitre = $nomBase;
    include "config/db.inc.php";
    include "config/config.inc.php";
    include "search_funcs.php";                  
    
    // Détermination des colonnes nécessaires
    $tabDetails = explode("~",$container);
    $tabCol = array(strtoupper($nomColonne));
    for ($i=1; $i<sizeof($tabDetails); $i+=2){
        array_push($tabCol,$tabDetails[$i]);
    }
    $tabCol = array_unique($tabCol);

    // Recherche proprement dite
    $search = str_replace("~plus~","+",$search);
    $tab = recherche($search,$hash,$mode,$methode,$tabCol,$page,$limite,$source,null);
    $result = $tab['resultats'];
    $print = "";

    if ($visuel=="result"){
        $res = "";
        if (sizeof($result)==0) $res = str_replace("~RES~","Pas de résultats.",$container_list);
        else {
            foreach ($result as $r){     
                $details = $container;
                foreach ($tabCol as $col){
                    $txt = decodeUTF($r[strtolower($col)]);                             
                    // Remplacement des ~COLONNE~ par la valeur de colonne
                    $details = str_replace("~$col~",$txt,$details);
                }
                $res .= str_replace("~RES~",$details,$container_list);
            }
        }                                                               


        
                               /*


        $from = ($start-1) * 10;
        $to = min(($start)*10, $numOfPages);

        
        $linksQuery = "select link_id, url from ".$mysql_table_prefix."links where site_id = $site_id and url like '%$filter%' order by url limit $from, $per_page";
        $result = mysql_query($linksQuery);
        echo mysql_error();
        ?>
        <div id="submenu"></div>
        <br/>
        <center>
        <b>Pages of site <a href="admin.php?f=20&site_id=<?php  print $site_id?>"><?php print $url;?></a></b><br/>
        <p>
        <form action="admin.php" method="post">
        Urls per page: <input type="text" name="per_page" size="3" value="<?php print $per_page;?>"> 
        Url contains: <input type="text" name="filter" size="15" value="<?php print $filter;?>"> 
        <input type="submit" id="submit" value="Filter">
        <input type="hidden" name="start" value="1">
        <input type="hidden" name="site_id" value="<?php print $site_id?>">
        <input type="hidden" name="f" value="21">
        </form>
        </p>
    <table width="600"><tr><td>
        <table cellspacing ="0" cellpadding="0" class="darkgrey" width ="100%"><tr><td>
        <table  cellpadding="3" cellspacing="1" width="100%">

        <?php 

        $pages = ceil($numOfPages / $per_page);
        $prev = $start - 1;
        $next = $start + 1;

        if ($pages > 0)
            print "<center>Pages: ";

        $links_to_next =10;
        $firstpage = $start - $links_to_next;
        if ($firstpage < 1) $firstpage = 1;
        $lastpage = $start + $links_to_next;
        if ($lastpage > $pages) $lastpage = $pages;
        
        for ($x=$firstpage; $x<=$lastpage; $x++)
            if ($x<>$start)    {
                print "<a href=admin.php?f=21&site_id=$site_id&start=$x&filter=$filter&per_page=$per_page>$x</a> ";
            }     else
                print "<b>$x </b>";
        print"</td></tr></table></center>";

                                                    */
        
        
        
        
        
        
        
        $print = str_replace("~TITLE~","Résultats de la recherche : ".decodeUTF($search),$container_all);
        $print = str_replace("~ALL~",$res,$print);
        $print = str_replace("~TIME~","Temps écoulé : ".$tab['temps']." seconde(s)".$tab['nombre'],$print);                                             
    }

    else {
        foreach ($result as $r){                                         
            $suggest = $container;
            foreach ($tabCol as $col){
                $txt = decodeUTF($r[strtolower($col)]); 
                // Remplacement des ~COLONNE~ par la valeur de colonne  
                $suggest = str_replace("~$col~",$txt,$suggest);
            }
            $print .= $suggest."|";
        }
    }

    echo $print;

?>
