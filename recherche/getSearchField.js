url = "../../recherche/getSearchField.php?hash="+hash;      

var path=location.pathname.split("crawl/");
var js_path=location.hostname+":"+location.port +path[0]+"";


// chargement de l'autocomplete (� faire en synchrone)
$.ajaxSetup({async: false});
$.getScript("http://"+js_path+"jquery.ui/core.js");
$.getScript("http://"+js_path+"jquery.ui/widget.js");  
$.getScript("http://"+js_path+"jquery.ui/position.js");  
$.getScript("http://"+js_path+"jquery.ui/autocomplete.js");
$.ajaxSetup({async: true});  

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
        document.getElementById('search_zone_'+hash).innerHTML = xhr.responseText;                              
    }                       
};

// envoi de la requ�te (asynchrone : false)      
xhr.open("GET", url, false); 
xhr.send(null);                          


function soumettre(re,source,page,fieldId,p){

    search = escape(document.getElementById(fieldId).value);
    search = search.replace(/\+/g,"~plus~");
    if (search.length==0){
        document.getElementById(p.nomDiv).innerHTML = ""; 
        return;
    }

    if (!re){
        if (p.visuel=="suggest" && !source) p.visuel = "result";
        if (p.visuel=="result" && !page) page = 1;
        p.mode = p["mode_"+p.visuel];
        p.methode = p["methode_"+p.visuel];
        p.limite = p["limite_"+p.visuel];
        p.container = p["container_"+p.visuel];
    } 

    var param = "";
    for (key in p){
        if (!endsWith(key,"_suggest") && !endsWith(key,"_result")){
            param += "&"+key+"="+p[key];
        }
    }

    var url = "../../recherche/ajax.php?search="+search + "&source="+source + "&page="+page + param;
    // debug
    // alert(url);

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
                    source: tab,
                    onclickSearch: p.onclickSearch
                });                       
            }                 
        }
    };

    // envoi de la requ�te       
    xhr.open("GET", url, true); 
    xhr.send(null);  

}

function endsWith(str, suffix) { 
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
}

document.getElementById('ajax').innerHTML=js_path;