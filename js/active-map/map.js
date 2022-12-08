let map = L.map('map', {zoomControl: false});

let options = {attribution: '© Mapbox'};

let accessToken = 'pk.eyJ1IjoibWlrYWVsLWtvbGVobWFpbmVuIiwiYSI6ImNsYmY3NmNnaDAzaWgzd282dGlxeHRhMXcifQ.0zsXMnPmmcaSQw46ADEziQ';

let baselayers = {
    "streets" : L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v12/tiles/256/{z}/{x}/{y}?access_token=' + accessToken, options),
    "outdoors" : L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/outdoors-v12/tiles/256/{z}/{x}/{y}?access_token=' + accessToken, options),
    "light" : L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/light-v11/tiles/256/{z}/{x}/{y}?access_token=' + accessToken, options),
    "dark" : L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/dark-v11/tiles/256/{z}/{x}/{y}?access_token=' + accessToken, options),
    "satellite" : L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/satellite-v9/tiles/256/{z}/{x}/{y}?access_token=' + accessToken, options),
    "satellite (streets)" : L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/satellite-streets-v12/tiles/256/{z}/{x}/{y}?access_token=' + accessToken, options),
    "navigation (day)" : L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/navigation-day-v1/tiles/256/{z}/{x}/{y}?access_token=' + accessToken, options),
    "navigation (night)" : L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/navigation-night-v1/tiles/256/{z}/{x}/{y}?access_token=' + accessToken, options)
};

let overlays = {};

L.control.layers(baselayers, overlays, {position: 'topleft'}).addTo(map);

baselayers["streets"].addTo(map);

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