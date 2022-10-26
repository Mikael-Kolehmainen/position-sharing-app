const groupCode = new URLSearchParams(window.location.search).get('groupcode');

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