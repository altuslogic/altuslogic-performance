<?php 

    $nomBase="maitre";

    include "../proto2/config/db.php";
    include "../proto2/time_function.php"; 
    include "../proto2/controller.php";

    error_reporting(15); 
    if (isset($_GET['key'])) $hash=$_GET['key'];
    else $hash="";

    $sql = "SELECT * FROM champs_recherche WHERE hash='$hash' LIMIT 1";
    $result = mysql_query($sql) or die(mysql_error());
    $ligne = mysql_fetch_assoc($result);
                                
    foreach ($ligne as $key => $val){    
        $$key = $val;                                       
    }                   

    if ($resume==1){
        echo "<table border='1'>"; 
        foreach ($ligne as $key => $val){    
            echo "<tr><td>".$key."</td><td>".$val."</td></tr>";
        }          
        echo "</table>";
    }

    mysql_select_db($nomBase);

    $param = "\"".$nomBase."\",\"".$nomTable."\",\"".$nomColonne."\",\"".$mode."\",\"".$methode."\",\"".$visuel."\",\"".$limite."\",\"".$containerAll."\",\"".$containerResult."\"";     

    $ok = "OK";
    $t = "y_".$nomTable."_".$nomColonne;
    if ($methode=='tables' && !tableExiste($t."_stats") || $methode=='mot' && !tableExiste($t."_keyword") || $methode=='tout' && !tableExiste($t."_keyphrase")){
        $ok = "KO";
    }

?> 

<head> 
    <link rel="stylesheet" href="../jquery.ui/all.css"> 
    <script src="../jquery-1.5.1.js"></script> 
    <script src="../jquery.ui/core.js"></script> 
    <script src="../jquery.ui/widget.js"></script> 
    <script src="../jquery.ui/position.js"></script> 
    <script src="../jquery.ui/autocomplete.js"></script>
</head> 

<br><form>
    <input type='text' onkeyup='javascript:soumettre(this.value,<?php echo $param; ?>);' id='champ_<?php echo $key; ?>' style="background-color: transparent; color: #444; border: 1px solid #444;">
    <?php echo $ok; ?>
</form>                    
<div id='resultats'></div>  

<script> 
    $("#champ_<?php echo $hash; ?>").autocomplete({source: []}); 
</script>

<script>
    //ajax
    function soumettre(search,base,table,colonne,mode,methode,visuel,limite,containerAll,containerResult){  
                      
        search = escape(search);

        if (search.length==0){
            document.getElementById('resultats').innerHTML = ""; 
            return;
        }

        var url = "ajax.php?search="+search + "&base="+base + "&table="+table + "&colonne="+colonne
        + "&mode="+mode + "&methode="+methode + "&limite="+limite;    
                                                    
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

                // debug                                                 
                //alert(result);

                if (visuel=="result"){                                              
                    var res = ""; 
                    if (tab.length==1) res = containerResult.replace("~RES~","Pas de résultats.");
                    else {
                        for (var i=0; i<tab.length-1; i++){
                            res += containerResult.replace("~RES~",tab[i]);  
                        }
                    }                                                               

                    var print = containerAll.replace("~TITLE~","Résultats de la recherche : "+search);
                    print = print.replace("~ALL~",res);
                    print = print.replace("~TIME~","Temps écoulé : "+tab[tab.length-1]+" seconde(s)");
                    
                    document.getElementById('resultats').innerHTML = print; 
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