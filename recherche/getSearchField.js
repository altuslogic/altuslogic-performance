   url = "../recherche/getSearchField.php?key="+key;      

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
    if (xhr.readyState == 4){                 
        document.getElementById('search_zone').innerHTML = xhr.responseText; 
    }
};

// envoi de la requête       
xhr.open("GET", url, true); 
xhr.send(null);  



function soumettre(search,base,table,colonne,mode,methode,visuel,limite,nomDiv,containerAll,containerResult,containerDetails){  

    search = escape(search);
    if (search.length==0){
        document.getElementById(nomDiv).innerHTML = ""; 
        return;
    }

    var url = "../recherche/ajax.php?search="+search + "&base="+base + "&table="+table + "&colonne="+colonne
    + "&mode="+mode + "&methode="+methode + "&visuel="+visuel + "&limite="+limite 
    + "&containerAll="+containerAll + "&containerResult="+containerResult + "&containerDetails="+containerDetails;      

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

            // debug                                                 
            // alert(result);

            if (visuel=="result"){                                                               
                document.getElementById(nomDiv).innerHTML = result; 
            }

            else {
                var tab = result.split('|');
                tab.pop();
                $("#champ_<?php echo $hash; ?>").autocomplete({
                    source: tab
                });
            }                 
        }
    };

    // envoi de la requête       
    xhr.open("GET", url, true); 
    xhr.send(null);  

}