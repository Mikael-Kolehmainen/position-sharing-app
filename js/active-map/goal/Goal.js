class Goal
{
    #STYLE_CLASS_NAME = "start-goal-style";
    ACTIVE_GOAL_STYLE_CLASS_NAME = "active-goal-style";

    constructor(goalsData, usersData, current_position)
    {
        this.goalsData = goalsData;
        this.usersData = usersData;
        this.current_position = current_position;

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
        this.goalIsBeingPlanned = false;

        this.outerRouteSegments = [[], []];
        this.innerRouteSegments = [];
        this.outerRouteWaypoints = [[], []];
        this.routeLoopIndex = 0;

        this.routes = [];

        this.markersOnMap = 0;
        this.goalStyleSheetContent = "";
    }

    userCanChooseStartGoalMarkerPositions()
    {
        map.on('click', this.#addMarker);
    }

    #addMarker(mouseEvent)
    {
        goal.markersOnMap = goal.markersOnMap + 1;

        switch (goal.markersOnMap) {
            case 1:
                goal.start_marker_pos[0] = mouseEvent.latlng;
                if (goal.idsOfGoals.length == 1) {
                    instructions.replace("Add outer goal marker #1");
                } else {
                    instructions.replace("Add outer start marker #2");
                }
                break;
            case 2:
                if (goal.idsOfGoals.length == 1) {
                    goal.goal_marker_pos[0] = mouseEvent.latlng;
                    map.off('click', goal.#addMarker);
                    instructions.replace("Confirm positions");
                } else {
                    goal.start_marker_pos[goal.idsOfGoals.length - 1] = mouseEvent.latlng;
                    instructions.replace("Add outer goal marker #1");
                }
                break;
            case 3:
                goal.goal_marker_pos[0] = mouseEvent.latlng;
                instructions.replace("Add outer goal marker #2");
                break;
            case 4:
                goal.goal_marker_pos[goal.idsOfGoals.length - 1] = mouseEvent.latlng;
                map.off('click', goal.#addMarker);
                goal.createTheInnerStartGoalMarkers();
                instructions.replace("Confirm positions");
                break;
            default:
                console.log("Something went wrong with adding the marker");
                break;
        }

        for (let i = 0; i < goal.start_marker_pos.length; i++) {
            goal.addStartGoalMarkersToMap(i);
        }
    }

    createTheInnerStartGoalMarkers()
    {
        for (let i = 1; i < goal.idsOfGoals.length - 1; i++) {
            let ratio = 1 / (goal.idsOfGoals.length - 1) * i;

            goal.start_marker_pos[i] = L.GeometryUtil.interpolateOnLine(map, new L.Polyline([goal.start_marker_pos[0], goal.start_marker_pos[goal.start_marker_pos.length - 1]]), ratio).latLng;
            goal.goal_marker_pos[i] = L.GeometryUtil.interpolateOnLine(map, new L.Polyline([goal.goal_marker_pos[0], goal.goal_marker_pos[goal.goal_marker_pos.length - 1]]), ratio).latLng;

            goal.addStartGoalMarkersToMap(i);
        }
    }

    addStartGoalMarkersToMap(i)
    {
        const startGoalStyle = new Style(this.#STYLE_CLASS_NAME);
        startGoalStyle.removeStyle();


        if (this.idsOfGoals.length == 0) {
            for (let j = 0; j < this.goalsData.length; j++) {
                if (this.goalsData[j] != "user has no goal") {
                    this.idsOfGoals.push(this.goalsData[j].goalOrderNumber);
                }
            }
        }

        this.#createStartGoalMarkers(i);

        this.goalStyleSheetContent += this.#createMarkerStyleSheetContent(i);

        startGoalStyle.styleSheetContent = this.goalStyleSheetContent;
        startGoalStyle.createStyle();
    }

    #createStartGoalMarkers(i)
    {
        if (typeof this.start_marker_pos[i] != "undefined") {
            this.start_marker_arr[i] = new L.Marker(this.start_marker_pos[i], {icon: this.startGoalIcon});
            layerManagement.draggableRouteLayerGroup.addLayer(this.start_marker_arr[i]);
        }

        if (typeof this.goal_marker_pos[i] != "undefined") {
            this.goal_marker_arr[i] = new L.Marker(this.goal_marker_pos[i], {icon: this.startGoalIcon});
            layerManagement.draggableRouteLayerGroup.addLayer(this.goal_marker_arr[i]);
        }

        map.addLayer(layerManagement.draggableRouteLayerGroup);
    }

    #createMarkerStyleSheetContent(i)
    {
        let classNameStartMarkers, classNameGoalMarkers, styleSheetContent = "";

        classNameStartMarkers = 'user-start-marker-' + i;
        styleSheetContent += '.' + classNameStartMarkers + '{ background-color: lightgreen; border-radius: 0 !important;}';

        classNameGoalMarkers = 'user-goal-marker-' + i;
        styleSheetContent += '.' + classNameGoalMarkers + '{ background-color: red; border-radius: 0 !important;}';

        let initials = "";

        if (typeof this.goalsData[i].fallbackinitials != "undefined") {
            initials = '\"' + this.goalsData[i].fallbackinitials + '\"';
        } else {
            initials = '\"' + this.usersData[this.idsOfGoals[i]].initials + '\"';
        }

        styleSheetContent += '.' + classNameStartMarkers + '::before { content: ' + initials + '; }';
        styleSheetContent += '.' + classNameGoalMarkers + '::before { content: ' + initials + '; }';

        if (typeof this.start_marker_arr[i] != "undefined") {
            this.start_marker_arr[i]._icon.classList.add(classNameStartMarkers);
        }

        if (typeof this.goal_marker_arr[i] != "undefined") {
            this.goal_marker_arr[i]._icon.classList.add(classNameGoalMarkers);
        }

        return styleSheetContent;
    }

    sendDataToPHP()
    {
        let goalData = [];

        for (let i = 0; i < this.goal_marker_pos.length; i++) {
            goalData.push(
                {
                    id : i,
                    goallat : this.goal_marker_pos[i].lat,
                    goallng : this.goal_marker_pos[i].lng,
                    startlat : this.start_marker_pos[i].lat,
                    startlng : this.start_marker_pos[i].lng,
                    routewaypoints : this.routes[i],
                    goalordernumber : this.idsOfGoals[i]
                }
            );
        }

        const sendGoal = new Data("/index.php/ajax/send-goal", goalData);
        sendGoal.sendToPhpAsJSON(function() {
            console.log("Successfully sent data.");
        });
    }

    saveDataFromPHPToVariables()
    {
        let i = 0;
        this.goalsData.forEach(goal => {
            this.start_marker_pos.push(new L.LatLng(goal.startPosition.latitude, goal.startPosition.longitude));

            this.routes[i] = [];

            this.routes[i].push(this.start_marker_pos[i]);

            goal.waypointPositions.forEach(waypoint => {
                this.routes[i].push(new L.LatLng(waypoint.latitude, waypoint.longitude));
            });

            this.goal_marker_pos.push(new L.LatLng(goal.goalPosition.latitude, goal.goalPosition.longitude));

            this.routes[i].push(this.goal_marker_pos[i]);

            i++;
        });
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

        if (this.idsOfGoals.length == 0 && document.getElementById('show-draggable-goal') != null) {
            document.getElementById('show-draggable-goal').id = 'show-draggable-goal-disabled';
        }
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

        if (this.idsOfGoals.length == 1) {
            document.getElementById('show-draggable-goal-disabled').id = 'show-draggable-goal';
        }

        if (this.idsOfGoals.length == 0) {
            document.getElementById('show-draggable-goal').id = 'show-draggable-goal-disabled';
        }
    }

    saveOuterRouteSegments()
    {
        for (let i = 0; i < 1; i += 0.01) {
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
                let ratio = 1 / (this.goal_marker_arr.length - 1) * i;
                this.innerRouteSegments[i-1].push(L.GeometryUtil.interpolateOnLine(map, new L.Polyline([this.outerRouteSegments[0][j], this.outerRouteSegments[1][j]]), ratio).latLng);
            }
        }
    }

    saveSegmentsAsRoutes()
    {
        for (let i = 0; i < this.goal_marker_arr.length; i++) {
            switch (i) {
                case 0:
                    this.routes[i] = this.outerRouteSegments[0];
                    break;
                case this.goal_marker_arr.length - 1:
                    this.routes[i] = this.outerRouteSegments[1];
                    break;
                default:
                    this.routes[i] = this.innerRouteSegments[i-1];
                    break;
            }
        }
    }

    drawAllRoutes()
    {
        map.addLayer(layerManagement.goalLayerGroup);

        for (let i = 0; i < this.routes.length; i++) {
            let polyline = new L.Polyline(this.routes[i], {weight: 5});

            this.#assignParentLines(polyline, i);

            layerManagement.goalLayerGroup.addLayer(polyline);
        }
    }

    #assignParentLines(parentLine, i)
    {
        this.start_marker_arr[i].parentLine = parentLine;
        this.goal_marker_arr[i].parentLine = parentLine;
    }

    removeUserDrawnRoutes()
    {
        LayerManagement.removeAndClearLayers([layerManagement.draggableRouteLayerGroup]);
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
        if (this.idsOfGoals.length == 1) {
            this.outerRouteWaypoints = [[]];
            this.outerRouteWaypoints[0].push(this.start_marker_arr[0].getLatLng());
        } else {
            this.outerRouteWaypoints[0].push(this.start_marker_arr[0].getLatLng());
            this.outerRouteWaypoints[1].push(this.start_marker_arr[this.start_marker_arr.length - 1].getLatLng());
        }

        map.addLayer(layerManagement.draggableRouteLayerGroup);

        this.#changeActiveStyleToActiveMarker(0);

        this.routeLoopIndex = 0;
        this.#addOnClickEvents();
    }

    #changeActiveStyleToActiveMarker(i)
    {
        const activeGoalStyle = new Style(goal.ACTIVE_GOAL_STYLE_CLASS_NAME);
        activeGoalStyle.removeStyle();

        let className = 'active-goal-marker-' + i;
        let styleSheetContent = '.' + className + '{ box-shadow: 0 0 5px 12px #3388ff; }';

        goal.start_marker_arr[i]._icon.classList.add(className);
        goal.goal_marker_arr[i]._icon.classList.add(className);

        activeGoalStyle.styleSheetContent = styleSheetContent;
        activeGoalStyle.createStyle();
    }

    #addOnClickEvents()
    {
        map.on('click', this.#addOuterRouteWaypoint);

        if (this.routeLoopIndex == 0) {
            this.goal_marker_arr[0].on('click', this.#attachOuterRouteToGoalMarker);
        } else {
            this.goal_marker_arr[this.goal_marker_arr.length - 1].on('click', this.#attachOuterRouteToGoalMarker);
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
            goal.outerRouteWaypoints[0].push(goal.goal_marker_arr[0].getLatLng());
            goal.goal_marker_arr[0].off('click', goal.#attachOuterRouteToGoalMarker);
        } else {
            goal.outerRouteWaypoints[1].push(goal.goal_marker_arr[goal.goal_marker_arr.length - 1].getLatLng());
            goal.goal_marker_arr[goal.goal_marker_arr.length - 1].off('click', goal.#attachOuterRouteToGoalMarker);
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

            instructions.replace("Confirm outer routes");
        }
    }

    remove()
    {
        const removeData = new Data("/index.php/ajax/remove-goal");
        removeData.sendToPhpAsJSON(function() {
            console.log("Successfully removed data.");
        });

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
        this.markersOnMap = 0;
        this.goalStyleSheetContent = "";
        map.off('click', goal.#addMarker);
        map.off('click', goal.#addOuterRouteWaypoint);

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