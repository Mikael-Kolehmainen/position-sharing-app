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
        this.userPopupContent = [];
        this.idsOfGoals = [];
        this.startGoalIcon = L.divIcon ({
            iconSize: [25, 25],
            iconAnchor: [12.5, 25],
            popupAnchor: [0, -20],
            className: "goal-marker"
        });
        this.start_marker_arr = [];
        this.start_marker_pos = [];
        this.goal_marker_arr = [];
        this.goal_marker_pos = [];
        this.goal_waypoints = [];
        this.goalIndexes = [];
        this.goalIsBeingPlanned = false;

        this.outerRoutes = [[], []];
        this.outerRouteSegments = [[], []];
        this.outerRouteWaypoints = [[], []];
        this.routeLoopIndex = 0;
        this.indexesOfOutermostRoutes;
    }

    calculatePositionsOfStartGoalMarkers()
    {
        let latlngValue = this.#DISTANCE_BETWEEN_MARKERS;

        for (let i = 0; i < this.idsOfGoals.length; i++) {
            this.goal_marker_pos[i] = new L.LatLng(this.current_position.lat + this.#DISTANCE_BETWEEN_MARKERS, this.current_position.lng + latlngValue);
            this.start_marker_pos[i] = new L.LatLng(this.current_position.lat, this.current_position.lng + latlngValue);
            latlngValue = latlngValue + this.#DISTANCE_BETWEEN_MARKERS;
        }
    }

    drawPolyline(isDraggable)
    {
        const startGoalStyle = new Style(this.#STYLE_CLASS_NAME);
        startGoalStyle.removeStyle();

        if (this.idsOfGoals.length == 0) {
            for (let i = 0; i < this.goalsData.length; i++) {
                if (this.goalsData[i] != "user has no goal") {
                    this.idsOfGoals.push(this.goalsData[i].goal_id.goalIndex);
                }
            }
        }

        let styleSheetContent = "";

        for (let i = 0; i < this.goal_marker_pos.length; i++) {
            if (typeof this.goal_marker_pos[i] != "undefined") {
                this.#createStartGoalMarkers(i, isDraggable);

                styleSheetContent += this.#createMarkerStyleSheetContent(i);

                this.#bindPopupToUsers(i);
            }
        }

        startGoalStyle.styleSheetContent = styleSheetContent;
        startGoalStyle.createStyle();
    }

    #createStartGoalMarkers(i, isDraggable)
    {
        this.start_marker_arr[i] = new L.Marker(this.start_marker_pos[i], {draggable: isDraggable, icon: this.startGoalIcon});
        layerManagement.goalLayerGroup.addLayer(this.start_marker_arr[i]);
        
        this.goal_marker_arr[i] = new L.Marker(this.goal_marker_pos[i], {draggable: isDraggable, icon: this.startGoalIcon});
        layerManagement.goalLayerGroup.addLayer(this.goal_marker_arr[i]);
        map.addLayer(layerManagement.goalLayerGroup);
    }

    #createMarkerStyleSheetContent(i)
    {
        let initialsArr = [];

        for (let j = 0; j < this.idsOfGoals.length; j++) {
            initialsArr.push(this.usersData[this.idsOfGoals[j]].initials);
        }

        let classNameStartMarkers, classNameGoalMarkers, styleSheetContent = "";

        classNameStartMarkers = 'user-start-marker-' + i;
        styleSheetContent += '.' + classNameStartMarkers + '{ background-color: lightgreen; border-radius: 0 !important;}';

        classNameGoalMarkers = 'user-goal-marker-' + i;
        styleSheetContent += '.' + classNameGoalMarkers + '{ background-color: red; border-radius: 0 !important;}';

        let initials = '\"' + initialsArr[i] + '\"';
        styleSheetContent += '.' + classNameStartMarkers + '::before { content: ' + initials + '; }';
        styleSheetContent += '.' + classNameGoalMarkers + '::before { content: ' + initials + '; }';

        this.start_marker_arr[i]._icon.classList.add(classNameStartMarkers);
        this.goal_marker_arr[i]._icon.classList.add(classNameGoalMarkers);

        return styleSheetContent;
    }

    #bindPopupToUsers(i)
    {
        if (this.userPopupContent.length > 0) {
            user.user_markers[this.idsOfGoals[i]].bindPopup('<h3>'+this.userPopupContent[i]+'</h3>', {closeOnClick: false, autoClose: false, autoPan: false}).openPopup();
        }
    }

    sendDataToPHP()
    {
        let xmlhttp = new XMLHttpRequest();
        let url = 'goal/send-goals.php?groupcode=' + groupCode;
        let startlat, startlng, goallat, goallng;
        for (let i = 0; i < this.goal_marker_pos.length; i++) {
            goallat = this.goal_marker_pos[i].lat;
            goallng = this.goal_marker_pos[i].lng;

            startlat = this.start_marker_pos[i].lat;
            startlng = this.start_marker_pos[i].lng;

            url += '&goallat' + i + '=' + goallat + 
                    '&goallng' + i + '=' + goallng +
                    '&startlat' + i + '=' + startlat + 
                    '&startlng' + i + '=' + startlng + 
                    '&goalindex' + i + '=' + this.idsOfGoals[i];

            console.log(this.idsOfGoals[i]);
        }

        // We count how many of each id there's in this.goalIndexes
        const IDcounts = {};
        this.goalIndexes.forEach(function (x) { IDcounts[x] = (IDcounts[x] || 0) + 1; });

        let all_waypoints = waypoint.all_waypoints;
        // We run the loop in reverse because I want to save the waypoints to the database in correct order
        for (let i = all_waypoints.length - 1; i >= 0; i--) {
            url += '&waypoint' + this.goalIndexes[i] + '-' + (IDcounts[this.goalIndexes[i]]-1) + '-lat' + '=' + all_waypoints[i].getLatLng().lat;
            url += '&waypoint' + this.goalIndexes[i] + '-' + (IDcounts[this.goalIndexes[i]]-1) + '-lng' + '=' + all_waypoints[i].getLatLng().lng;
            IDcounts[this.goalIndexes[i]] = IDcounts[this.goalIndexes[i]] - 1;
        }
        url += '&groupcode=' + groupCode + '&goalamount=' + this.goal_marker_pos.length;
        xmlhttp.open("GET", url, true);
        xmlhttp.onreadystatechange = function() {
            if(xmlhttp.readyState === XMLHttpRequest.DONE && xmlhttp.status === 200) {
                console.log("Successfully sent data.");
            }
        }
        xmlhttp.send();

        LayerManagement.removeAndClearLayers([layerManagement.draggableRouteLayerGroup, layerManagement.goalWaypointsLayerGroup]);
    }

    saveDataFromPHPToVariables()
    {
        while (this.goalsData.length < this.usersData.length) {
            this.goalsData.push("user has no goal");
        }
        for (let i = 0; i < this.goalsData.length; i++) {
            if (this.goalsData[i] != "user has no goal") {
                this.start_marker_pos[i] = new L.LatLng(this.goalsData[i].start_position[0], this.goalsData[i].start_position[1]);

                this.goal_waypoints[i] = [];
                for (let j = 0; j < this.goalsData[i].waypoints.length; j++) {
                    this.goal_waypoints[i][j] = new L.marker(this.goalsData[i].waypoints[j]);
                }

                this.goal_marker_pos[i] = new L.LatLng(this.goalsData[i].goal_position[0], this.goalsData[i].goal_position[1]);
            }
        }
    }

    calculatePercentagesOfRouteTravelled()
    {
        let percentage;

        for (let i = 0; i < this.start_marker_pos.length; i++) {
            let routeLength = this.#getRouteLength(i);
            let userHasTravelledLength = this.#getUserHasTravelledLength(i);

            percentage = Math.round((userHasTravelledLength / routeLength) * 100);

            this.percentages.push(percentage);
        }
    }

    #getRouteLength(i)
    {
        let routeLength = 0;

        if (this.goal_waypoints[i].length > 1) {
            for (let j = 0; j < this.goal_waypoints[i].length; j++) {
                if (j == 0) {
                    routeLength += this.start_marker_pos[i].distanceTo(this.goal_waypoints[i][j].getLatLng());
                    routeLength += this.goal_waypoints[i][j].getLatLng().distanceTo(this.goal_waypoints[i][j+1].getLatLng());
                } else if (j == this.goal_waypoints[i].length - 1) {
                    routeLength += this.goal_marker_pos[i].distanceTo(this.goal_waypoints[i][j].getLatLng());
                } else {
                    routeLength += this.goal_waypoints[i][j].getLatLng().distanceTo(this.goal_waypoints[i][j+1].getLatLng());
                }
            }
        } else if (this.goal_waypoints[i].length == 1) {
            routeLength += this.start_marker_pos[i].distanceTo(this.goal_waypoints[i][0].getLatLng());
            routeLength += this.goal_marker_pos[i].distanceTo(this.goal_waypoints[i][0].getLatLng());
        } else {
            routeLength += this.start_marker_pos[i].distanceTo(this.goal_marker_pos[i]);
        }

        return routeLength;
    }

    #getUserHasTravelledLength(i)
    {
        let userHasTravelledLength = 0;
        if (this.goal_waypoints[i].length > 0) {
            let closestWaypointToUserIndex = this.#getClosestWaypointToUserIndexInGoalWaypoints(i);
            if (user.user_markers[this.idsOfGoals[i]].getLatLng().distanceTo(this.start_marker_pos[i]) > this.goal_waypoints[i][closestWaypointToUserIndex].getLatLng().distanceTo(user.user_markers[this.idsOfGoals[i]].getLatLng())) {
                for (let j = closestWaypointToUserIndex; j >= 0; j--) {
                    if (j == 0) {
                        userHasTravelledLength += this.goal_waypoints[i][j].getLatLng().distanceTo(this.start_marker_pos[i]);
                    } else {
                        userHasTravelledLength += this.goal_waypoints[i][j].getLatLng().distanceTo(this.goal_waypoints[i][j-1].getLatLng());
                    }
                }
            } else {
                userHasTravelledLength = -user.user_markers[this.idsOfGoals[i]].getLatLng().distanceTo(this.start_marker_pos[i]);
                
            }
        } else {
            userHasTravelledLength = -user.user_markers[this.idsOfGoals[i]].getLatLng().distanceTo(this.goal_marker_pos[i]);
        }

        return userHasTravelledLength;
    }

    #getClosestWaypointToUserIndexInGoalWaypoints(i)
    {
        let distancesToUser = [];

        for (let j = 0; j < this.goal_waypoints[i].length; j++) {
            distancesToUser.push(user.user_markers[this.idsOfGoals[i]].getLatLng().distanceTo(this.goal_waypoints[i][j].getLatLng()));
        }

        let shortestDistance = Math.min(...distancesToUser);

        return distancesToUser.indexOf(shortestDistance);
    }

    updatePercentagePopups()
    {
        this.userPopupContent = [];

        let smallestPercentage = Math.min(...this.percentages);
        for (let i = 0; i < this.percentages.length; i++) {
            this.userPopupContent[i] = this.percentages[i] + "%";
            if (smallestPercentage + 10 < this.percentages[i]) {
                this.userPopupContent[i] += "\n(Slow down)";
            }
        }

        this.percentages = [];
    }

    createPopup()
    {
        const popupStyle = new Style('popup-style');
        popupStyle.removeStyle();

        const usersTable = document.getElementById('users-table');

        this.idsOfGoals = [];

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

        this.userPopupContent = [];
        this.goal_waypoints = [];
        waypoint.all_waypoints = [];
        this.goalIndexes = [];
        this.start_marker_arr = [];
        this.start_marker_pos = [];
        this.goal_marker_arr = [];
        this.goal_marker_pos = [];

        LayerManagement.removeAndClearLayers([layerManagement.goalLayerGroup, layerManagement.draggableRouteLayerGroup, layerManagement.goalWaypointsLayerGroup]);
    }

    updateIdsOfGoals(idOfGoal)
    {
        let indexOfId = this.idsOfGoals.indexOf(idOfGoal);

        if (indexOfId == -1) {
            this.idsOfGoals.push(idOfGoal);
        } else {
            this.idsOfGoals.splice(indexOfId, 1);
        }
    }

    saveOuterRouteSegments()
    {
        for (let i = 0; i < 1; i = i + 0.01) {
            this.outerRouteSegments[0].push(L.GeometryUtil.interpolateOnLine(map, this.outerRoutes[0].getLatLngs(), i).latLng);
            this.outerRouteSegments[1].push(L.GeometryUtil.interpolateOnLine(map, this.outerRoutes[this.goalRoutes.length - 1].getLatLngs(), i).latLng);
        }
    }

    saveInnerRouteSegments()
    {
        for (let i = 0; i < this.outerRouteSegments[0].length; i++) {
            for (let j = 1; j <= this.goal_marker_arr.length - 2; j++) {
                let ratio = this.#defineRatioOfInterpolation(j);
                L.marker(L.GeometryUtil.interpolateOnLine(map, new L.Polyline([this.outerRouteSegments[0][i], this.outerRouteSegments[1][i]]), ratio).latLng).addTo(map);
            }
        }
    }

    #defineRatioOfInterpolation(i)
    {
        let increment;

        increment = 1 / (this.goal_marker_arr.length - 1);

        return increment * i;
    }

    disableMarkerDraggability()
    {
        for (let i = 0; i < this.start_marker_arr.length; i++) {
            this.start_marker_arr[i].dragging.disable();
            this.goal_marker_arr[i].dragging.disable();
        }
    }

    enableOuterRouteDrawing()
    {
        this.indexesOfOutermostRoutes = this.#getIndexesOfOutermostRoutes();

        for (let i = 0; i < this.outerRouteWaypoints.length; i++) {
            this.outerRouteWaypoints[i].push(this.start_marker_arr[this.indexesOfOutermostRoutes[i]].getLatLng());
        }

        this.routeLoopIndex = 0;
        this.#addOnClickEvents();
    }

    #addOnClickEvents()
    {
        map.on('click', this.#addOuterRouteWaypoint);

        this.goal_marker_arr[this.indexesOfOutermostRoutes[this.routeLoopIndex]].on('click', this.#attachOuterRouteToGoalMarker);
    }

    // Only works with longitude
    #getIndexesOfOutermostRoutes()
    {
        let indexes = [];
        let longitudes = [];

        for (let i = 0; i < this.start_marker_arr.length; i++) {
            longitudes.push(this.start_marker_arr[i].getLatLng().lng);
        }

        indexes.push(longitudes.indexOf(Math.min(...longitudes)));
        indexes.push(longitudes.indexOf(Math.max(...longitudes)));

        return indexes;
    }

    #addOuterRouteWaypoint(mouseEvent)
    {
        goal.outerRouteWaypoints[goal.routeLoopIndex].push(mouseEvent.latlng);
        let polyline = new L.Polyline(goal.outerRouteWaypoints[goal.routeLoopIndex], {weight: 5});
        goal.outerRoutes[goal.routeLoopIndex].push(polyline);
        layerManagement.goalLayerGroup.addLayer(polyline);
    }

    #attachOuterRouteToGoalMarker()
    {
        goal.outerRouteWaypoints[goal.routeLoopIndex].push(goal.goal_marker_arr[goal.indexesOfOutermostRoutes[goal.routeLoopIndex]].getLatLng());
        let polyline = new L.Polyline(goal.outerRouteWaypoints[goal.routeLoopIndex], {weight: 5});
        goal.outerRoutes[goal.routeLoopIndex].push(polyline);
        layerManagement.goalLayerGroup.addLayer(polyline);

        map.off('click', goal.#addOuterRouteWaypoint);

        goal.routeLoopIndex = goal.routeLoopIndex + 1;

        if (goal.routeLoopIndex < goal.indexesOfOutermostRoutes.length) {
            console.log(goal.routeLoopIndex)
            goal.#addOnClickEvents();
        }
    }
}

function getIdOfCheckbox(checkbox)
{
    let idOfCheckbox = checkbox.id;
    const idSplitted = idOfCheckbox.split('-');
    idOfCheckbox = idSplitted[1];
    
    goal.updateIdsOfGoals(idOfCheckbox);
}