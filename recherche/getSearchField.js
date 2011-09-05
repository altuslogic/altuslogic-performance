url = "../../recherche/getSearchField.php?key="+key;      
                                        
$.getScript("http://localhost/jquery.ui/core.js");
$.getScript("http://localhost/jquery.ui/widget.js");  
$.getScript("http://localhost/jquery.ui/position.js");  
$.getScript("http://localhost/jquery.ui/autocomplete.js");  

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
        document.getElementById('search_zone_'+key).innerHTML = xhr.responseText;                              
    }                       
};
             
// envoi de la requête (asynchrone : false)      
xhr.open("GET", url, false); 
xhr.send(null);                          


function soumettre(auto,fieldId,p){  
    
    search = escape(document.getElementById(fieldId).value);
    if (search.length==0){
        document.getElementById(nomDiv).innerHTML = ""; 
        return;
    }
    if (!p.auto){
        p.visuel="result";
        p.methode="tables";
    }

    var param = "";
    for (key in p){
        param += "&"+key+"="+p[key];
    }
    
    var url = "../../recherche/ajax.php?search="+search + param;
alert(url);
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

            if (p.visuel=="result"){                                                               
                document.getElementById(nomDiv).innerHTML = result; 
            }

            else {           
                var tab = result.split('|');
                tab.pop();  
                $("#champ_"+key).autocomplete({
                    source: tab
                });
            }                 
        }
    };

    // envoi de la requête       
    xhr.open("GET", url, true); 
    xhr.send(null);  

}