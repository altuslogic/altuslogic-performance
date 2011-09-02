function getStats(zone,methode,string,id,base,table,colonne){

    string = string.replace(/&/g,"~AND~");
    url = "getWordStats.php?zone="+zone + "&methode="+methode + "&string="+string + "&id="+id + "&base="+base + "&table="+table + "&colonne="+colonne;

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
            document.getElementById(zone).innerHTML = xhr.responseText;                                  
        }                       
    };

    // envoi de la requête (asynchrone : false)      
    xhr.open("GET", url, false); 
    xhr.send(null);                          

}

// script pour simuler un menu déroulant  
function openclose(divid){
    if (document.getElementById(divid).style.display=='none')    
        document.getElementById(divid).style.display = 'block';
    else document.getElementById(divid).style.display = 'none'; 
}
