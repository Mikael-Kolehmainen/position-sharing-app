let map = L.map('map', {zoomControl: false});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '© OpenStreetMap'
}).addTo(map);

let refreshedLayerGroup = L.layerGroup();

const groupCode = new URLSearchParams(window.location.search).get('groupcode');

let usersData;

let userIcon = L.divIcon ({
    iconSize: [25, 25],
    iconAnchor: [12.5, 25],
    className: 'user-marker',
    popupAnchor: [0, -20]
});

let counter = 0;

let start_marker_arr = [];
let start_marker_pos = [];

let user_markers = [];
let userPopupContent = [];

let idsOfGoals = [];
let goal_marker_arr = [];
let goal_marker_pos = [];
let goalRouteIsDrawn = false;
let goalIsBeingPlanned = false;
let goal_waypoints = [];

let goalLayerGroup = L.layerGroup();
let draggableRouteLayerGroup = L.layerGroup();
let goalWaypointsLayerGroup = L.layerGroup();

let goal, chat, user;

document.addEventListener("DOMContentLoaded", objects);

function objects()
{
    goal = new Goal();
    chat = new Chat();
    user = new User();
}

function onLocationFound(e) 
{
    map.removeLayer(refreshedLayerGroup);

    const sendData = new Data("data/send-position.php?lat=" + e.latlng.lat + "&lng=" + e.latlng.lng + "&groupcode=" + groupCode);
    sendData.sendToPHP(function() 
    {
        const getData = new Data("data/get-data.php?groupcode=" + groupCode);
        getData.getFromPHP(function(data) {

            user_markers = [];
            user.usersData = data.usersdata;
            user.addMarkersToMap();

            chat.messagesData = data.messagesdata;
            chat.updateChat();
            
            goal.goalsData = data.goalsdata;
            goal.usersData = data.usersdata;
            goal.current_position = e.latlng;

            if (data.goalsdata[0] != "empty") {
                goal.saveDataFromPHPToVariables();
                goal.drawPolyline();
                goal.calculatePercentagesOfRouteTravelled();
                goal.updatePercentagePopups();
                ElementDisplay.change('active-goal-disclaimer', 'block');
                ElementDisplay.change('goal-btn', 'none');                
            }
        });
    });

    refreshedLayerGroup.addTo(map);
}

function onLocationError(e) 
{
    alert(e.message);
}

map.on('locationfound', onLocationFound);
map.on('locationerror', onLocationError);

function locate()
{
    if (counter == 0) {
        map.locate({setView: true, enableHighAccuracy: true});
        counter = 1;
    } else if (counter == 1) {
        map.locate({setView: false, enableHighAccuracy: true});
    }
}

locate();
setInterval(locate, 3000);