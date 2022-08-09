var map = L.map('map');

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '© OpenStreetMap'
}).addTo(map);

var current_position;

function onLocationFound(e) {

    if (current_position) {
        map.removeLayer(current_position);
    }

    current_position = L.marker(e.latlng).addTo(map);
    // Få gruppkoden från sökfältet
    const groupCode = new URLSearchParams(window.location.search).get('groupcode');
    // Skicka positionsdata & gruppkoden till PHP
    let xmlhttp = new XMLHttpRequest();
    let sent = false;
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log("successfully sent data to php.");
            sent = true;
        }
    };
    if (sent == false) {
        xmlhttp.open("GET", "send-data.php?pos=" + e.latlng + "&groupcode=" + groupCode, true);
        xmlhttp.send();
    }
}

function onLocationError(e) {
    alert(e.message);
}

map.on('locationfound', onLocationFound);
map.on('locationerror', onLocationError);

function locate() {
    if (current_position) {
        map.locate({setView: true, enableHighAccuracy: true});
    } else {
        map.locate({setView: false, enableHighAccuracy: true});
    }
}

setInterval(locate, 3000);