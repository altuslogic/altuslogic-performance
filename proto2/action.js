function soumettre(){ 
    var search = escape(document.getElementById('search').value);
    if (search.length==0) return;                          
    var mode = document.getElementById('mode').value; 
    var lat = document.getElementById('latitude').value;
    var lon = document.getElementById('longitude').value;

    var url = "ajax.php?search=" + search + "&mode=" + mode + "&lat=" + lat + "&lon=" + lon;

    // création d'un objet capable d'interagir avec le serveur
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

    // attente du résultat
    xhr.onreadystatechange = function(){
        // instructions de traitement de la réponse  
        if (xhr.readyState == 4) {

            clearMarkers();

            var print = "<h2>Résultats de la recherche</h2>";
            var result = xhr.responseText;
            var tab = result.split('|');

            if (tab.length==1) print += "Pas de résultats.<br>";
            else {
                for (var i=0; i<tab.length-1; i++){
                    var t = tab[i].split(',');
                    print += t[0]+" ("+t[1]+" km)<br>";
                    createMarker(i,t[0],t[2],t[3]);
                }
                adaptMap();
            }
            print += "<br>Temps écoulé : "+tab[tab.length-1]+" seconde(s)";

            document.getElementById('ajax').innerHTML = print; 

        }
        else { 
            //document.getElementById('ajax').innerHTML = "Erreur : " + url;    
        }   
    };

    // envoi de la requête
    xhr.open("GET", url, true); 
    xhr.send(null); 
}

function initCoord(){

    if (google.loader.ClientLocation){
        coord = locate();       
    }

    map = new google.maps.Map(document.getElementById("map_canvas"), getOptions(coord));

    position = new google.maps.Marker({
        position: coord,
        map: map, 
        title: "Votre position",
        icon: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=O|009900|000000"  
    }); 
}

function lireCoord(){
    var lat = document.getElementById('latitude').value;
    var lon = document.getElementById('longitude').value; 
    return new google.maps.LatLng(lat,lon); 
}

function gotoCoord(){                                 
    setCoord(lireCoord());
}

function setCoord(coord){                                 
    map.setOptions(getOptions(coord));
    position.setPosition(coord);
}

function getOptions(coord){                  
    var options = {
        zoom: 6,
        center: coord,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    return options;                  
}

function resetCoord(){
    if (google.loader.ClientLocation){
        setCoord(locate());        
    }
}

function locate(){            
    lat = google.loader.ClientLocation.latitude;
    lon = google.loader.ClientLocation.longitude;
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lon;
    return new google.maps.LatLng(lat,lon);       
}

var markArray = [];

function createMarker(i, nom, lat, lon){     
    var lettre = String.fromCharCode(i+65);
    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(lat,lon),
        map: map, 
        title: nom,
        icon: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld="+lettre+"|0099FF|000000" 
    });
    markArray.push(marker); 
}

function clearMarkers(){
    while (markArray.length>0){
        markArray.pop().setMap(null);
    }
}

function adaptMap(){
    var bounds = new google.maps.LatLngBounds();
    for (var i in markArray){
        bounds.extend(markArray[i].getPosition());
    }
    map.setCenter(bounds.getCenter());
    map.fitBounds(bounds);
}