class Waypoint
{
    constructor(waypointId, waypointPosition)
    {
        this.id = waypointId
        this.position = waypointPosition;

        this.all_waypoints = [];
    }

    add()
    {
        const id = this.id;
        goal.goalIndexes.push(id);
        if (typeof goal.goal_waypoints[id] == "undefined") {
            goal.goal_waypoints[id] = [];
        }

        map.removeLayer(layerManagement.draggableRouteLayerGroup);
        layerManagement.draggableRouteLayerGroup.eachLayer(function(layer) {
            if (id == layer.options.id) {
                layerManagement.draggableRouteLayerGroup.removeLayer(layer);
            }
        });

        let waypoint = L.marker(this.position, {draggable: true})
                        .on('dragstart', dragStartHandler)
                        .on('drag', dragHandler)
                        .on('dragend', dragEndHandler);
        layerManagement.goalWaypointsLayerGroup.addLayer(waypoint);
        goal.goal_waypoints[id].push(waypoint);
        this.all_waypoints.push(waypoint);

        this.#updatePolylines();

        map.addLayer(layerManagement.goalWaypointsLayerGroup);
    }

    remove()
    {
        if (this.all_waypoints.length != 0 && goal.goalIndexes.length != 0) {
            const id = goal.goalIndexes[goal.goalIndexes.length - 1];
            this.id = id;

            const waypointToBeRemoved = this.all_waypoints[this.all_waypoints.length - 1];
            layerManagement.goalWaypointsLayerGroup.removeLayer(waypointToBeRemoved);

            let index;
            for (let i = 0; i < goal.goal_waypoints[id].length; i++) {
                if (goal.goal_waypoints[id][i] == this.all_waypoints[this.all_waypoints.length - 1]) {
                    index = i;
                }
            }
            this.all_waypoints.splice(this.all_waypoints.length - 1, 1);
            goal.goal_waypoints[id].splice(index, 1);

            this.#updatePolylines();

            goal.goalIndexes.splice(goal.goalIndexes.length - 1, 1);
        }
    }

    #updatePolylines()
    {
        const id = this.id;

        map.removeLayer(layerManagement.draggableRouteLayerGroup);
        layerManagement.draggableRouteLayerGroup.eachLayer(function(layer) {
            if (id == layer.options.id) {
                layerManagement.draggableRouteLayerGroup.removeLayer(layer);
            }
        });

        const polyLineOptions = {weight: 5, id: id};
        if (goal.goal_waypoints[id].length > 0) {
            for (let i = 0; i < goal.goal_waypoints[id].length; i++) {
                if (i == 0) {
                    goal.goalRoutes.push(new L.polyline([goal.goal_waypoints[id][i].getLatLng(), goal.start_marker_pos[id]], polyLineOptions));
                    goal.start_marker_arr[id].parentLine = [goal.goalRoutes[i]];
        
                    if (i == goal.goal_waypoints[id].length - 1) {
                        goal.goal_waypoints[id][i].parentLine = goal.goalRoutes[i];
                        goal.goal_marker_arr[id].parentLine = [goal.goalRoutes[i]];
                    } else {
                        goal.goal_waypoints[id][i].parentLine = [goal.goalRoutes[i]];
                    }
                } else if (i == goal.goal_waypoints[id].length - 1) {
                    goal.goalRoutes.push(new L.polyline([goal.goal_waypoints[id][i].getLatLng(), goal.goal_marker_pos[id]], polyLineOptions));
                    goal.goalRoutes.push(new L.polyline([goal.goal_waypoints[id][i].getLatLng(), goal.goal_waypoints[id][i-1].getLatLng()], polyLineOptions));

                    goal.goal_waypoints[id][i].parentLine = goal.goalRoutes;
                    goal.goal_waypoints[id][i-1].parentLine = [goal.goalRoutes[i], goal.goal_waypoints[id][i-1].parentLine[0]];
                    goal.goal_marker_arr[id].parentLine = [goal.goalRoutes[i]];
                }
                if (i != 0 && i != goal.goal_waypoints[id].length - 1) {
                    goal.goalRoutes.push(new L.polyline([goal.goal_waypoints[id][i].getLatLng(), goal.goal_waypoints[id][i-1].getLatLng()], polyLineOptions));
                    goal.goal_waypoints[id][i].parentLine = [goal.goalRoutes[i]];
                    goal.goal_waypoints[id][i-1].parentLine = [goal.goalRoutes[i], goal.goal_waypoints[id][i-1].parentLine[0]];
                }
            }
            for (let i = 0; i < goal.goalRoutes.length; i++) {
                goal.goalRoutes[i].on('click', addWaypointToRoute);
                layerManagement.draggableRouteLayerGroup.addLayer(goal.goalRoutes[j]);
            }
        } else {
            goal.goalRoutes = [];
            goal.goalRoutes[0] = new L.polyline([goal.start_marker_arr[id].getLatLng(), goal.goal_marker_arr[id].getLatLng()], polyLineOptions);
            goal.goalRoutes.on('click', addWaypointToRoute);
            layerManagement.draggableRouteLayerGroup.addLayer(goal.goalRoutes[0]);
            goal.start_marker_arr[id].parentLine = [goal.goalRoutes[0]];
            goal.goal_marker_arr[id].parentLine = [goal.goalRoutes[0]];
        }

        map.addLayer(layerManagement.draggableRouteLayerGroup);
    }
}