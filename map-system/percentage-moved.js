function calculatePercentage(start_marker_pos, goal_marker_pos, latlngs, user_markers) {
    let userlatlng;
    let goallatlng;
    if (start_marker_pos.lat < goal_marker_pos.lat) {
        userlatlng = new L.LatLng(latlngs[0]['lat'], latlngs[0]['lng']);
        goallatlng = new L.LatLng(latlngs[1]['lat'], latlngs[1]['lng']);
    } else {
        userlatlng = new L.LatLng(latlngs[1]['lat'], latlngs[1]['lng']);
        goallatlng = new L.LatLng(latlngs[0]['lat'], latlngs[0]['lng']);
    }
    
    return Math.round((1 - user_markers.getLatLng().distanceTo(goallatlng) / userlatlng.distanceTo(goallatlng)) * 100);
}