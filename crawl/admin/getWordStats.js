function getStats(id,base,table,colonne){

    url = "getWordStats.php?id="+id + "&base="+base + "&table="+table + "&colonne="+colonne;

    // cr�ation d'un objet capable d'interagir avec le serveur                                           
    try {
        // Essayer IE
        xhr = new ActiveXObject("Microsoft.XMLHTTP");             
    }
    catch(e){
        // Echec, utiliser l'objet standard    
        xhr = new XMLHttpRequest();                           
    }

    // attente du r�sultat
    xhr.onreadystatechange = function(){
        // instructions de traitement de la r�ponse  
        if (xhr.readyState == 4){
            document.getElementById('stats_keywords').innerHTML = xhr.responseText;                                  
        }                       
    };

    // envoi de la requ�te (asynchrone : false)      
    xhr.open("GET", url, false); 
    xhr.send(null);                          

}