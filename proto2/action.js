function soumettre(){ 
    var search = escape(document.getElementById('champ1').value);
    if (search.length==0) return;                          
    var mode = document.getElementById('mode').value; 
    var url = "http://localhost/proto2/ajax.php?search=" + search + "&mode=" + mode;

    // cr�ation d'un objet capable d'interagir avec le serveur
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

    // attente du r�sultat
    xhr.onreadystatechange = function(){
        // instructions de traitement de la r�ponse  
        if (xhr.readyState == 4) {
            document.getElementById('ajax').innerHTML = xhr.responseText; 
        }
        else { 
            document.getElementById('ajax').innerHTML = "Erreur : " + url;     
        }   
    };

    // envoi de la requ�te
    xhr.open("GET", url, true); 
    xhr.send(null); 
}