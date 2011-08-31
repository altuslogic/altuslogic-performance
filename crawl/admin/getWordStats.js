function getStats(zone,methode,id,base,table,colonne){

    id = id.replace(/&/g,"**");
    url = "getWordStats.php?zone="+zone + "&methode="+methode + "&id="+id + "&base="+base + "&table="+table + "&colonne="+colonne;

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
            document.getElementById(zone).innerHTML = xhr.responseText;                                  
        }                       
    };

    // envoi de la requ�te (asynchrone : false)      
    xhr.open("GET", url, false); 
    xhr.send(null);                          

}

// script pour simuler un menu d�roulant  
function openclose(divid){
    if (document.getElementById(divid).style.display=='none')    
        document.getElementById(divid).style.display = 'block';
    else document.getElementById(divid).style.display = 'none'; 
}
