function applyDistance() {
    let distanceElement = document.getElementById('distance-number');

    if (distanceElement.value.length > 2) {
        distanceElement.value = 99;
    }
/*
    let distance = distanceElement.value / 1000;
    const increment = distanceElement.value / 1000;
    const startLatLng = start_marker_arr[0].getLatLng();
    const goalLatLng = goal_marker_arr[0].getLatLng();
    let newLatLng;

    for (let i = 1; i < start_marker_arr.length; i++) {
        // START MARKER
        newLatLng = new L.LatLng(startLatLng.lat + distance, startLatLng.lng + distance);
        start_marker_arr[i].setLatLng(newLatLng);
        start_marker_pos[i] = newLatLng;
        // GOAL MARKER
        newLatLng = new L.LatLng(goalLatLng.lat + distance, goalLatLng.lng + distance);
        goal_marker_arr[i].setLatLng(newLatLng);
        goal_marker_pos[i] = newLatLng;
        // UPDATE ROUTE OF START MARKER
        start_marker_arr[i].parentLine.forEach((line)=>{
            var latlngPoly = line.getLatLngs();
            latlngPoly.splice(start_marker_arr[i][L.stamp(line)], 1, start_marker_arr[i].getLatLng());
            line.setLatLngs(latlngPoly);
        }); */
        // UPDATE ROUTE OF GOAL MARKER
      /*  goal_marker_arr[i].parentLine.forEach((line)=>{
            var latlngPoly = line.getLatLngs();
            latlngPoly.splice(goal_marker_arr[i][L.stamp(line)], 1, goal_marker_arr[i].getLatLng());
            line.setLatLngs(latlngPoly);
        }); */
/*
        distance = distance + increment;
    } */
}