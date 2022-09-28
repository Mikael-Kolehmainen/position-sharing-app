let map = L.map('map', {zoomControl: false});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '© OpenStreetMap'
}).addTo(map);

let refreshedLayerGroup = L.layerGroup();
let goalLayerGroup = L.layerGroup();
let draggableRouteLayerGroup = L.layerGroup();
let goalWaypointsLayerGroup = L.layerGroup();
let waterLayerGroup = L.layerGroup();
waterLayerGroup.addLayer(L.geoJSON(vaasa));

const groupCode = new URLSearchParams(window.location.search).get('groupcode');

let usersData;

let userIcon = L.divIcon ({
    iconSize: [25, 25],
    iconAnchor: [12.5, 25],
    className: 'user-marker',
    popupAnchor: [0, -20]
});

let current_position;
let counter = 0;
let dataGlobal;
let styleSheetContent =  "";

let goal_marker_arr = [];
let goal_marker_pos = [];
let goalRouteIsDrawn = false;
let goalIsBeingPlanned = false;

let start_marker_arr = [];
let start_marker_pos = [];

let goal_waypoints = [];

let user_markers = [];
let userPopupContent = [];

function onLocationFound(e) 
{
    map.removeLayer(refreshedLayerGroup);

    // change to LatLng object
    current_position = L.marker(e.latlng);

    sendDataToPHP("send-position.php?lat=" + e.latlng.lat + "&lng=" + e.latlng.lng + "&groupcode=" + groupCode, function()
    {
        getDataFromPHP("get-data.php?groupcode=" + groupCode, function(data)
        {
            removeStyles('js-style');
            user_markers = [];
            dataGlobal = data;
            usersData = data.usersdata;
            let classNameOtherUsers;
            for (let i = 0; i < usersData.length; i++) {
                marker = L.marker(L.latLng(usersData[i].position), {icon: userIcon});
                refreshedLayerGroup.addLayer(marker);
                user_markers.push(marker);
                let initials = '\"' + usersData[i].initials + '\"';

                // GIVES COLOR & INITIALS TO OTHER MARKERS
                classNameOtherUsers = 'user-marker-' + i;
                styleSheetContent += '.' + classNameOtherUsers + '{ background-color: ' + usersData[i].color + '; }';
                // INITIALS
                styleSheetContent += '.' + classNameOtherUsers + '::before { content: ' + initials + '; }';

                marker._icon.classList.add(classNameOtherUsers);
            }
            createStyle(styleSheetContent, 'js-style');

            const chat = new Chat(data.messagesdata);
            chat.updateChat();
            
            // GOALS
            const goalsData = data.goalsdata;
            if (goalsData[0] != "empty") {
                // IF USER DOESN'T HAVE A GOAL, GIVE A NO GOAL VALUE
                while (goalsData.length < usersData.length) {
                    goalsData.push("no goal");
                }
                for (let i = 0; i < goalsData.length; i++) {
                    if (goalsData[i] != "no goal") {
                        // SAVE START POSITIONS TO VARIABLE
                        start_marker_pos[i] = new L.LatLng(goalsData[i].start_position[0], goalsData[i].start_position[1]);
                        // SAVE WAYPOINT POSITIONS TO VARIABLE
                        goal_waypoints[i] = [];
                        for (let j = 0; j < goalsData[i].waypoints.length; j++) {
                            goal_waypoints[i][j] = new L.marker(goalsData[i].waypoints[j]);
                        }
                        // SAVE GOAL POSITIONS TO VARIABLE
                        goal_marker_pos[i] = new L.LatLng(goalsData[i].goal_position[0], goalsData[i].goal_position[1]);
                    } else {
                        start_marker_pos[i] = "no goal";
                        goal_marker_pos[i] = "no goal";
                    }
                }
                createGoalLine(false, false);

                // SHOW ACTIVE GOAL DISCLAIMER
                let disclaimer = document.getElementById('active-goal-disclaimer');
                disclaimer.style.display = 'block';
                // HIDE CREATE GOAL BTN
                let goalBtn = document.getElementById('goal-btn');
                goalBtn.style.display = 'none';

                // SHOW THE FASTEST ROUTE TO THE ACTIVE GOAL
                let latlngs = [];
                userPopupContent = [];
                let percentages = [];
                for (let i = 0; i < user_markers.length; i++) {
                    // Check which of the positions are lower then save the lower one first so the ghostline gets drawn from the lower one to the higher one
                    if (start_marker_pos[i].lat < goal_marker_arr[i].getLatLng().lat) {
                        latlngs.push(start_marker_pos[i]);
                        if (typeof goal_waypoints[i] != "undefined") {
                            for (let j = 0; j < goal_waypoints[i].length; j++) {
                                latlngs.push(goal_waypoints[i][j].getLatLng());
                            }
                        }
                        latlngs.push(goal_marker_arr[i].getLatLng());
                    } else {
                        latlngs.push(goal_marker_arr[i].getLatLng());
                        if (typeof goal_waypoints[i] != "undefined") {
                            for (let j = 0; j < goal_waypoints[i].length; j++) {
                                latlngs.push(goal_waypoints[i][j].getLatLng());
                            }
                        }
                        latlngs.push(start_marker_pos[i]);
                    }

                    let polylineRoute = L.polyline(latlngs, {color: 'red'});
                    goalLayerGroup.addLayer(polylineRoute);
                    goalLayerGroup.addTo(map);
                    
                    /* WATER ALGORITHM CODE COMMENTED FOR NOW BECAUSE IT DOESN'T WORK WITH THE WAYPOINTS SYSTEM */

                /*     if (!goalRouteIsDrawn) {
                        // DRAW A GHOST LINE BEFORE THE ACTUAL ROUTE
                        let ghostLine = L.polyline(latlngs, {color: 'red', opacity: 0});
                        let intersectPoint;
                        let polygonCenters = [];
                        let polygonBounds = [];
                        let polygonSort = [];
                        let circleCenter;
                        let circleOptions = {steps: 100, units: 'meters', options: {}};
                        let circleRadius = 80;
                        let intersectPositions_1 = [];
                        let intersectPositions_2 = [];
                        // FIND IF GHOSTLINE INTERSECTS WITH A WATER ENTITY
                        for (let j = 0; j < vaasa['features'].length; j++) {
                            intersectPoint = turf.lineIntersect(turf.polygonToLine(vaasa['features'][j]), ghostLine.toGeoJSON());
                            // Check if there's an intersectpoint and if user or goal isn't in water
                            if (intersectPoint.features.length > 0
                                && !turf.booleanPointInPolygon([start_marker_pos[i].lng, start_marker_pos[i].lat], vaasa['features'][j])
                                && !turf.booleanPointInPolygon([goal_marker_arr[i].getLatLng().lng, goal_marker_arr[i].getLatLng().lat], vaasa['features'][j])) {
                                polygonCenters.push(L.geoJSON(vaasa['features'][j]).getBounds().getCenter());
                                polygonBounds.push(L.geoJSON(vaasa['features'][j]).getBounds());
                                polygonSort.push(L.geoJSON(vaasa['features'][j]).getBounds()._northEast);
                            }
                        }
                        // sort array from lowest points to highest points
                        polygonSort.sort(function(a, b) {
                            return a.lat - b.lat;
                        });
                        polygonCenters.sort(function(a, b) {
                            return polygonSort.indexOf(a) - polygonSort.indexOf(b);
                        });
                        polygonBounds.sort(function(a, b) {
                            return polygonSort.indexOf(a) - polygonSort.indexOf(b);
                        });
                        if (polygonCenters.length > 0) {
                            for (let j = 0; j < polygonCenters.length; j++) {
                                circleCenter = [polygonCenters[j].lng, polygonCenters[j].lat];
                                circleRadius = polygonBounds[j]._northEast.distanceTo(polygonBounds[j]._southWest) / 2;
                                let intersectCircle = turf.circle(circleCenter, circleRadius, circleOptions);
                                let intersectPointsOfCircle = turf.lineIntersect(intersectCircle, ghostLine.toGeoJSON());
                                let intersectPositionSwapped_1 = new L.LatLng(start_marker_pos[i].lat, start_marker_pos[i].lng);
                                let intersectPositionSwapped_2 = goal_marker_arr[i].getLatLng();

                                // SWAP PLACES OF LATITUDE & LONGITUDE
                                if (typeof intersectPointsOfCircle.features[0] != "undefined") {
                                    intersectPositionSwapped_1 = new L.LatLng(intersectPointsOfCircle.features[0].geometry.coordinates[1], intersectPointsOfCircle.features[0].geometry.coordinates[0]);
                                }
                                if (typeof intersectPointsOfCircle.features[1] != "undefined") {
                                    intersectPositionSwapped_2 = new L.LatLng(intersectPointsOfCircle.features[1].geometry.coordinates[1], intersectPointsOfCircle.features[1].geometry.coordinates[0]);
                                }
                                intersectPositions_1.push(intersectPositionSwapped_1);
                                intersectPositions_2.push(intersectPositionSwapped_2);
                                // Calculate the arc length from radius and chord length then use arc length and radius to calculate sector angle.
                                let chordLength = intersectPositionSwapped_1.distanceTo(intersectPositionSwapped_2); 
                                let arcLength = Math.asin(chordLength / (2 * circleRadius)) * 2 * circleRadius;
                                let centralAngle = arcLength / circleRadius;
                                // convert angle to degrees from radians
                                centralAngle = Math.round(centralAngle * 180 / Math.PI);
                                let arcRoute = turf.lineArc(circleCenter, circleRadius, 0.0, centralAngle, circleOptions);
                                let attachmentRadius = 1;
                                let attachmentPoint_1 = turf.circle([intersectPositionSwapped_1.lng, intersectPositionSwapped_1.lat], attachmentRadius, circleOptions);
                                let attachmentPoint_2 = turf.circle([intersectPositionSwapped_2.lng, intersectPositionSwapped_2.lat], attachmentRadius, circleOptions);
                                let turnIncrement = .1;
                                let arcIntersect_1 = turf.booleanIntersects(turf.point(arcRoute.geometry.coordinates[0]), attachmentPoint_1);
                                let arcIntersect_2 = turf.booleanIntersects(turf.point(arcRoute.geometry.coordinates[0]), attachmentPoint_2);
                                let arcValue = 0;
                                // Turn the arc around until it intersects with the intersectpoints
                                if (typeof intersectPointsOfCircle.features[0] != "undefined"
                                    && typeof intersectPointsOfCircle.features[1] != "undefined") {
                                    while (!arcIntersect_1 && !arcIntersect_2) {
                                        arcRoute = turf.lineArc(circleCenter, circleRadius, arcValue, centralAngle + arcValue, circleOptions);
                                        arcValue = arcValue + turnIncrement;
                                        arcIntersect_1 = turf.booleanIntersects(turf.point(arcRoute.geometry.coordinates[0]), attachmentPoint_1);
                                        arcIntersect_2 = turf.booleanIntersects(turf.point(arcRoute.geometry.coordinates[0]), attachmentPoint_2);
                                        if (arcValue >= 360) {
                                            attachmentRadius = 2;
                                            if (arcValue >= 1800) {
                                                break;
                                            } else if (arcValue >= 1400) {
                                                attachmentRadius = 5;
                                            } else if (arcValue >= 1080) {
                                                attachmentRadius = 4;
                                            } else if (arcValue >= 720) {
                                                attachmentRadius = 3;
                                            }

                                            attachmentPoint_1 = turf.circle([intersectPositionSwapped_1.lng, intersectPositionSwapped_1.lat], attachmentRadius, circleOptions);
                                            attachmentPoint_2 = turf.circle([intersectPositionSwapped_2.lng, intersectPositionSwapped_2.lat], attachmentRadius, circleOptions);
                                        }
                                    }
                                }

                                let ghoststyle = {fillColor: 'none', color: 'red', opacity: 0};
                                let polystyle = {fillColor: 'none', color: 'red', opacity: 1};
                                L.geoJSON(intersectCircle, {style: ghoststyle}).addTo(goalLayerGroup);
                                
                                L.geoJSON(arcRoute, {style: polystyle}).addTo(goalLayerGroup);
                            }
                            // DRAW ROUTELINES BETWEEN THE ARCES
                            // with the if statements we figure out which intersectposition in the water entity is closer to another intersecposition in another water entity
                            const polyLineStyle = {color: 'red', opacity: 1};
                            for (let j = 0; j < intersectPositions_1.length; j++) {
                                if (j == 0) {
                                    if (intersectPositions_1[j].distanceTo(start_marker_pos[i]) < intersectPositions_2[j].distanceTo(start_marker_pos[i])) {
                                        ghostLine = L.polyline([intersectPositions_1[j], start_marker_pos[i]], polyLineStyle);
                                    } else {
                                        ghostLine = L.polyline([intersectPositions_2[j], start_marker_pos[i]], polyLineStyle);
                                    }
                                    goalLayerGroup.addLayer(ghostLine);
                                } else {
                                    if (intersectPositions_1[j-1].distanceTo(intersectPositions_1[j]) < intersectPositions_2[j-1].distanceTo(intersectPositions_1[j])) {
                                        if (intersectPositions_1[j-1].distanceTo(intersectPositions_1[j]) < intersectPositions_1[j-1].distanceTo(intersectPositions_2[j])) {
                                            ghostLine = L.polyline([intersectPositions_1[j-1], intersectPositions_1[j]], polyLineStyle);
                                        } else {
                                            ghostLine = L.polyline([intersectPositions_1[j-1], intersectPositions_2[j]], polyLineStyle);
                                        }
                                    } else {
                                        if (intersectPositions_2[j-1].distanceTo(intersectPositions_1[j]) < intersectPositions_2[j-1].distanceTo(intersectPositions_2[j])) {
                                            ghostLine = L.polyline([intersectPositions_2[j-1], intersectPositions_1[j]], polyLineStyle);
                                        } else {
                                            ghostLine = L.polyline([intersectPositions_2[j-1], intersectPositions_2[j]], polyLineStyle);
                                        }
                                    }
                                    goalLayerGroup.addLayer(ghostLine);
                                }
                                if (j == intersectPositions_1.length - 1) {
                                    if (intersectPositions_1[j].distanceTo(goal_marker_arr[i].getLatLng()) < intersectPositions_2[j].distanceTo(goal_marker_arr[i].getLatLng())) {
                                        ghostLine = L.polyline([intersectPositions_1[j], goal_marker_arr[i].getLatLng()], polyLineStyle);
                                    } else {
                                        ghostLine = L.polyline([intersectPositions_2[j], goal_marker_arr[i].getLatLng()], polyLineStyle);
                                    }
                                    goalLayerGroup.addLayer(ghostLine);
                                }
                            }
                        } else {
                            let polylineRoute = L.polyline(latlngs, {color: 'red'});
                            goalLayerGroup.addLayer(polylineRoute);
                        }
                        goalLayerGroup.addTo(map);
                    } */
                    
                    // GET PERCENTAGE OF DISTANCE MOVED
                    let percentage = calculatePercentage(start_marker_pos[i], goal_marker_pos[i], latlngs, user_markers[i]);

                    percentages.push(percentage);

                    // ADD PERCENTAGE TO POPUP CONTENT
                    userPopupContent.push(percentage + "%");

                    latlngs = [];
                }
                goalRouteIsDrawn = true;
                // TELL USER TO SLOW DOWN IF 10% FURTHER THAN OTHERS
                let smallestPercentage = Math.min(...percentages);
                for (let i = 0; i < percentages.length; i++) {
                    if (smallestPercentage + 10 < percentages[i]) {
                        userPopupContent[i] += "\n(Slow down)";
                    }
                }
            } else if (!goalIsBeingPlanned) {
                // REMOVE GOALS FROM MAP
                map.removeLayer(goalLayerGroup);
                goalLayerGroup.eachLayer(function(layer) {
                    goalLayerGroup.removeLayer(layer);
                });
                // HIDE ACTIVE GOAL DISCLAIMER
                let disclaimer = document.getElementById('active-goal-disclaimer');
                disclaimer.style.display = 'none';
            }
            // CREATE STYLESHEET FOR GOAL MENU
            for (let i = 0; i < usersData.length; i++) {
                const className = 'goal-menu-user-marker-' + i;
                styleSheetContent += '.' + className + '{ background-color: ' + usersData[i].color + '; }';
            }
                createStyle(styleSheetContent, 'js-style');    
        });
    });

    
    
    refreshedLayerGroup.addTo(map);
}

function onLocationError(e) {
    alert(e.message);
}

map.on('locationfound', onLocationFound);
map.on('locationerror', onLocationError);

function locate() {
    if (counter == 0) {
        map.locate({setView: true, enableHighAccuracy: true});
        counter = 1;
    } else if (counter == 1) {
        map.locate({setView: false, enableHighAccuracy: true});
    }
}

locate();
setInterval(locate, 3000);

function sendDataToPHP(url, _callback)
{
    let xmlhttp = new XMLHttpRequest();

    xmlhttp.open("GET", url, true);

    xmlhttp.onload = function()
    {
        if (xmlhttp.status >= 200 && xmlhttp.status < 400)
        {
            _callback();
        }
    }

    xmlhttp.send();
}

function getDataFromPHP(url, _callback)
{
    let xmlhttp = new XMLHttpRequest();

    xmlhttp.open("GET", url, true);

    xmlhttp.onload = function() 
    {
        if (xmlhttp.status >= 200 && xmlhttp.status < 400) 
        {
            _callback(JSON.parse(this.responseText));
        }
    }

    xmlhttp.send();
}