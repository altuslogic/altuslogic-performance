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