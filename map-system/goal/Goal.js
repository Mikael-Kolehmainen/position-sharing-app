class Goal
{
    #STYLE_CLASS_NAME = "start-goal-style";
    #DISTANCE_BETWEEN_MARKERS = 0.002;

    constructor(goalsData, usersData, current_position)
    {
        this.goalsData = goalsData;
        this.usersData = usersData;
        this.current_position = current_position;

        this.percentages = [];
    }

    calculatePositionsOfStartGoalMarkers()
    {
        let latlngValue = this.#DISTANCE_BETWEEN_MARKERS;

        for (let i = 0; i < idsOfGoals.length; i++) {
            goal_marker_pos[i] = new L.LatLng(this.current_position.lat + latlngValue, this.current_position.lng + latlngValue);
            start_marker_pos[i] = new L.LatLng(this.current_position.lat + latlngValue, this.current_position.lng + latlngValue + this.#DISTANCE_BETWEEN_MARKERS);
            latlngValue = latlngValue + this.#DISTANCE_BETWEEN_MARKERS;
        }
    }

    drawPolyline(isDraggable)
    {
        const startGoalStyle = new Style(this.#STYLE_CLASS_NAME);
        startGoalStyle.removeStyle();

        if (idsOfGoals.length == 0) {
            for (let i = 0; i < this.goalsData.length; i++) {
                if (this.goalsData[i] != "user has no goal") {
                    idsOfGoals.push(this.goalsData[i].goal_id.goalIndex);
                }
            }
        }

        let styleSheetContent = "";
        let latlngs = [];

        for (let i = 0; i < goal_marker_pos.length; i++) {
            if (typeof goal_marker_pos[i] != "undefined") {
                this.#createStartGoalMarkers(i, isDraggable);
                latlngs.push(start_marker_pos[i]);
                if (typeof goal_waypoints[i] != "undefined") {
                    for (let j = 0; j < goal_waypoints[i].length; j++) {
                        latlngs.push(goal_waypoints[i][j].getLatLng());
                    }
                }
                latlngs.push(goal_marker_pos[i]);
                let polyline = [new L.Polyline(latlngs, {weight: 5, id: i})];
                if (isDraggable) {
                    draggableRouteLayerGroup.addLayer(polyline[0]);
                    start_marker_arr[i].parentLine = polyline;
                    goal_marker_arr[i].parentLine = polyline;
                    polyline[0].on('click', addWaypointToRoute);
                    map.addLayer(draggableRouteLayerGroup);

                    start_marker_arr[i]
                        .on('dragstart', dragStartHandler)
                        .on('drag', dragHandler)
                        .on('dragend', dragEndHandler);
                    goal_marker_arr[i]
                        .on('dragstart', dragStartHandler)
                        .on('drag', dragHandler)
                        .on('dragend', dragEndHandler);
                }

                goalLayerGroup.addLayer(polyline[0]);

                styleSheetContent += this.#createMarkerStyleSheetContent(i);

                this.#bindPopupToUsers(i);

                latlngs = [];
            }
        }

        startGoalStyle.styleSheetContent = styleSheetContent;
        startGoalStyle.createStyle();
    }

    #createStartGoalMarkers(i, isDraggable)
    {
        start_marker_arr[i] = new L.Marker(start_marker_pos[i], {draggable: isDraggable, icon: userIcon});
        goalLayerGroup.addLayer(start_marker_arr[i]);
        
        goal_marker_arr[i] = new L.Marker(goal_marker_pos[i], {draggable: isDraggable, icon: userIcon});
        goalLayerGroup.addLayer(goal_marker_arr[i]);
        map.addLayer(goalLayerGroup);
    }

    #createMarkerStyleSheetContent(i)
    {
        let initialsArr = [];

        for (let j = 0; j < idsOfGoals.length; j++) {
            initialsArr.push(this.usersData[idsOfGoals[j]].initials);
        }

        let classNameStartMarkers, classNameGoalMarkers, styleSheetContent = "";

        classNameStartMarkers = 'user-start-marker-' + i;
        styleSheetContent += '.' + classNameStartMarkers + '{ background-color: lightgreen; border-radius: 0 !important;}';

        classNameGoalMarkers = 'user-goal-marker-' + i;
        styleSheetContent += '.' + classNameGoalMarkers + '{ background-color: red; border-radius: 0 !important;}';

        let initials = '\"' + initialsArr[i] + '\"';
        styleSheetContent += '.' + classNameStartMarkers + '::before { content: ' + initials + '; }';
        styleSheetContent += '.' + classNameGoalMarkers + '::before { content: ' + initials + '; }';

        start_marker_arr[i]._icon.classList.add(classNameStartMarkers);
        goal_marker_arr[i]._icon.classList.add(classNameGoalMarkers);

        return styleSheetContent;
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
        let url = 'goal/send-goals.php?groupcode=' + groupCode;
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
        // REMOVE DRAGGABLE ROUTE
        map.removeLayer(draggableRouteLayerGroup);
        draggableRouteLayerGroup.eachLayer(function(layer) {draggableRouteLayerGroup.removeLayer(layer)});
        // REMOVE WAYPOINT MARKERS
        map.removeLayer(goalWaypointsLayerGroup);
        goalWaypointsLayerGroup.eachLayer(function(layer) {goalWaypointsLayerGroup.removeLayer(layer)});
    }

    saveDataFromPHPToVariables()
    {
        while (this.goalsData.length < this.usersData.length) {
            this.goalsData.push("user has no goal");
        }
        for (let i = 0; i < this.goalsData.length; i++) {
            if (this.goalsData[i] != "user has no goal") {
                start_marker_pos[i] = new L.LatLng(this.goalsData[i].start_position[0], this.goalsData[i].start_position[1]);

                goal_waypoints[i] = [];
                for (let j = 0; j < this.goalsData[i].waypoints.length; j++) {
                    goal_waypoints[i][j] = new L.marker(this.goalsData[i].waypoints[j]);
                }

                goal_marker_pos[i] = new L.LatLng(this.goalsData[i].goal_position[0], this.goalsData[i].goal_position[1]);
            }
        }
    }

    calculatePercentagesOfRouteTravelled()
    {
        let percentage;

        for (let i = 0; i < start_marker_pos.length; i++) {
            percentage = Math.round((1 - user_markers[i].getLatLng().distanceTo(goal_marker_pos[i]) / start_marker_pos[i].distanceTo(goal_marker_pos[i])) * 100);

            this.percentages.push(percentage);
        }
    }

    updatePercentagePopups()
    {
        userPopupContent = [];

        let smallestPercentage = Math.min(...this.percentages);
        for (let i = 0; i < this.percentages.length; i++) {
            userPopupContent[i] = this.percentages[i] + "%";
            if (smallestPercentage + 10 < this.percentages[i]) {
                userPopupContent[i] += "\n(Slow down)";
            }
        }
    }

    createPopup()
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

        for (let i = 0; i < this.usersData.length; i++) {
            const userRow = document.createElement("tr");
            const userCell_1 = document.createElement("td");
            const userCell_2 = document.createElement("td");
            
            const userProfile = document.createElement("div");
            const initialsText = document.createElement("p");
            initialsText.innerHTML = this.usersData[i].initials;
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
            popupStyleSheetContent += '.' + className + '{ background-color: ' + this.usersData[i].color + '; }';
        }

        popupStyle.styleSheetContent = popupStyleSheetContent;
        popupStyle.createStyle();
    }

    clearPreviousPopup()
    {
        removeChilds(document.getElementById('users-table'));
    }

    remove()
    {
        let xmlhttp = new XMLHttpRequest();
        let url = 'goal/remove-goal.php?groupcode=' + groupCode;
        
        xmlhttp.open("GET", url, true);
        xmlhttp.onreadystatechange = function() {
            if(xmlhttp.readyState === XMLHttpRequest.DONE && xmlhttp.status === 200) {
                console.log("Successfully removed data.");
            }
        }
        xmlhttp.send();

        userPopupContent = [];
        goal_waypoints = [];
        all_waypoints = [];
        goalIDs = [];
        start_marker_arr = [];
        start_marker_pos = [];
        goal_marker_arr = [];
        goal_marker_pos = [];

        this.clearLayers();
    }

    clearLayers()
    {
        LayerManagement.removeAndClearLayers([goalLayerGroup, draggableRouteLayerGroup, goalWaypointsLayerGroup]);
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