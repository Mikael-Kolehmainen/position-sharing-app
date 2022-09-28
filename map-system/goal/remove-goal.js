// REMOVE FUNCTION
function removeDraggableGoal() {
    map.removeLayer(goalLayerGroup);
    goalLayerGroup.eachLayer(function(layer) {goalLayerGroup.removeLayer(layer)});
    map.removeLayer(draggableRouteLayerGroup);
    draggableRouteLayerGroup.eachLayer(function(layer) {draggableRouteLayerGroup.removeLayer(layer)});
    map.removeLayer(goalWaypointsLayerGroup);
    goalWaypointsLayerGroup.eachLayer(function(layer) {goalWaypointsLayerGroup.removeLayer(layer)});
    goal_waypoints = [];
    all_waypoints = [];
    goalIDs = [];
    start_marker_arr = [];
    start_marker_pos = [];
    goal_marker_arr = [];
    goal_marker_pos = [];
}
// REMOVE GOAL ONCLICK
function removeActiveGoal() {
    let xmlhttp = new XMLHttpRequest();
    let url = 'remove-goal.php?groupcode=' + groupCode;
    
    xmlhttp.open("GET", url, true);
    xmlhttp.onreadystatechange = function() {
        if(xmlhttp.readyState === XMLHttpRequest.DONE && xmlhttp.status === 200) {
            console.log("Successfully removed data.");
        }
    }
    xmlhttp.send();
    // HIDE ACTIVE GOAL DISCLAIMER
    let disclaimer = document.getElementById("active-goal-disclaimer");
    disclaimer.style.display = "none";
    // SHOW CREATE GOAL BTN
    let goalBtn = document.getElementById('goal-btn');
    goalBtn.style.display = 'block';
    // MISC
    userPopupContent = [];
    goalRouteIsDrawn = false;
    map.removeLayer(goalLayerGroup);
    goalLayerGroup.eachLayer(function(layer) {goalLayerGroup.removeLayer(layer)});
    goal_waypoints = [];
    all_waypoints = [];
    goalIDs = [];
    start_marker_arr = [];
    start_marker_pos = [];
    goal_marker_arr = [];
    goal_marker_pos = [];
}