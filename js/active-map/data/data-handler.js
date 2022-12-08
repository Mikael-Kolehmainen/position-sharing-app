let refreshCounter = 0;
let geolocationPermission = false;

function onLocationFound(e) 
{
    geolocationPermission = true;

    LayerManagement.removeAndClearLayers([layerManagement.refreshedLayerGroup]);
    map.removeLayer(layerManagement.refreshedLayerGroup);

    const sendData = new Data("/index.php/ajax/send-position", {lat : e.latlng.lat, lng : e.latlng.lng});
    sendData.sendToPhpAsJSON(function() 
    {
        const getData = new Data("/index.php/ajax/get-data");
        getData.getFromPhp(function(data) {
            if (data != "Group doesn't exist") {

                saveUsersData(data);
                saveGoalData(e.latlng, data);

                if (data.messagesdata != "already saved") {
                    saveChatData(data);
                }
            
                if (data.goalsdata == "empty" && !goal.goalIsBeingPlanned) {
                    LayerManagement.removeAndClearLayers([layerManagement.goalLayerGroup, layerManagement.draggableRouteLayerGroup]);
                    ElementDisplay.change('active-goal-disclaimer', 'none');
                } else if (data.goalsdata == "already saved" && !goal.goalIsBeingPlanned && refreshCounter != 0) {
                    goal.updatePercentagePopups();
                    ElementDisplay.change('active-goal-disclaimer', 'block');
                    ElementDisplay.change('add-goal-btn', 'none');
                    refreshCounter = refreshCounter + 1;
                } else if (!goal.goalIsBeingPlanned) {
                    goal.saveDataFromPHPToVariables();
                    for (let i = 0; i < goal.goalsData.length; i++) {
                        goal.addStartGoalMarkersToMap(i);
                    }
                    goal.drawAllRoutes();
                    goal.calculateTheDistancesOfRoutes();
                    goal.updatePercentagePopups();
                    ElementDisplay.change('active-goal-disclaimer', 'block');
                    ElementDisplay.change('add-goal-btn', 'none');
                    refreshCounter = refreshCounter + 1;
                }
            } else {
                redirectUserToIndexPage();
            }
        });
    });

    layerManagement.refreshedLayerGroup.addTo(map);
}

function saveUsersData(data)
{
    user.user_markers = [];
    user.usersData = data.usersdata;
    user.addMarkersToMap();
}

function saveChatData(data)
{
    chat.messagesData = data.messagesdata;
    chat.updateChat();
}

function saveGoalData(current_position, data)
{
    goal.goalsData = data.goalsdata;
    goal.usersData = data.usersdata;
    goal.current_position = current_position;
}

function redirectUserToIndexPage()
{
    alert('The group has been removed.');
    window.location.replace('/index.php');
}

function onLocationError(e) 
{
    geolocationPermission = false;
    alert(e.message);
}