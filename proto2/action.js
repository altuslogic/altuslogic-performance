function soumettre(){ 

    var search = escape(document.getElementById('champ1').value);
    if (search.length==0) return;                          
    var mode = document.getElementById('mode').value; 
    var url = "http://antoineluong.com/proto2/ajax.php?search=" + search + "&mode=" + mode;
    //document.getElementById('ajax').innerHTML = "Travail en cours : " + url;

    // création d'un objet capable d'interagir avec le serveur
    try {
        xhr = new ActiveXObject("Microsoft.XMLHTTP");    // Essayer IE
    }
    catch(e)   // Echec, utiliser l'objet standard
    {
        xhr = new XMLHttpRequest();
    }

    // attente du résultat
    xhr.onreadystatechange = function(){
        // instructions de traitement de la réponse }; 
        if (xhr.readyState == 4) {
            document.getElementById('ajax').innerHTML = xhr.responseText; 
        }
        else { 

        }   
    };

    // envoi de la requête
    xhr.open("GET", url, true); 
    xhr.send(null); 
}