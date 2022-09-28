class Goal
{
    #STYLE_CLASS_NAME = "start-goal-style";
    #DISTANCE_BETWEEN_MARKERS = 0.002;

    constructor()
    {

    }

    showDraggableGoal()
    {
        let latlngValue = this.#DISTANCE_BETWEEN_MARKERS;

        for (let i = 0; i < idsOfGoals.length; i++) {
            goal_marker_pos[i] = new L.LatLng(current_position.lat + latlngValue, current_position.lng + latlngValue);
            start_marker_pos[i] = new L.LatLng(current_position.lat + latlngValue, current_position.lng + latlngValue + this.#DISTANCE_BETWEEN_MARKERS);
            latlngValue = latlngValue + this.#DISTANCE_BETWEEN_MARKERS;
        }
        
        this.createGoalLine(true);

        goalIsBeingPlanned = true;
    }

    createGoalLine(isDraggable = true)
    {
        const startGoalStyle = new Style(this.#STYLE_CLASS_NAME);
        startGoalStyle.removeStyle();

        map.removeLayer(goalLayerGroup);

        let classNameGoalMarkers, classNameStartMarkers, initials;
        let usersData = dataGlobal.usersdata;
        let initialsArr = [];
        let styleSheetContent = "";

        if (idsOfGoals.length == 0) {
            for (let i = 0; i < dataGlobal.goalsdata.length; i++) {
                idsOfGoals.push(dataGlobal.goalsdata[i].goal_id.goalIndex);
            }
        }

        for (let i = 0; i < idsOfGoals.length; i++) {
            initialsArr.push(usersData[idsOfGoals[i]].initials);
        }

        for (let i = 0; i < goal_marker_pos.length; i++) {
            if (goal_marker_pos[i] != "no goal") {
                // CREATE START POINTS
                start_marker_arr[i] =  new L.Marker(start_marker_pos[i], {draggable: isDraggable, icon: userIcon});
                goalLayerGroup.addLayer(start_marker_arr[i]);
                // CREATE GOALS
                goal_marker_arr[i] = new L.Marker(goal_marker_pos[i], {draggable: isDraggable, icon: userIcon});
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
                initials = '\"' + initialsArr[i] + '\"';
                styleSheetContent += '.' + classNameStartMarkers + '::before { content: ' + initials + '; }';
                styleSheetContent += '.' + classNameGoalMarkers + '::before { content: ' + initials + '; }';
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

                this.#bindPopupToUsers(i);
            }
        }

        startGoalStyle.styleSheetContent = styleSheetContent;
        startGoalStyle.createStyle();
    }

    #bindPopupToUsers(i)
    {
        if (userPopupContent.length > 0) {
            user_markers[i].bindPopup('<h3>'+userPopupContent[i]+'</h3>', {closeOnClick: false, autoClose: false, autoPan: false}).openPopup();
        }
    }

    sendDataToPHP()
    {
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

    createGoalPopup()
    {
        const popupStyle = new Style('popup-style');
        popupStyle.removeStyle();

        const usersTable = document.getElementById('users-table');

        idsOfGoals = [];

        /*
            <tr>
                <td>User</td>
                <td>Goal</td>
            </tr>
            <tr>
                <td>
                    <div class='profile'>
                        <p>MK</p>
                    </div>
                </td>
                <td><input type='checkbox' id='index-of-user'></td>
            </tr>
        */

        const titleRow = document.createElement("tr");
        const titleCell_1 = document.createElement("td");
        const titleCell_2 = document.createElement("td");
        titleCell_1.innerText = "User";
        titleCell_2.innerText = "Goal";

        usersTable.appendChild(titleRow);
        titleRow.appendChild(titleCell_1);
        titleRow.appendChild(titleCell_2);

        let popupStyleSheetContent = "";

        for (let i = 0; i < usersData.length; i++) {
            const userRow = document.createElement("tr");
            const userCell_1 = document.createElement("td");
            const userCell_2 = document.createElement("td");
            
            const userProfile = document.createElement("div");
            const initialsText = document.createElement("p");
            initialsText.innerHTML = usersData[i].initials;
            userProfile.classList.add('profile');
            
            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.id = "userindex-" + i;
            checkbox.onchange = function(){ getIdOfCheckbox(this)};

            usersTable.appendChild(userRow);
            userRow.appendChild(userCell_1);
            userRow.appendChild(userCell_2);
            userCell_1.appendChild(userProfile);
            userProfile.appendChild(initialsText);
            userCell_2.appendChild(checkbox);

            const className = 'goal-menu-user-marker-' + i;
            userProfile.classList.add(className);
            popupStyleSheetContent += '.' + className + '{ background-color: ' + usersData[i].color + '; }';
        }

        popupStyle.styleSheetContent = popupStyleSheetContent;
        popupStyle.createStyle();
    }

    clearPreviousGoalPopup()
    {
        removeChilds(document.getElementById('users-table'));
    }
}

function getIdOfCheckbox(checkbox)
{
    let idOfCheckbox = checkbox.id;
    const idSplitted = idOfCheckbox.split('-');
    idOfCheckbox = idSplitted[1];
    
    updateIdsOfGoals(idOfCheckbox);
}

function updateIdsOfGoals(idOfGoal)
{
    let indexOfId = idsOfGoals.indexOf(idOfGoal);

    if (indexOfId == -1) {
        idsOfGoals.push(idOfGoal);
    } else {
        idsOfGoals.splice(indexOfId, 1);
    }
}