// HANDLER EVENT FOR ROUTE

// WHEN ROUTE IS CLICKED ADD WAYPOINT WHERE CLICKED
function addWaypointToRoute(e) {
    // remove previous lines
    map.removeLayer(draggableRouteLayerGroup);
    draggableRouteLayerGroup.eachLayer(function(layer) {draggableRouteLayerGroup.removeLayer(layer)});
    // add waypoint where user clicks and save it to an array
    let waypoint = L.marker(e.latlng, {draggable: true})
                    .on('dragstart', dragStartHandler)
                    .on('drag', dragHandler)
                    .on('dragend', dragEndHandler);
    goalWaypointsLayerGroup.addLayer(waypoint);
    goal_waypoints.push(waypoint);
    const id = e.target.options.id;
    // sort the array based on what waypoint is closest to start marker
    for (let i = 0; i < goal_waypoints.length - 1; i++) {
        if (goal_waypoints[i].getLatLng().distanceTo(start_marker_pos[id]) > goal_waypoints[i+1].getLatLng().distanceTo(start_marker_pos[id])) {
            let temp = goal_waypoints[i];
            goal_waypoints[i] = goal_waypoints[i+1];
            goal_waypoints[i+1] = temp;

            i = -1;
        }
    }
    // put the waypoint that is closest to start marker first in array, first we figure out which point is closest and then get the index of it in the array
  /*  const goal_waypoints_distances = [];
    for (let i = 0; i < goal_waypoints.length; i++) {
        goal_waypoints_distances.push(goal_waypoints[i].getLatLng().distanceTo(start_marker_pos[id]));
    }
    console.log(goal_waypoints_distances);
    const min = Math.min(...goal_waypoints_distances);
    const index = goal_waypoints_distances.indexOf(min);
    const temp = goal_waypoints[0]
    goal_waypoints[0] = goal_waypoints[index];
    goal_waypoints[index] = temp; */
    // CREATE LINES BETWEEN MARKERS
    const polyLineOptions = {weight: 5, id: id};
    for (let i = 0; i < goal_waypoints.length; i++) {
        let polyline = [];

        if (i == 0) {
            polyline.push(new L.polyline([goal_waypoints[i].getLatLng(), start_marker_pos[id]], polyLineOptions));
            polyline[0].on('click', addWaypointToRoute);
            draggableRouteLayerGroup.addLayer(polyline[0]);
            start_marker_arr[id].parentLine = [polyline[0]];

            if (i == goal_waypoints.length - 1) {
                polyline.push(new L.polyline([goal_waypoints[i].getLatLng(), goal_marker_pos[id]], polyLineOptions));
                polyline[1].on('click', addWaypointToRoute);
                draggableRouteLayerGroup.addLayer(polyline[1]);
                goal_waypoints[i].parentLine = polyline;
                goal_marker_arr[id].parentLine = [polyline[1]];
            } else {
                goal_waypoints[i].parentLine = [polyline[0]];
            }
        } else if (i == goal_waypoints.length - 1) {
            polyline.push(new L.polyline([goal_waypoints[i].getLatLng(), goal_marker_pos[id]], polyLineOptions));
            polyline.push(new L.polyline([goal_waypoints[i].getLatLng(), goal_waypoints[i-1].getLatLng()], polyLineOptions));

            for (let j = 0; j < polyline.length; j++) {
                polyline[j].on('click', addWaypointToRoute);
                draggableRouteLayerGroup.addLayer(polyline[j]);
            }
            goal_waypoints[i].parentLine = polyline;
            goal_waypoints[i-1].parentLine = [polyline[1], goal_waypoints[i-1].parentLine[0]];
            goal_marker_arr[id].parentLine = [polyline[0]];
        }
        if (i != 0 && i != goal_waypoints.length - 1) {
            polyline.push(new L.polyline([goal_waypoints[i].getLatLng(), goal_waypoints[i-1].getLatLng()], polyLineOptions));
            polyline[0].on('click', addWaypointToRoute);
            draggableRouteLayerGroup.addLayer(polyline[0]);
            goal_waypoints[i].parentLine = [polyline[0]];
            goal_waypoints[i-1].parentLine = [polyline[0], goal_waypoints[i-1].parentLine[0]];
        }
    }
    map.addLayer(goalWaypointsLayerGroup);
    map.addLayer(draggableRouteLayerGroup);
}