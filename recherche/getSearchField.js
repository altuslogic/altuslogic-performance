url = "../../recherche/getSearchField.php?hash="+hash;      

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
        document.getElementById('search_zone_'+hash).innerHTML = xhr.responseText;                              
    }                       
};

// envoi de la requête (asynchrone : false)      
xhr.open("GET", url, false); 
xhr.send(null);                          


function soumettre(auto,fieldId,p){  

    search = escape(document.getElementById(fieldId).value);
    search = search.replace(/\+/g,"~plus~");
    if (search.length==0){
        document.getElementById(p.nomDiv).innerHTML = ""; 
        return;
    }

    if (p.visuel=="suggest" && !auto) p.visuel = "result";
    p.mode = p["mode_"+p.visuel];
    p.methode = p["methode_"+p.visuel];
    p.limite = p["limite_"+p.visuel];
    p.container = p["container_"+p.visuel];

    var param = "";
    for (key in p){
        if (!endsWith(key,"_suggest") && !endsWith(key,"_result")){
            param += "&"+key+"="+p[key];
        }
    }

    var url = "../../recherche/ajax.php?search="+search + param;
    // debug
    // alert(url);
    
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
                document.getElementById(p.nomDiv).innerHTML = result; 
            }

            else {           
                var tab = result.split('|');
                tab.pop();  
                $("#champ_"+hash).autocomplete({
                    source: tab
                });
            }                 
        }
    };

    // envoi de la requête       
    xhr.open("GET", url, true); 
    xhr.send(null);  

}

function endsWith(str, suffix) {
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
}
