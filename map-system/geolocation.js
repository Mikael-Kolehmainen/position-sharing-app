let map = L.map('map', {zoomControl: false});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '© OpenStreetMap'
}).addTo(map);

const groupCode = new URLSearchParams(window.location.search).get('groupcode');

let goal, chat, user, waypoint, layerManagement;

document.addEventListener("DOMContentLoaded", objects);

function objects()
{
    goal = new Goal();
    chat = new Chat();
    user = new User();
    waypoint = new Waypoint();
    layerManagement = new LayerManagement();
}

function onLocationFound(e) 
{
    map.removeLayer(layerManagement.refreshedLayerGroup);

    const sendData = new Data("data/send-position.php?lat=" + e.latlng.lat + "&lng=" + e.latlng.lng + "&groupcode=" + groupCode);
    sendData.sendToPHP(function() 
    {
        const getData = new Data("data/get-data.php?groupcode=" + groupCode);
        getData.getFromPHP(function(data) {

            user.user_markers = [];
            user.usersData = data.usersdata;
            user.addMarkersToMap();

            chat.messagesData = data.messagesdata;
            chat.updateChat();
            
            goal.goalsData = data.goalsdata;
            goal.usersData = data.usersdata;
            goal.current_position = e.latlng;

            if (data.goalsdata[0] == "empty" && !goal.goalIsBeingPlanned) {
                LayerManagement.removeAndClearLayers([layerManagement.goalLayerGroup, layerManagement.draggableRouteLayerGroup, layerManagement.goalWaypointsLayerGroup]);
                ElementDisplay.change('active-goal-disclaimer', 'none');
            } else if (!goal.goalIsBeingPlanned) {
                goal.saveDataFromPHPToVariables();
                goal.drawPolyline(false);
                goal.calculatePercentagesOfRouteTravelled();
                goal.updatePercentagePopups();
                ElementDisplay.change('active-goal-disclaimer', 'block');
                ElementDisplay.change('add-goal-btn', 'none');               
            }
        });
    });

    layerManagement.refreshedLayerGroup.addTo(map);
}

function onLocationError(e) 
{
    alert(e.message);
}

map.on('locationfound', onLocationFound);
map.on('locationerror', onLocationError);

map.locate({setView: true, enableHighAccuracy: true});

setInterval("map.locate({setView: false, enableHighAccuracy: true})", 3000);