url = "../../recherche/getSearchField.php?key="+key;      
                                        
$.getScript("http://localhost/jquery.ui/core.js");
$.getScript("http://localhost/jquery.ui/widget.js");  
$.getScript("http://localhost/jquery.ui/position.js");  
$.getScript("http://localhost/jquery.ui/autocomplete.js");  

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
        document.getElementById('search_zone_'+key).innerHTML = xhr.responseText;                              
    }                       
};
             
// envoi de la requ�te (asynchrone : false)      
xhr.open("GET", url, false); 
xhr.send(null);                          


function soumettre(search,key,base,table,colonne,mode,methode,visuel,limite,nomDiv,containerAll,containerResult,containerDetails){  

    search = escape(search);
    if (search.length==0){
        document.getElementById(nomDiv).innerHTML = ""; 
        return;
    }

    var url = "../../recherche/ajax.php?search="+search + "&hash="+key + "&nomBase="+base + "&nomTable="+table + "&nomColonne="+colonne
    + "&mode="+mode + "&methode="+methode + "&visuel="+visuel + "&limite="+limite 
    + "&containerAll="+containerAll + "&containerResult="+containerResult + "&containerDetails="+containerDetails;      
                 
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

            if (visuel=="result"){                                                               
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

    // envoi de la requ�te       
    xhr.open("GET", url, true); 
    xhr.send(null);  

}