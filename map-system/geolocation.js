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

                removeChilds(document.getElementById('messages'));

                // Create structure of message
                /*
                    <div class='message'>
                        <div class='profile'>
                            <p>MK</p>
                        </div>
                        <p class='text'>Hello, this is a placeholder message.</p>
                    </div>
                */
                styleSheetContent = "";
                for (let i = 0; i < messagesArr.length; i++) {
                    const message = document.createElement("div");
                    message.classList.add('message');
                    const profile = document.createElement("div");
                    profile.classList.add('profile');
                    message.appendChild(profile);
                    const initialsText = document.createElement("p");
                    profile.appendChild(initialsText);
                    const messageText = document.createElement("p");
                    messageText.classList.add('text');
                    message.appendChild(messageText);

                    let node;

                    node = document.createTextNode(initialsArr[i]);
                    initialsText.appendChild(node);
                    node = document.createTextNode(messagesArr[i]);
                    messageText.appendChild(node);

                    const messages = document.getElementById("messages");
                    messages.appendChild(message);

                    classNameOtherUsers = 'other-profile-icon-' + i;
                    profile.classList.add(classNameOtherUsers);
                    styleSheetContent += '.' + classNameOtherUsers + '{ background-color: ' + colorsArr[i] + '; }';
                }
                createStyle(styleSheetContent, 'js-style');
                
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
                        let userlatlng;
                        let goallatlng;
                        if (start_marker_pos[i].lat < goal_marker_arr[i].getLatLng().lat) {
                            userlatlng = new L.LatLng(latlngs[0]['lat'], latlngs[0]['lng']);
                            goallatlng = new L.LatLng(latlngs[1]['lat'], latlngs[1]['lng']);
                        } else {
                            userlatlng = new L.LatLng(latlngs[1]['lat'], latlngs[1]['lng']);
                            goallatlng = new L.LatLng(latlngs[0]['lat'], latlngs[0]['lng']);
                        }
                        
                        let percentage = Math.round((1 - user_markers[i].getLatLng().distanceTo(goallatlng) / userlatlng.distanceTo(goallatlng)) * 100);
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

// FUNCTIONS

function createStyle(content, className) {
    let head = document.head;
    let style = document.createElement('style');
    style.classList.add(className);

    if (style.stylesheet) {
        style.stylesheet = content;
    } else {
        style.appendChild(document.createTextNode(content));
    }
    head.appendChild(style);
}
function removeStyles(className) {
    let styles = document.getElementsByClassName(className);

    for (let i = 0; i < styles.length; i++) {
        styles[i].remove();
    }
}
const removeChilds = (parent) => {
    while (parent.lastChild) {
        parent.removeChild(parent.lastChild);
    }
};

// GOAL FUNCTIONS

// CREATE GOAL BTN ONCLICK FUNCTION
function showDraggableGoal() {
    removeStyles('js-style-goals');
    const initialsArr = data.positionsdata.initials;
    
    let latlngValue = 0.002;
    // CREATE THE POSITIONS
    for (let i = 0; i < initialsArr.length; i++) {
        goal_marker_pos[i] = new L.LatLng(current_position.getLatLng().lat + latlngValue, current_position.getLatLng().lng + latlngValue);
        start_marker_pos[i] = new L.LatLng(current_position.getLatLng().lat + latlngValue, current_position.getLatLng().lng + latlngValue + 0.002);
        latlngValue = latlngValue + 0.002;
    }
    styleSheetContent = createGoalLine(true);
    createStyle(styleSheetContent, 'js-style-goals');
}
// CREATE FUNCTION
function createGoalLine(returnStyleSheet = false, isDraggable = true) {
    // REMOVE & CLEAR PREVIOUS GOALLINE
    map.removeLayer(goalLayerGroup);

    let classNameGoalMarkers, classNameStartMarkers, initial;
    const initialsArr = data.positionsdata.initials;
    const colorsArr = data.positionsdata.colors;

    for (let i = 0; i < goal_marker_pos.length; i++) {
        if (goal_marker_pos[i] != "no goal") {
            // CREATE START POINTS
            start_marker_arr[i] =  new L.Marker(start_marker_pos[i], {draggable: isDraggable, icon: otherUsersIcon});
            goalLayerGroup.addLayer(start_marker_arr[i]);
            // CREATE GOALS
            goal_marker_arr[i] = new L.Marker(goal_marker_pos[i], {draggable: isDraggable, icon: otherUsersIcon});
            goalLayerGroup.addLayer(goal_marker_arr[i]);
            map.addLayer(goalLayerGroup);
            // CREATE LINE BETWEEN START & GOAL (ONLY SHOW WHILE GOAL IS BEING PLANNED)
            if (isDraggable) {
                let polyline = [];
                polyline.push(new L.Polyline([start_marker_pos[i], goal_marker_pos[i]], {id: i}));
                draggableRouteLayerGroup.addLayer(polyline[0]);
                start_marker_arr[i].parentLine = polyline;
                goal_marker_arr[i].parentLine = polyline;
                polyline[0].on('click', addWaypointToRoute);
                map.addLayer(draggableRouteLayerGroup);
            }
            // START POINTS CSS
            classNameStartMarkers = 'user-start-marker-' + i;
            styleSheetContent += '.' + classNameStartMarkers + '{ background-color: red; border-radius: 0 !important;}';
            // GOALS CSS
            classNameGoalMarkers = 'user-goal-marker-' + i;
            styleSheetContent += '.' + classNameGoalMarkers + '{ background-color: lightgreen; border-radius: 0 !important;}';
            // INITIALS
            initial = '\"' + initialsArr[i] + '\"';
            styleSheetContent += '.' + classNameStartMarkers + '::before { content: ' + initial + '; }';
            styleSheetContent += '.' + classNameGoalMarkers + '::before { content: ' + initial + '; }';
            start_marker_arr[i]._icon.classList.add(classNameStartMarkers);
            goal_marker_arr[i]._icon.classList.add(classNameGoalMarkers);
            // ASSIGN EVENTHANDLERS TO MARKERS
            start_marker_arr[i]
                    .on('dragstart', dragStartHandler)
                    .on('drag', dragHandler)
                    .on('dragend', dragEndHandler);
            goal_marker_arr[i]
                    .on('dragstart', dragStartHandler)
                    .on('drag', dragHandler)
                    .on('dragend', dragEndHandler);

            // BIND PERCENTAGE POPUP TO USER MARKERS
            if (userPopupContent.length > 0) {
                if (user_markers[i]._mapToAdd != null) {
                    user_markers[i].bindPopup('<h3>'+userPopupContent[i]+'</h3>', {closeOnClick: false, autoClose: false, autoPan: false}).openPopup();
                } else {
                    current_position.bindPopup('<h3>'+userPopupContent[i]+'</h3>', {closeOnClick: false, autoClose: false, autoPan: false}).openPopup();
                }
            }
        }
    }
    if (returnStyleSheet) {
        return styleSheetContent;
    }
}
// REMOVE FUNCTION
function removeDraggableGoal() {
    map.removeLayer(goalLayerGroup);
    goalLayerGroup.eachLayer(function(layer) {goalLayerGroup.removeLayer(layer)});
    map.removeLayer(draggableRouteLayerGroup);
    draggableRouteLayerGroup.eachLayer(function(layer) {draggableRouteLayerGroup.removeLayer(layer)});
}
// SEND DATA FUNCTION
function sendGoalData() {
    let xmlhttp = new XMLHttpRequest();
    let url = 'send-data.php?goalpos=' + goal_marker_pos + '&startpos=' + start_marker_pos + '&groupcode=' + groupCode;

    xmlhttp.open("GET", url, true);
    xmlhttp.onreadystatechange = function() {
        if(xmlhttp.readyState === XMLHttpRequest.DONE && xmlhttp.status === 200) {
            console.log("Successfully sent data.");
        }
    }
    xmlhttp.send();
    // HIDE CREATE GOAL BTN
    let goalBtn = document.getElementById('goal-btn');
    goalBtn.style.display = 'none';
    // REMOVE DRAGGABLE ROUTE
    map.removeLayer(draggableRouteLayerGroup);
    draggableRouteLayerGroup.eachLayer(function(layer) {draggableRouteLayerGroup.removeLayer(layer)});
}
// REMOVE GOAL ONCLICK
function removeActiveGoal() {
    let xmlhttp = new XMLHttpRequest();
    let url = 'remove-data.php?groupcode=' + groupCode;
    
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
}
// ONCLICK FOR WATER SWITCH
function showWaterEntities() {
    if (showWaterEnabled) {
        map.removeLayer(waterLayerGroup);
        showWaterEnabled = false;
    } else {
        waterLayerGroup.addTo(map);
        showWaterEnabled = true;
    }
}

// HANDLER EVENT FOR ROUTE

// WHEN ROUTE IS CLICKED ADD WAYPOINT WHERE CLICKED
function addWaypointToRoute(e) {
    // remove previous lines
    map.removeLayer(draggableRouteLayerGroup);
    draggableRouteLayerGroup.eachLayer(function(layer) {draggableRouteLayerGroup.removeLayer(layer)});
    // add waypoint where user clicks and save it to an array
    let waypoint = L.marker(e.latlng, {draggable: true})
                    .on('dragstart', dragStartHandler)
                    .on('drag', dragHandler)
                    .on('dragend', dragEndHandler);
    goalWaypointsLayerGroup.addLayer(waypoint);
    goal_waypoints.push(waypoint);
    const id = e.target.options.id;
        // to-do: attach the polyline to the start marker and goal marker
    for (let i = 0; i < goal_waypoints.length; i++) {
        let polyline = [];

        if (i == 0) {
            polyline.push(new L.polyline([goal_waypoints[i].getLatLng(), start_marker_pos[id]]));
            polyline[0].on('click', addWaypointToRoute);
            draggableRouteLayerGroup.addLayer(polyline[0]);

            if (i == goal_waypoints.length - 1) {
                polyline.push(new L.polyline([goal_waypoints[i].getLatLng(), goal_marker_pos[id]]));
                polyline[1].on('click', addWaypointToRoute);
                draggableRouteLayerGroup.addLayer(polyline[1]);
                goal_waypoints[i].parentLine = polyline;
            } else {
                goal_waypoints[i].parentLine = polyline[0];
            }
        } else if (i == goal_waypoints.length - 1) {
            polyline = new L.polyline([goal_waypoints[i].getLatLng(), goal_marker_pos[id]]);
            polyline.on('click', addWaypointToRoute);
            draggableRouteLayerGroup.addLayer(polyline);
            goal_waypoints[i].parentLine = polyline;
        }
        if (i != 0 && i != goal_waypoints.length - 1) {
            polyline = new L.polyline([goal_waypoints[i].getLatLng(), goal_waypoints[i+1]]);
            polyline.on('click', addWaypointToRoute);
            draggableRouteLayerGroup.addLayer(polyline);
            goal_waypoints[i].parentLine = polyline;
        }
    }
    map.addLayer(goalWaypointsLayerGroup);
    map.addLayer(draggableRouteLayerGroup);
}

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