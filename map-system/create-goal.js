// CREATE GOAL BTN ONCLICK FUNCTION
function showDraggableGoal() {
    removeStyles('js-style-goals');
    
    let latlngValue = 0.002;
    // CREATE THE POSITIONS
    for (let i = 0; i < idsOfGoals.length; i++) {
        goal_marker_pos[i] = new L.LatLng(current_position.getLatLng().lat + latlngValue, current_position.getLatLng().lng + latlngValue);
        start_marker_pos[i] = new L.LatLng(current_position.getLatLng().lat + latlngValue, current_position.getLatLng().lng + latlngValue + 0.002);
        latlngValue = latlngValue + 0.002;
    }
    styleSheetContent = createGoalLine(true);
    createStyle(styleSheetContent, 'js-style-goals');
    goalIsBeingPlanned = true;
}
// CREATE FUNCTION
function createGoalLine(returnStyleSheet = false, isDraggable = true) {
    // REMOVE & CLEAR PREVIOUS GOALLINE
    map.removeLayer(goalLayerGroup);

    let classNameGoalMarkers, classNameStartMarkers, initial;
    let usersData = dataGlobal.usersdata;
    let initialsArr = [];

    if (idsOfGoals.length == 0) {
        idsOfGoals = dataGlobal.goalsdata.goalids;
    }

    for (let i = 0; i < idsOfGoals.length; i++) {
        initialsArr.push(usersData[idsOfGoals[i]].initials);
    }

    for (let i = 0; i < goal_marker_pos.length; i++) {
        if (goal_marker_pos[i] != "no goal") {
            // CREATE START POINTS
            start_marker_arr[i] =  new L.Marker(start_marker_pos[i], {draggable: isDraggable, icon: otherUsersIcon});
            goalLayerGroup.addLayer(start_marker_arr[i]);
            // CREATE GOALS
            goal_marker_arr[i] = new L.Marker(goal_marker_pos[i], {draggable: isDraggable, icon: otherUsersIcon});
            goalLayerGroup.addLayer(goal_marker_arr[i]);
            map.addLayer(goalLayerGroup);
            // CREATE LINE BETWEEN START & GOAL (ONLY SHOW WHILE GOAL IS BEING PLANNED)
            if (isDraggable) {
                let polyline = [];
                polyline.push(new L.Polyline([start_marker_pos[i], goal_marker_pos[i]], {weight: 5, id: i}));
                draggableRouteLayerGroup.addLayer(polyline[0]);
                start_marker_arr[i].parentLine = polyline;
                goal_marker_arr[i].parentLine = polyline;
                polyline[0].on('click', addWaypointToRoute);
                map.addLayer(draggableRouteLayerGroup);
            }
            // START POINTS CSS
            classNameStartMarkers = 'user-start-marker-' + i;
            styleSheetContent += '.' + classNameStartMarkers + '{ background-color: lightgreen; border-radius: 0 !important;}';
            // GOALS CSS
            classNameGoalMarkers = 'user-goal-marker-' + i;
            styleSheetContent += '.' + classNameGoalMarkers + '{ background-color: red; border-radius: 0 !important;}';
            // INITIALS
            initial = '\"' + initialsArr[i] + '\"';
            styleSheetContent += '.' + classNameStartMarkers + '::before { content: ' + initial + '; }';
            styleSheetContent += '.' + classNameGoalMarkers + '::before { content: ' + initial + '; }';
            start_marker_arr[i]._icon.classList.add(classNameStartMarkers);
            goal_marker_arr[i]._icon.classList.add(classNameGoalMarkers);
            // ASSIGN EVENTHANDLERS TO MARKERS
            start_marker_arr[i]
                    .on('dragstart', dragStartHandler)
                    .on('drag', dragHandler)
                    .on('dragend', dragEndHandler);
            goal_marker_arr[i]
                    .on('dragstart', dragStartHandler)
                    .on('drag', dragHandler)
                    .on('dragend', dragEndHandler);

            // BIND PERCENTAGE POPUP TO USER MARKERS
            if (userPopupContent.length > 0) {
                if (user_markers[i]._mapToAdd != null) {
                    user_markers[i].bindPopup('<h3>'+userPopupContent[i]+'</h3>', {closeOnClick: false, autoClose: false, autoPan: false}).openPopup();
                } else {
                    current_position.bindPopup('<h3>'+userPopupContent[i]+'</h3>', {closeOnClick: false, autoClose: false, autoPan: false}).openPopup();
                }
            }
        }
    }
    if (returnStyleSheet) {
        return styleSheetContent;
    }
}
// SEND DATA FUNCTION
function sendGoalData() {
    let xmlhttp = new XMLHttpRequest();
    let url = 'send-goals.php?groupcode=' + groupCode;
    let startlat, startlng, goallat, goallng;
    for (let i = 0; i < goal_marker_pos.length; i++) {
        goallat = goal_marker_pos[i].lat;
        goallng = goal_marker_pos[i].lng;

        startlat = start_marker_pos[i].lat;
        startlng = start_marker_pos[i].lng;

        url += '&goallat' + i + '=' + goallat + 
                '&goallng' + i + '=' + goallng +
                '&startlat' + i + '=' + startlat + 
                '&startlng' + i + '=' + startlng + 
                '&goalid' + i + '=' + idsOfGoals[i];
    }

    // We count how many of each id there's in goalIDs
    const IDcounts = {};
    goalIDs.forEach(function (x) { IDcounts[x] = (IDcounts[x] || 0) + 1; });

    // We save the waypoint positions
    // We run the loop in reverse because I want to save the waypoints to the database in correct order
    for (let i = all_waypoints.length - 1; i >= 0; i--) {
        url += '&waypoint' + goalIDs[i] + '-' + (IDcounts[goalIDs[i]]-1) + '-lat' + '=' + all_waypoints[i].getLatLng().lat;
        url += '&waypoint' + goalIDs[i] + '-' + (IDcounts[goalIDs[i]]-1) + '-lng' + '=' + all_waypoints[i].getLatLng().lng;
        IDcounts[goalIDs[i]] = IDcounts[goalIDs[i]] - 1;
    }
    url += '&groupcode=' + groupCode + '&goalamount=' + goal_marker_pos.length;
    console.log(url);
    xmlhttp.open("GET", url, true);
    xmlhttp.onreadystatechange = function() {
        if(xmlhttp.readyState === XMLHttpRequest.DONE && xmlhttp.status === 200) {
            console.log("Successfully sent data.");
        }
    }
    xmlhttp.send();
    // HIDE CREATE GOAL BTN
    let goalBtn = document.getElementById('goal-btn');
    goalBtn.style.display = 'none';
    // REMOVE DRAGGABLE ROUTE
    map.removeLayer(draggableRouteLayerGroup);
    draggableRouteLayerGroup.eachLayer(function(layer) {draggableRouteLayerGroup.removeLayer(layer)});
    // REMOVE WAYPOINT MARKERS
    map.removeLayer(goalWaypointsLayerGroup);
    goalWaypointsLayerGroup.eachLayer(function(layer) {goalWaypointsLayerGroup.removeLayer(layer)});
    
    goalIsBeingPlanned = false;
}