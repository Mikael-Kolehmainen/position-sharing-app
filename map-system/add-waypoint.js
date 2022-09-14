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

    // UPDATE LINES BETWEEN WAYPOINTS
    updateWaypointLines(id);

    map.addLayer(goalWaypointsLayerGroup);
}