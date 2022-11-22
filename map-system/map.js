let map = L.map('map', {zoomControl: false});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '© OpenStreetMap'
}).addTo(map);

L.control.zoom({
    position: 'topright'
}).addTo(map);

map.on('locationfound', onLocationFound);
map.on('locationerror', onLocationError);

map.locate({setView: true, enableHighAccuracy: true});

setInterval(locate, 1000);

function locate()
{
    if (geolocationPermission) {
        map.locate({setView: false, enableHighAccuracy: true});
    }
}