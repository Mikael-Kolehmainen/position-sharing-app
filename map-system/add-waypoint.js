// we use these in remove-waypoint.js and create-goal.js
let all_waypoints = [];
let goalIDs = [];

// WHEN ROUTE IS CLICKED ADD WAYPOINT WHERE CLICKED
function addWaypointToRoute(e) {
    const id = e.target.options.id;
    goalIDs.push(id);
    if (typeof goal_waypoints[id] == "undefined") {
        goal_waypoints[id] = [];
    }
    // remove previous lines
    map.removeLayer(draggableRouteLayerGroup);
    draggableRouteLayerGroup.eachLayer(function(layer) {
        if (id == layer.options.id) {
            draggableRouteLayerGroup.removeLayer(layer);
        }
    });
    // add waypoint where user clicks and save it to an array
    let waypoint = L.marker(e.latlng, {draggable: true})
                    .on('dragstart', dragStartHandler)
                    .on('drag', dragHandler)
                    .on('dragend', dragEndHandler);
    goalWaypointsLayerGroup.addLayer(waypoint);
    goal_waypoints[id].push(waypoint);
    all_waypoints.push(waypoint);
    // sort the array based on what waypoint is closest to start marker
    /*for (let i = 0; i < goal_waypoints[id].length - 1; i++) {
        if (goal_waypoints[id][i].getLatLng().distanceTo(start_marker_pos[id]) > goal_waypoints[id][i+1].getLatLng().distanceTo(start_marker_pos[id])) {
            let temp = goal_waypoints[id][i];
            goal_waypoints[id][i] = goal_waypoints[id][i+1];
            goal_waypoints[id][i+1] = temp;

            i = -1;
        }
    } */
    // put the waypoint that is closest to start marker first in array, first we figure out which point is closest and then get the index of it in the array
  /*  const goal_waypoints_distances = [];
    for (let i = 0; i < goal_waypoints[id].length; i++) {
        goal_waypoints_distances.push(goal_waypoints[id][i].getLatLng().distanceTo(start_marker_pos[id]));
    }
    console.log(goal_waypoints_distances);
    const min = Math.min(...goal_waypoints_distances);
    const index = goal_waypoints_distances.indexOf(min);
    const temp = goal_waypoints[id][0]
    goal_waypoints[id][0] = goal_waypoints[id][index];
    goal_waypoints[id][index] = temp; */

    // UPDATE LINES BETWEEN WAYPOINTS
    updateWaypointLines(id);

    map.addLayer(goalWaypointsLayerGroup);
}