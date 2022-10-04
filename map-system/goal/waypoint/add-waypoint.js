function addWaypointToRoute(e) 
{
    waypoint.id = e.target.options.id;
    waypoint.position = e.latlng;
    waypoint.add();
}