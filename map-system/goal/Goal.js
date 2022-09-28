class Goal
{
    #STYLE_CLASS_NAME = "goals-style";
    #DISTANCE_BETWEEN_MARKERS = 0.002;

    constructor()
    {

    }

    showDraggableGoal()
    {
        const style = new Style(this.#STYLE_CLASS_NAME);
        style.removeStyle();
    
        let latlngValue = this.#DISTANCE_BETWEEN_MARKERS;

        for (let i = 0; i < idsOfGoals.length; i++) {
            goal_marker_pos[i] = new L.LatLng(current_position.getLatLng().lat + latlngValue, current_position.getLatLng().lng + latlngValue);
            start_marker_pos[i] = new L.LatLng(current_position.getLatLng().lat + latlngValue, current_position.getLatLng().lng + latlngValue + this.#DISTANCE_BETWEEN_MARKERS);
            latlngValue = latlngValue + this.#DISTANCE_BETWEEN_MARKERS;
        }
        styleSheetContent = this.createGoalLine(true);

        style.styleSheetContent = styleSheetContent;
        style.createStyle();

        goalIsBeingPlanned = true;
    }

    createGoalLine(returnStyleSheet = false, isDraggable = true)
    {
        map.removeLayer(goalLayerGroup);

        let classNameGoalMarkers, classNameStartMarkers, initials;
        let usersData = dataGlobal.usersdata;
        let initialsArr = [];

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
        if (returnStyleSheet) {
            return styleSheetContent;
        }
    }

    #bindPopupToUsers(i)
    {
        if (userPopupContent.length > 0) {
            user_markers[i].bindPopup('<h3>'+userPopupContent[i]+'</h3>', {closeOnClick: false, autoClose: false, autoPan: false}).openPopup();
        }
    }
}