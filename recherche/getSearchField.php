<?php 
    error_reporting(15); 
    $key=$_GET['key'];
    include "config/db.php";
    init();
    $sql = "SELECT * FROM champs_recherche WHERE hash='$key'";
    $result = mysql_query($sql) or die(mysql_error());
    $ligne = mysql_fetch_array($result);                                              
    $base = $ligne['base'];
    $table = $ligne['table'];
    $colonne = $ligne['colonne'];

    function init(){
        $sql = "CREATE TABLE IF NOT EXISTS champs_recherche (
        `hash` char(10) NOT NULL,
        `base` char(20) NOT NULL,
        `table` char(20) NOT NULL,
        `colonne` char(20) NOT NULL, 
        PRIMARY KEY (`hash`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1";
        mysql_query($sql); 
    }

?>

<form>
    <input type="text" value="" onkeypress="javascript:soumettre();" id="champ_<? echo $key; ?>">
</form>
<div id="ajax"><h2>Résultats de la recherche</h2></div> 

<script>
    //ajax
    function soumettre(){ 
    var search = escape(document.getElementById('champ_'<? echo $key; ?>).value);
    if (search.length==0) return; 

    var url = "ajax.php?search=" + search;

    // création d'un objet capable d'interagir avec le serveur
    try {
        // Essayer IE
        xhr = new ActiveXObject("Microsoft.XMLHTTP");   
        //document.getElementById('ajax').innerHTML = "micro";   
    }
    catch(e){
        // Echec, utiliser l'objet standard    
        xhr = new XMLHttpRequest();
        //document.getElementById('ajax').innerHTML = "other"; 
    }

    // attente du résultat
    xhr.onreadystatechange = function(){
        // instructions de traitement de la réponse  
        if (xhr.readyState == 4) {

            var print = "<h2>Résultats de la recherche</h2>";
            var result = xhr.responseText;
            var tab = result.split('|');

            if (tab.length==1) print += "Pas de résultats.<br>";
            else {
                for (var i=0; i<tab.length; i++){
                    print += tab[i];  
                }
            }                                                             

            document.getElementById('ajax').innerHTML = print; 

        }
        else { 
            //document.getElementById('ajax').innerHTML = "Erreur : " + url;    
        }   
    };

    // envoi de la requête
    xhr.open("GET", url, true); 
    xhr.send(null); 
}
</script>