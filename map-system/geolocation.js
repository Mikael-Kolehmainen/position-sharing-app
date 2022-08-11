var map = L.map('map', {zoomControl: false});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '© OpenStreetMap'
}).addTo(map);

var current_position;
var counter = 0;

var userIcon = L.divIcon ({
    iconSize: [25, 25],
    iconAnchor: [12.5, 12.5],
    className: 'user-marker'
});

function onLocationFound(e) {

    if (current_position) {
        map.eachLayer(function (layer) {
            if (layer.options.attribution == null) {
                map.removeLayer(layer);
            }
        });
    }

    current_position = L.marker(e.latlng, {icon: userIcon}).addTo(map);
    // Få gruppkoden från sökfältet
    const groupCode = new URLSearchParams(window.location.search).get('groupcode');
    // Skicka positionsdata & gruppkoden till PHP
    var index = ['send-data', 'get-data']
    var xmlhttp = new XMLHttpRequest();
    (function loop(i, length) {
        if (i>= length) {
            return;
        }
        var url = index[i] + ".php?pos=" + e.latlng + "&groupcode=" + groupCode;

        if (i == 1) {
            xmlhttp.onload = function() {
                var data = JSON.parse(this.responseText);
                var positionsArr = data.positions;
                var initialsArr = data.initials;
                var colorsArr = data.colors;
                var otherUsersIcon;
                for (var i = 0; i < positionsArr.length; i++) {
                    otherUsersIcon = L.divIcon ({
                        iconSize: [25, 25],
                        iconAnchor: [12.5, 12.5],
                        className: 'other-user-marker'
                    });
                    positionsArr[i] = positionsArr[i].replace(/[^\d.,-]/g,'');
                    latlngArr = positionsArr[i].split(",");
                    marker = L.marker(L.latLng(latlngArr[0], latlngArr[1]), {icon: otherUsersIcon}).addTo(map);
                    // Tar bort användarens egna markör som redan är på kartan & sätter färg och initialer på användarens egna markör
                    if (marker.getLatLng().equals(current_position.getLatLng())) {
                        map.removeLayer(marker);
                        // GE FÄRG & INITIALERNA ÅT ANVÄNDARENS MARKÖR
                        const stylesheet = document.styleSheets[0];
                        var initial = '\"' + initialsArr[i] + '\"';
                        stylesheet.cssRules[1].style.setProperty('content', initial);
                        stylesheet.cssRules[0].style.setProperty('background-color', colorsArr[i]);
                    }
                    // GE FÄRG & INITIALER ÅT DE ANDRA MARKÖRERNA

                }
            };
        }

        xmlhttp.open("GET", url, true);
        xmlhttp.onreadystatechange = function() {
            if(xmlhttp.readyState === XMLHttpRequest.DONE && xmlhttp.status === 200) {
                loop(i + 1, length);
            }
        }
        xmlhttp.send();
    })(0, index.length);
}

function onLocationError(e) {
    alert(e.message);
}

map.on('locationfound', onLocationFound);
map.on('locationerror', onLocationError);

function locate() {
    if (counter == 0) {
        map.locate({setView: true, enableHighAccuracy: true});
        counter = 1;
    } else if (counter == 1) {
        map.locate({setView: false, enableHighAccuracy: true});
    }
}

setInterval(locate, 3000);