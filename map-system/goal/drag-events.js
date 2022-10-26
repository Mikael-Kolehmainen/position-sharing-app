function dragStartHandler(e) 
{
    let marker = e.target;
    marker.polylineLatlng = {};
    e.target.parentLine.forEach((line)=>{
        let latlngPoly = line.getLatLngs(),
        latlngMarker = marker.getLatLng();
        for (let i = 0; i < latlngPoly.length; i++) {
            if (latlngMarker.equals(latlngPoly[i])) {
                marker.polylineLatlng[L.stamp(line)] = i;
            }
        }
    })
}

function dragHandler(e) 
{
    let markerClassNames = this._icon.className;
    let markerClasses = markerClassNames.split(" ");
    for (let i = 0; i < goal.goal_marker_pos.length; i++) {
        if (markerClasses.includes("user-goal-marker-"+i)) {
            goal.goal_marker_pos[i] = this.getLatLng();
            goal.goal_marker_arr[i].setLatLng(this.getLatLng());
        }
        if (markerClasses.includes("user-start-marker-"+i)) {
            goal.start_marker_pos[i] = this.getLatLng();
            goal.start_marker_arr[i].setLatLng(this.getLatLng());
        }
    }
    let marker = e.target;
    e.target.parentLine.forEach((line)=>{
        let latlngPoly = line.getLatLngs(),
        latlngMarker = marker.getLatLng();
        latlngPoly.splice(marker.polylineLatlng[L.stamp(line)], 1, latlngMarker);
        line.setLatLngs(latlngPoly);
    })
}

function dragEndHandler(e) 
{
    let marker = e.target;
    delete marker.polylineLatlng;
}