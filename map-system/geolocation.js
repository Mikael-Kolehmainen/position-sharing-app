const groupCode = new URLSearchParams(window.location.search).get('groupcode');
let refreshCounter = 0;

function onLocationFound(e) 
{
    map.removeLayer(layerManagement.refreshedLayerGroup);

    const sendData = new Data("data/send-position.php?lat=" + e.latlng.lat + "&lng=" + e.latlng.lng + "&groupcode=" + groupCode);
    sendData.sendToPHP(function() 
    {
        const getData = new Data("data/get-data.php?groupcode=" + groupCode);
        getData.getFromPHP(function(data) {
            if (data != "Group doesn't exist") {

                saveUsersData(data);
                saveChatData(data);
                saveGoalData(e.latlng, data);
            
                if (data.goalsdata[0] == "empty" && !goal.goalIsBeingPlanned) {
                    LayerManagement.removeAndClearLayers([layerManagement.goalLayerGroup, layerManagement.draggableRouteLayerGroup]);
                    ElementDisplay.change('active-goal-disclaimer', 'none');
                } else if (data.goalsdata[0] == "already saved" && !goal.goalIsBeingPlanned && refreshCounter != 0) {
                    goal.updatePercentagePopups();
                    ElementDisplay.change('active-goal-disclaimer', 'block');
                    ElementDisplay.change('add-goal-btn', 'none');
                    refreshCounter = refreshCounter + 1;
                } else if (!goal.goalIsBeingPlanned) {
                    goal.saveDataFromPHPToVariables();
                    for (let i = 0; i < goal.start_marker_pos.length; i++) {
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
    window.location.href = './../index.php';
}

function onLocationError(e) 
{
    alert(e.message);
}