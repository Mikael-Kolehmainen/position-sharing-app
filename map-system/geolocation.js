let map = L.map('map', {zoomControl: false});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '© OpenStreetMap'
}).addTo(map);

// LAYER GROUPS
let refreshedLayerGroup = L.layerGroup();
let goalLayerGroup = L.layerGroup();
let draggableRouteLayerGroup = L.layerGroup();
let goalWaypointsLayerGroup = L.layerGroup();
let waterLayerGroup = L.layerGroup();
waterLayerGroup.addLayer(L.geoJSON(vaasa));

// GET THE GROUPCODE FROM SEARCH FIELD
const groupCode = new URLSearchParams(window.location.search).get('groupcode');

let userIcon = L.divIcon ({
    iconSize: [25, 25],
    iconAnchor: [12.5, 25],
    className: 'user-marker',
    popupAnchor: [0, -20]
});
let otherUsersIcon = L.divIcon ({
    iconSize: [25, 25],
    iconAnchor: [12.5, 25],
    className: 'other-user-marker',
    popupAnchor: [0, -20]
});

let current_position;
let counter = 0;
let data;
let styleSheetContent =  "";

let goal_marker_arr = [];
let goal_marker_pos = [];
let goalRouteIsDrawn = false;

let start_marker_arr = [];
let start_marker_pos = [];

let goal_waypoints = [];

let user_markers = [];
let userPopupContent = [];

let showWaterEnabled = false;

function onLocationFound(e) {
    // REMOVE THE PREVIOUS LAYER OF OBJECTS THAT WILL BE REFRESHED
    map.removeLayer(refreshedLayerGroup);

    current_position = L.marker(e.latlng, {icon: userIcon});
    refreshedLayerGroup.addLayer(current_position);
    
    // SEND POSITION DATA & GROUPCODE TO PHP
    let index = ['send-data', 'get-data'];
    let xmlhttp = new XMLHttpRequest();
    (function loop(i, length) {
        if (i >= length) {
            return;
        }
        let url = index[i] + ".php?pos=" + e.latlng + "&groupcode=" + groupCode;

        if (i == 1) {
            xmlhttp.onload = function() {
                // MARKERS
                removeStyles('js-style');
                user_markers = [];
                data = JSON.parse(this.responseText);
                let positionsArr = data.positionsdata.positions;
                let initialsArr = data.positionsdata.initials;
                let colorsArr = data.positionsdata.colors;
                let classNameOtherUsers;
                for (let i = 0; i < positionsArr.length; i++) {
                    positionsArr[i] = positionsArr[i].replace(/[^\d.,-]/g,'');
                    latlngArr = positionsArr[i].split(",");
                    marker = L.marker(L.latLng(latlngArr[0], latlngArr[1]), {icon: otherUsersIcon});
                    refreshedLayerGroup.addLayer(marker);
                    user_markers.push(marker);
                    let initial = '\"' + initialsArr[i] + '\"';
                    if (marker.getLatLng().equals(current_position.getLatLng())) {
                        // REMOVES USERS OWN MARKER WHICH IS ALREADY ON THE MAP
                        goalLayerGroup.removeLayer(marker);
                        // GIVES COLOR & INITIALS TO USERS MARKER
                        const stylesheet = document.styleSheets[0];
                        stylesheet.cssRules[1].style.setProperty('content', initial);
                        stylesheet.cssRules[0].style.setProperty('background-color', colorsArr[i]);
                    } else {
                         // GIVES COLOR & INITIALS TO OTHER MARKERS
                        classNameOtherUsers = 'other-user-marker-' + i;
                        styleSheetContent += '.' + classNameOtherUsers + '{ background-color: ' + colorsArr[i] + '; }';
                        // INITIALS
                        styleSheetContent += '.' + classNameOtherUsers + '::before { content: ' + initial + '; }';

                        marker._icon.classList.add(classNameOtherUsers);
                    }
                }
                createStyle(styleSheetContent, 'js-style');
                // MESSAGES
                let messagesArr = data.messagesdata.messages;
                initialsArr = data.messagesdata.initials;
                colorsArr = data.messagesdata.colors;

                updateChat(messagesArr, initialsArr, colorsArr);
                
                // GOALS
                const startsArr = data.goalspositions.startpositions;
                const goalsArr = data.goalspositions.goalpositions;
                if (goalsArr[0] != "empty" && startsArr[0] != "empty") {
                    // IF USER DOESN'T HAVE A GOAL, GIVE A NO GOAL VALUE
                    while (goalsArr.length < positionsArr.length) {
                        goalsArr.push("no goal");
                    }
                    for (let i = 0; i < goalsArr.length; i++) {
                        if (goalsArr[i] != "no goal") {
                            // SAVE START POSITIONS TO VARIABLE
                            startsArr[i] = startsArr[i].replace(/[^\d.,-]/g,'');
                            latlngArr = startsArr[i].split(",");
                            start_marker_pos[i] = new L.LatLng(latlngArr[0], latlngArr[1]);
                            // SAVE GOAL POSITIONS TO VARIABLE
                            goalsArr[i] = goalsArr[i].replace(/[^\d.,-]/g,'');
                            latlngArr = goalsArr[i].split(",");
                            goal_marker_pos[i] = new L.LatLng(latlngArr[0], latlngArr[1]);
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
                    for (let i = 0; i < goal_marker_arr.length; i++) {
                        // Check which of the positions are lower then save the lower one first so the ghostline gets drawn from the lower one to the higher one
                        if (start_marker_pos[i].lat < goal_marker_arr[i].getLatLng().lat) {
                            latlngs.push(start_marker_pos[i]);
                            latlngs.push(goal_marker_arr[i].getLatLng());
                        } else {
                            latlngs.push(goal_marker_arr[i].getLatLng());
                            latlngs.push(start_marker_pos[i]);
                        }
                        
                        if (!goalRouteIsDrawn) {
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
                                // Check if user or goal is in water
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
                                return a.lat - b.lat
                            });
                            polygonCenters.sort(function(a, b) {
                                return polygonSort.indexOf(a) - polygonSort.indexOf(b)
                            });
                            polygonBounds.sort(function(a, b) {
                                return polygonSort.indexOf(a) - polygonSort.indexOf(b)
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
                        }
                        
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
                }
            };
        }
        xmlhttp.open("GET", url, true);
        xmlhttp.onreadystatechange = function() {
            if(xmlhttp.readyState === XMLHttpRequest.DONE && xmlhttp.status === 200) {
                loop(i + 1, length);
            }
        }
        xmlhttp.send();
    })(0, index.length);
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