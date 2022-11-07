class Goal
{
    #STYLE_CLASS_NAME = "start-goal-style";
    ACTIVE_GOAL_STYLE_CLASS_NAME = "active-goal-style";

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
        this.goalIndexes = [];
        this.goalIsBeingPlanned = false;

        this.outerRouteSegments = [[], []];
        this.innerRouteSegments = [];
        this.outerRouteWaypoints = [[], []];
        this.routeLoopIndex = 0;

        this.routes = [];
        this.routesDistances = [];

        this.howManyMarkersHasUserAddedToMap = 0;
        this.goalStyleSheetContent = "";
    }

    userCanChooseStartGoalMarkerPositions()
    {
        map.on('click', this.#addMarker);
    }

    #addMarker(mouseEvent)
    {
        goal.howManyMarkersHasUserAddedToMap = goal.howManyMarkersHasUserAddedToMap + 1;

        switch (goal.howManyMarkersHasUserAddedToMap) {
            case 1:
                goal.start_marker_pos[goal.idsOfGoals[0]] = mouseEvent.latlng;
                if (goal.idsOfGoals.length == 1) {
                    instructions.instructionText = "Add outer goal marker #1";
                    instructions.replace();
                } else {
                    instructions.instructionText = "Add outer start marker #2";
                    instructions.replace();
                }
                break;
            case 2:
                if (goal.idsOfGoals.length == 1) {
                    goal.goal_marker_pos[goal.idsOfGoals[0]] = mouseEvent.latlng;
                    map.off('click', goal.#addMarker);
                    instructions.instructionText = "Confirm positions";
                    instructions.replace();
                } else {
                    goal.start_marker_pos[goal.idsOfGoals[goal.idsOfGoals.length - 1]] = mouseEvent.latlng;
                    instructions.instructionText = "Add outer goal marker #1";
                    instructions.replace();
                }
                break;
            case 3:
                goal.goal_marker_pos[goal.idsOfGoals[0]] = mouseEvent.latlng;
                instructions.instructionText = "Add outer goal marker #2";
                instructions.replace();
                break;
            case 4:
                goal.goal_marker_pos[goal.idsOfGoals[goal.idsOfGoals.length - 1]] = mouseEvent.latlng;
                map.off('click', goal.#addMarker);
                goal.createTheInnerStartGoalMarkers();
                instructions.instructionText = "Confirm positions";
                instructions.replace();
                break;
            default:
                console.log("Something went wrong with adding the marker");
                break;
        }

        for (let i = 0; i < goal.start_marker_pos.length; i++) {
            goal.drawPolyline(i);
        }
    }

    createTheInnerStartGoalMarkers()
    {
        for (let i = 1; i < goal.idsOfGoals.length - 1; i++) {
            let ratio = 1 / (goal.idsOfGoals.length - 1) * i;

            goal.start_marker_pos[goal.idsOfGoals[i]] = L.GeometryUtil.interpolateOnLine(map, new L.Polyline([goal.start_marker_pos[goal.idsOfGoals[0]], goal.start_marker_pos[goal.idsOfGoals[goal.idsOfGoals.length - 1]]]), ratio).latLng;
            goal.goal_marker_pos[goal.idsOfGoals[i]] = L.GeometryUtil.interpolateOnLine(map, new L.Polyline([goal.goal_marker_pos[goal.idsOfGoals[0]], goal.goal_marker_pos[goal.idsOfGoals[goal.idsOfGoals.length - 1]]]), ratio).latLng;

            goal.drawPolyline(i);
        }
    }

    drawPolyline(i)
    {
        const startGoalStyle = new Style(this.#STYLE_CLASS_NAME);
        startGoalStyle.removeStyle();

        if (this.idsOfGoals.length == 0) {
            for (let j = 0; j < this.goalsData.length; j++) {
                if (this.goalsData[j] != "user has no goal") {
                    this.idsOfGoals.push(this.goalsData[j].goalIndex);
                }
            }
        }

        if (typeof this.start_marker_pos[this.idsOfGoals[i]] != "undefined") {
            this.#createStartGoalMarkers(i);

            this.goalStyleSheetContent += this.#createMarkerStyleSheetContent(i);

            this.#bindPopupToUsers(i);
        }

        startGoalStyle.styleSheetContent = this.goalStyleSheetContent;
        startGoalStyle.createStyle();
    }

    #createStartGoalMarkers(i)
    {
        if (typeof this.start_marker_pos[this.idsOfGoals[i]] != "undefined") {
            this.start_marker_arr[this.idsOfGoals[i]] = new L.Marker(this.start_marker_pos[this.idsOfGoals[i]], {icon: this.startGoalIcon});
            layerManagement.draggableRouteLayerGroup.addLayer(this.start_marker_arr[this.idsOfGoals[i]]);
        }
    
        if (typeof this.goal_marker_pos[this.idsOfGoals[i]] != "undefined") {
            this.goal_marker_arr[this.idsOfGoals[i]] = new L.Marker(this.goal_marker_pos[this.idsOfGoals[i]], {icon: this.startGoalIcon});
            layerManagement.draggableRouteLayerGroup.addLayer(this.goal_marker_arr[this.idsOfGoals[i]]);
        }

        map.addLayer(layerManagement.draggableRouteLayerGroup);
    }

    #createMarkerStyleSheetContent(i)
    {
        let classNameStartMarkers, classNameGoalMarkers, styleSheetContent = "";

        classNameStartMarkers = 'user-start-marker-' + this.idsOfGoals[i];
        styleSheetContent += '.' + classNameStartMarkers + '{ background-color: lightgreen; border-radius: 0 !important;}';

        classNameGoalMarkers = 'user-goal-marker-' + this.idsOfGoals[i];
        styleSheetContent += '.' + classNameGoalMarkers + '{ background-color: red; border-radius: 0 !important;}';

        let initials = '\"' + this.usersData[this.idsOfGoals[i]].initials + '\"';
        styleSheetContent += '.' + classNameStartMarkers + '::before { content: ' + initials + '; }';
        styleSheetContent += '.' + classNameGoalMarkers + '::before { content: ' + initials + '; }';

        if (typeof this.start_marker_arr[this.idsOfGoals[i]] != "undefined") {
            this.start_marker_arr[this.idsOfGoals[i]]._icon.classList.add(classNameStartMarkers);
        }

        if (typeof this.goal_marker_arr[this.idsOfGoals[i]] != "undefined") {
            this.goal_marker_arr[this.idsOfGoals[i]]._icon.classList.add(classNameGoalMarkers);
        }

        return styleSheetContent;
    }

    #bindPopupToUsers(i)
    {
        if (this.userPopupContent.length > 0) {
            user.user_markers[this.idsOfGoals[i]].bindPopup('<h3>'+this.userPopupContent[i]+'</h3>', {closeOnClick: false, autoClose: false, autoPan: false}).openPopup();
        }
    }

    updatePercentagePopups()
    {
        for (let i = 0; i < goal.idsOfGoals.length; i++) {
            let distanceFromUserToGoal = user.user_markers[goal.idsOfGoals[i]].getLatLng().distanceTo(goal.goal_marker_arr[goal.idsOfGoals[i]].getLatLng());
            let percentageOfGoalAchieved = Math.round((1 - distanceFromUserToGoal / this.routesDistances[i]) * 100);

            this.userPopupContent[i] = percentageOfGoalAchieved + "%";
            this.#bindPopupToUsers(i);
        }
    }

    sendDataToPHP()
    {
        let xmlhttp = new XMLHttpRequest();
        const url = 'goal/send-goals.php';

        let postObj = [];
        let goallat, goallng, startlat, startlng, route, goalindex;

        for (let i = 0; i < this.goal_marker_pos.length; i++) {
            if (typeof this.start_marker_pos[this.idsOfGoals[i]] != "undefined") {
                goallat = this.goal_marker_pos[this.idsOfGoals[i]].lat;
                goallng = this.goal_marker_pos[this.idsOfGoals[i]].lng;

                startlat = this.start_marker_pos[this.idsOfGoals[i]].lat;
                startlng = this.start_marker_pos[this.idsOfGoals[i]].lng;

                goalindex = this.idsOfGoals[i];

                route = this.routes[this.idsOfGoals[i]];

                postObj.push({id : i, goallat : goallat, goallng : goallng, startlat : startlat, startlng : startlng, routewaypoints : route, goalindex : goalindex, groupcode : groupCode});
            }
        }

        let post = JSON.stringify(postObj);

        xmlhttp.open('POST', url, true);
        xmlhttp.setRequestHeader('Content-type', 'application/JSON');
        xmlhttp.send(post);

        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log("Successfully sent data.");
            }
        };
    }

    saveDataFromPHPToVariables()
    {
        while (this.goalsData.length < this.usersData.length) {
            this.goalsData.push("user has no goal");
        }
        for (let i = 0; i < this.goalsData.length; i++) {
            if (this.goalsData[i] != "user has no goal") {
                this.start_marker_pos[this.goalsData[i].goalIndex] = new L.LatLng(this.goalsData[i].start_position[0], this.goalsData[i].start_position[1]);
                
                this.routes[this.goalsData[i].goalIndex] = [];

                this.routes[this.goalsData[i].goalIndex][0] = this.start_marker_pos[this.goalsData[i].goalIndex];

                for (let j = 0; j < this.goalsData[i].waypoints.length; j++) {
                    this.routes[this.goalsData[i].goalIndex][j+1] = new L.LatLng(this.goalsData[i].waypoints[j][0], this.goalsData[i].waypoints[j][1]);
                }

                this.goal_marker_pos[this.goalsData[i].goalIndex] = new L.LatLng(this.goalsData[i].goal_position[0], this.goalsData[i].goal_position[1]);

                this.routes[this.goalsData[i].goalIndex][this.routes[this.goalsData[i].goalIndex].length - 1] = this.goal_marker_pos[this.goalsData[i].goalIndex];
            }
        }
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
            for (let j = 0; j < this.outerRouteWaypoints.length; j++) {
                this.outerRouteSegments[j].push(L.GeometryUtil.interpolateOnLine(map, this.outerRouteWaypoints[j], i).latLng);
            }
        }
    }

    saveInnerRouteSegments()
    {
        for (let i = 1; i <= this.goal_marker_arr.length - 2; i++) {
            this.innerRouteSegments.push([]);
            for (let j = 0; j < this.outerRouteSegments[0].length; j++) {
                let ratio = this.#defineRatioOfInterpolation(i);
                this.innerRouteSegments[i-1].push(L.GeometryUtil.interpolateOnLine(map, new L.Polyline([this.outerRouteSegments[0][j], this.outerRouteSegments[1][j]]), ratio).latLng);
            }
        }
    }

    saveSegmentsAsRoutes()
    {
        for (let i = 0; i < this.goal_marker_arr.length; i++) {
            if (i == 0) {
                this.routes.push(this.outerRouteSegments[0]);
            } else if (i == this.goal_marker_arr.length - 1) {
                this.routes.push(this.outerRouteSegments[1]);
            } else {
                this.routes.push(this.innerRouteSegments[i-1]);
            }
        }
    }

    #defineRatioOfInterpolation(i)
    {
        let increment;

        increment = 1 / (this.goal_marker_arr.length - 1);

        return increment * i;
    }

    drawAllRoutes()
    {
        map.addLayer(layerManagement.goalLayerGroup);

        for (let i = 0; i < this.routes.length; i++) {
            if (typeof this.routes[i] != "undefined"
                && typeof this.start_marker_arr[this.idsOfGoals[i]] != "undefined") {
                let polyline = new L.Polyline(this.routes[i], {weight: 5});

                this.#assignParentLines(polyline, i);

                layerManagement.goalLayerGroup.addLayer(polyline);
            }       
        }
    }

    #assignParentLines(parentLine, i)
    {
        this.start_marker_arr[this.idsOfGoals[i]].parentLine = parentLine;
        this.goal_marker_arr[this.idsOfGoals[i]].parentLine = parentLine;
    }

    calculateTheDistancesOfRoutes()
    {
        let routeDistance = 0;

        for (let i = 0; i < this.routes.length; i++) {
            if (typeof this.routes[i] != "undefined") {
                for (let j = 0; j < this.routes[i].length - 1; j++) {
                    routeDistance += this.routes[i][j].distanceTo(this.routes[i][j+1]);
                }
                
                this.routesDistances.push(routeDistance);
                routeDistance = 0;
            }
        }
    }

    removeUserDrawnRoutes()
    {
        LayerManagement.removeAndClearLayers([layerManagement.draggableRouteLayerGroup]);
    }

    disableMarkerDraggability()
    {
        for (let i = 0; i < this.start_marker_arr.length; i++) {
            if (typeof this.start_marker_arr[this.idsOfGoals[i]] != "undefined") {
                this.start_marker_arr[this.idsOfGoals[i]].dragging.disable();
                this.goal_marker_arr[this.idsOfGoals[i]].dragging.disable();
            }
        }
    }

    enableOuterRouteDrawing()
    {
        console.log(this.idsOfGoals);
        if (this.idsOfGoals.length == 1) {
            this.outerRouteWaypoints = [[]];
            this.outerRouteWaypoints[0].push(this.start_marker_arr[this.idsOfGoals[0]].getLatLng());
        } else {
            this.outerRouteWaypoints[0].push(this.start_marker_arr[this.idsOfGoals[0]].getLatLng());
            this.outerRouteWaypoints[1].push(this.start_marker_arr[this.idsOfGoals[this.idsOfGoals.length - 1]].getLatLng());   
        }

        map.addLayer(layerManagement.draggableRouteLayerGroup);

        this.#changeActiveStyleToActiveMarker(0);

        this.routeLoopIndex = 0;
        this.#addOnClickEvents();
    }

    #changeActiveStyleToActiveMarker(i)
    {
        if (typeof goal.start_marker_arr[goal.idsOfGoals[i]] != "undefined") {
            const activeGoalStyle = new Style(goal.ACTIVE_GOAL_STYLE_CLASS_NAME);
            activeGoalStyle.removeStyle();

            let className = 'active-goal-marker-' + goal.idsOfGoals[i];
            let styleSheetContent = '.' + className + '{ box-shadow: 0 0 5px 12px #3388ff; }';

            goal.start_marker_arr[goal.idsOfGoals[i]]._icon.classList.add(className);
            goal.goal_marker_arr[goal.idsOfGoals[i]]._icon.classList.add(className);

            activeGoalStyle.styleSheetContent = styleSheetContent;
            activeGoalStyle.createStyle();
        }
    }

    #addOnClickEvents()
    {
        map.on('click', this.#addOuterRouteWaypoint);

        if (this.routeLoopIndex == 0) {
            this.goal_marker_arr[this.idsOfGoals[0]].on('click', this.#attachOuterRouteToGoalMarker);
        } else {
            this.goal_marker_arr[this.idsOfGoals[this.idsOfGoals.length - 1]].on('click', this.#attachOuterRouteToGoalMarker);
        }
    }

    #addOuterRouteWaypoint(mouseEvent)
    {
        goal.outerRouteWaypoints[goal.routeLoopIndex].push(mouseEvent.latlng);
        let polyline = new L.Polyline(goal.outerRouteWaypoints[goal.routeLoopIndex], {weight: 5});
        layerManagement.draggableRouteLayerGroup.addLayer(polyline);
    }

    #attachOuterRouteToGoalMarker()
    {
        if (goal.routeLoopIndex == 0) {
            goal.outerRouteWaypoints[0].push(goal.goal_marker_arr[goal.idsOfGoals[0]].getLatLng());
            goal.goal_marker_arr[goal.idsOfGoals[0]].off('click', goal.#attachOuterRouteToGoalMarker);
        } else {
            goal.outerRouteWaypoints[1].push(goal.goal_marker_arr[goal.idsOfGoals[goal.idsOfGoals.length - 1]].getLatLng());
            goal.goal_marker_arr[goal.idsOfGoals[goal.idsOfGoals.length - 1]].off('click', goal.#attachOuterRouteToGoalMarker);
        }

        let polyline = new L.Polyline(goal.outerRouteWaypoints[goal.routeLoopIndex], {weight: 5});
        layerManagement.draggableRouteLayerGroup.addLayer(polyline);

        map.off('click', goal.#addOuterRouteWaypoint);

        goal.routeLoopIndex = goal.routeLoopIndex + 1;

        if (goal.routeLoopIndex < goal.outerRouteWaypoints.length) {
            goal.#changeActiveStyleToActiveMarker(goal.idsOfGoals.length - 1);
            goal.#addOnClickEvents();
        } else {
            const activeGoalStyle = new Style(goal.ACTIVE_GOAL_STYLE_CLASS_NAME);
            activeGoalStyle.removeStyle();

            instructions.instructionText = "Confirm outer routes";
            instructions.replace();
        }
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
        this.goalIndexes = [];
        this.start_marker_arr = [];
        this.start_marker_pos = [];
        this.goal_marker_arr = [];
        this.goal_marker_pos = [];
        this.outerRouteSegments = [[], []];
        this.innerRouteSegments = [];
        this.outerRouteWaypoints = [[], []];
        this.routeLoopIndex = 0;
        this.goalsData = [];
        this.routes = [];
        this.howManyMarkersHasUserAddedToMap = 0;
        this.goalStyleSheetContent = "";

        LayerManagement.removeAndClearLayers([layerManagement.goalLayerGroup, layerManagement.draggableRouteLayerGroup]);
    }
}

function getIdOfCheckbox(checkbox)
{
    let idOfCheckbox = checkbox.id;
    const idSplitted = idOfCheckbox.split('-');
    idOfCheckbox = idSplitted[1];
    
    goal.updateIdsOfGoals(idOfCheckbox);
}