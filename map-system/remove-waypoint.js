function removeWaypoint() {
    if (all_waypoints.length != 0
        && goalIDs.length != 0) {
        // Get the id of the goal that was last clicked
        const id = goalIDs[goalIDs.length - 1];
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
        updateWaypointLines(id);
        // REMOVE GOAL ID FROM ARRAY
        goalIDs.splice(goalIDs.length - 1, 1);
    }
}