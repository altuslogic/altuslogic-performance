<?php 
    error_reporting(15); 
    $key=$_GET['key'];
    $nomBase="maitre";

    include "../proto2/config/db.php";
    include "../proto2/time_function.php"; 
    include "../proto2/controller.php";
    initChamps();
    $sql = "SELECT * FROM champs_recherche WHERE hash='$key'";
    $result = mysql_query($sql) or die(mysql_error());
    $ligne = mysql_fetch_array($result);
    $base = $ligne['nomBase'];
    $table = $ligne['nomTable'];
    $colonne = $ligne['nomCol'];
    $mode = $ligne['mode'];
    $methode = $ligne['methode'];
    $visuel = $ligne['visuel'];

    mysql_select_db($base);

    $param = "\"".$base."\",\"".$table."\",\"".$colonne."\",\"".$mode."\",\"".$methode."\",\"".$visuel."\"";

    echo "<table border='1'><tr><td>base</td><td>",$base,"</td></tr>";   
    echo "<tr><td>table</td><td>",$table,"</td></tr>";   
    echo "<tr><td>colonne</td><td>",$colonne,"</td></tr></table>";   
    echo "<br><form><input type='text' onkeyup='javascript:soumettre(this.value,".$param.");' id='champ_",$key,"'>";
    echo tableExiste("y_".$table."_".$colonne."_stats")?" OK":" KO";

?> 
</form>
<div id='ajax' style='border: 1px solid #444;'><b>Résultats de la recherche</b></div>  

<script>
    //ajax
    function soumettre(search,base,table,colonne,mode,methode,visuel){  

        search = escape(search);

        if (search.length==0) return; 

        var url = "ajax.php?search="+search + "&base="+base + "&table="+table + "&colonne="+colonne
        + "&mode="+mode + "&methode="+methode + "&visuel="+visuel;

        // création d'un objet capable d'interagir avec le serveur
        try {
            // Essayer IE
            xhr = new ActiveXObject("Microsoft.XMLHTTP");             
        }
        catch(e){
            // Echec, utiliser l'objet standard    
            xhr = new XMLHttpRequest();                           
        }

        // attente du résultat
        xhr.onreadystatechange = function(){
            // instructions de traitement de la réponse  
            if (xhr.readyState == 4) {

                var print = "<b>Résultats de la recherche : "+search+"</b>";
                var result = xhr.responseText;
                var tab = result.split('|');

                if (tab.length==1) print += "<br>Pas de résultats.";
                else {
                    for (var i=0; i<tab.length-1; i++){
                        print += "<br>"+tab[i];  
                    }
                }                                                             
                print += "<br><br>Temps écoulé : "+tab[tab.length-1]+" seconde(s)";

                document.getElementById('ajax').innerHTML = print; 

            }
        };

        // envoi de la requête
        xhr.open("GET", url, true); 
        xhr.send(null);  

    }

</script>