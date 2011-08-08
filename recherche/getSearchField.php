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

?> 

<head> 
    <link rel="stylesheet" href="../jquery.ui/all.css"> 
    <script src="../jquery-1.5.1.js"></script> 
    <script src="../jquery.ui/core.js"></script> 
    <script src="../jquery.ui/widget.js"></script> 
    <script src="../jquery.ui/position.js"></script> 
    <script src="../jquery.ui/autocomplete.js"></script>
</head>

<table border='1'><tr><td>base</td><td><?php echo $base; ?></td></tr>   
    <tr><td>table</td><td><?php echo $table; ?></td></tr>
    <tr><td>colonne</td><td><?php echo $colonne; ?></td></tr>
    <tr><td>mode</td><td><?php echo $mode; ?></td></tr>
    <tr><td>méthode</td><td><?php echo $methode; ?></td></tr> 
    <tr><td>visuel</td><td><?php echo $visuel; ?></td></tr>
</table>   

<br><form>
    <input type='text' onkeyup='javascript:soumettre(this.value,<?php echo $param; ?>);' id='champ_<?php echo $key; ?>'>   
    <?php echo(tableExiste("y_".$table."_".$colonne."_stats")? " OK": " KO"); ?>
</form>                    
<div id='ajax'></div>  

<script> 
    $("#champ_<?php echo $key; ?>").autocomplete({source: []}); 
</script>

<script>
    //ajax
    function soumettre(search,base,table,colonne,mode,methode,visuel){  

        search = escape(search);

        if (search.length==0){
            document.getElementById('ajax').innerHTML=""; 
            return;
        }

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

                var result = xhr.responseText;
                var tab = result.split('|');

                if (visuel=="result"){

                    var print = "<b>Résultats de la recherche : "+search+"</b>"; 
                    if (tab.length==1) print += "<br>Pas de résultats.";
                    else {
                        for (var i=0; i<tab.length-1; i++){
                            print += "<br>"+tab[i];  
                        }
                    }                                                             
                    print += "<br><br>Temps écoulé : "+tab[tab.length-1]+" seconde(s)";

                    document.getElementById('ajax').innerHTML = print; 
                }

                else {
                    tab.pop();
                    $("#champ_<?php echo $key; ?>").autocomplete({
                        source: tab
                    });
                }                 
            }
        };

        // envoi de la requête
        xhr.open("GET", url, true); 
        xhr.send(null);  

    }

</script>