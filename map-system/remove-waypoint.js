function removeWaypoint() {
    if (all_waypoints.length != 0
        && typeof id != "undefined") {
            // REMOVE WAYPOINT FROM MAP
        const waypointToBeRemoved = all_waypoints[all_waypoints.length - 1];
        goalWaypointsLayerGroup.removeLayer(waypointToBeRemoved);
        // REMOVE WAYPOINT FROM ARRAY
        let index;
        for (let i = 0; i < goal_waypoints[id].length; i++) {
            if (goal_waypoints[id][i] == all_waypoints[all_waypoints.length - 1]) {
                index = i;
            }
        }
        all_waypoints.splice(all_waypoints.length - 1, 1);
        goal_waypoints[id].splice(index, 1);
        // UPDATE LINES BETWEEN WAYPOINTS

        // *make this a function and call it here and in add-waypoint.js
        // remove previous lines
        map.removeLayer(draggableRouteLayerGroup);
        draggableRouteLayerGroup.eachLayer(function(layer) {
            if (id == layer.options.id) {
                draggableRouteLayerGroup.removeLayer(layer);
            }
        });
        const polyLineOptions = {weight: 5, id: id};
        if (goal_waypoints[id].length > 0) {
            for (let i = 0; i < goal_waypoints[id].length; i++) {
                let polyline = [];
        
                if (i == 0) {
                    polyline.push(new L.polyline([goal_waypoints[id][i].getLatLng(), start_marker_pos[id]], polyLineOptions));
                    polyline[0].on('click', addWaypointToRoute);
                    draggableRouteLayerGroup.addLayer(polyline[0]);
                    start_marker_arr[id].parentLine = [polyline[0]];
        
                    if (i == goal_waypoints[id].length - 1) {
                        polyline.push(new L.polyline([goal_waypoints[id][i].getLatLng(), goal_marker_pos[id]], polyLineOptions));
                        polyline[1].on('click', addWaypointToRoute);
                        draggableRouteLayerGroup.addLayer(polyline[1]);
                        goal_waypoints[id][i].parentLine = polyline;
                        goal_marker_arr[id].parentLine = [polyline[1]];
                    } else {
                        goal_waypoints[id][i].parentLine = [polyline[0]];
                    }
                } else if (i == goal_waypoints[id].length - 1) {
                    polyline.push(new L.polyline([goal_waypoints[id][i].getLatLng(), goal_marker_pos[id]], polyLineOptions));
                    polyline.push(new L.polyline([goal_waypoints[id][i].getLatLng(), goal_waypoints[id][i-1].getLatLng()], polyLineOptions));
        
                    for (let j = 0; j < polyline.length; j++) {
                        polyline[j].on('click', addWaypointToRoute);
                        draggableRouteLayerGroup.addLayer(polyline[j]);
                    }
                    goal_waypoints[id][i].parentLine = polyline;
                    goal_waypoints[id][i-1].parentLine = [polyline[1], goal_waypoints[id][i-1].parentLine[0]];
                    goal_marker_arr[id].parentLine = [polyline[0]];
                }
                if (i != 0 && i != goal_waypoints[id].length - 1) {
                    polyline.push(new L.polyline([goal_waypoints[id][i].getLatLng(), goal_waypoints[id][i-1].getLatLng()], polyLineOptions));
                    polyline[0].on('click', addWaypointToRoute);
                    draggableRouteLayerGroup.addLayer(polyline[0]);
                    goal_waypoints[id][i].parentLine = [polyline[0]];
                    goal_waypoints[id][i-1].parentLine = [polyline[0], goal_waypoints[id][i-1].parentLine[0]];
                }
            }
        } else {
            let polyline = new L.polyline([start_marker_arr[id].getLatLng(), goal_marker_arr[id].getLatLng()], polyLineOptions);
            polyline.on('click', addWaypointToRoute);
            draggableRouteLayerGroup.addLayer(polyline);
            start_marker_arr[id].parentLine = [polyline];
            goal_marker_arr[id].parentLine = [polyline];
        }
    }
    map.addLayer(draggableRouteLayerGroup);
}