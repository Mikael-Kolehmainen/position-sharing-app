// HANDLER EVENTS FOR MARKERS

function dragStartHandler(e) {
    var marker = e.target;
    marker.polylineLatlng = {};
    e.target.parentLine.forEach((line)=>{
        var latlngPoly = line.getLatLngs(),         // Get the polyline's latlngs
            latlngMarker = marker.getLatLng();                             // Get the marker's current latlng
        for (var i = 0; i < latlngPoly.length; i++) {       // Iterate the polyline's latlngs
        if (latlngMarker.equals(latlngPoly[i])) {       // Compare marker's latlng ot the each polylines 
            marker.polylineLatlng[L.stamp(line)] = i;            // If equals store key in marker instance
        }
        }
    })
}
// Now you know the key of the polyline's latlng you can change it
// when dragging the marker on the dragevent:
function dragHandler(e) {
    // We get the index of the marker by looking at the classname 'other-user-marker[index]'
    let markerClassNames = this._icon.className;
    let markerClasses = markerClassNames.split(" ");
    for (let i = 0; i < goal_marker_pos.length; i++) {
        if (markerClasses.includes("user-goal-marker-"+i)) {
            goal_marker_pos[i] = this.getLatLng();
        }
        if (markerClasses.includes("user-start-marker-"+i)) {
            start_marker_pos[i] = this.getLatLng();
        }
    }
    var marker = e.target;
    e.target.parentLine.forEach((line)=>{
        var latlngPoly = line.getLatLngs(),         // Get the polyline's latlngs
          latlngMarker = marker.getLatLng();                             // Get the marker's current latlng
        latlngPoly.splice(marker.polylineLatlng[L.stamp(line)], 1, latlngMarker); // Replace the old latlng with the new
        line.setLatLngs(latlngPoly);           // Update the polyline with the new latlngs
    })
}

// Just to be clean and tidy remove the stored key on dragend:
function dragEndHandler(e) {
    var marker = e.target;
    delete marker.polylineLatlng;
}