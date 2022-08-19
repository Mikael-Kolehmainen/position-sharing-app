let map = L.map('map', {zoomControl: false});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '© OpenStreetMap'
}).addTo(map);

// LAYER GROUPS
let refreshedLayerGroup = L.layerGroup();
let goalLayerGroup = L.layerGroup();

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
let goalIsBeingCreated = false;
let goalRouteIsDrawn = false;

let user_markers = [];
let userPopupContent = [];

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
                const goalsArr = data.goalspositions.positions;
                if (goalsArr[0] != "empty") {
                    let polyLineCords = [];
                    for (let i = 0; i < goalsArr.length; i++) {
                        goalsArr[i] = goalsArr[i].replace(/[^\d.,-]/g,'');
                        latlngArr = goalsArr[i].split(",");
                        goal_marker_pos[i] = new L.LatLng(latlngArr[0], latlngArr[1]);
                        polyLineCords.push(goal_marker_pos[i]);
                    }
                    createGoalLine(polyLineCords, false, false);

                    // SHOW ACTIVE GOAL DISCLAIMER
                    let disclaimer = document.getElementById('active-goal-disclaimer');
                    disclaimer.style.display = 'block';
                    // HIDE CREATE GOAL BTN
                    let goalBtn = document.getElementById('goal-btn');
                    goalBtn.style.display = 'none';

                    // SAVE ORIGINAL POSITIONS OF USERS
                    if (!localStorage.getItem('user-markers')) {
                        let user_positions = [];
                        for (let i = 0; i < user_markers.length; i++) {
                            user_positions.push(user_markers[i].getLatLng());
                        }
                        localStorage.setItem('user-markers', JSON.stringify(user_positions));
                    }
                    // SHOW THE FASTEST ROUTE TO THE ACTIVE GOAL
                    if (localStorage.getItem('user-markers')) {
                        let latlngs = [];
                        let original_user_markers = JSON.parse(localStorage.getItem('user-markers'));
                        userPopupContent = [];
                        let percentages = [];
                        for (let i = 0; i < original_user_markers.length; i++) {
                            latlngs.push(original_user_markers[i]);
                            latlngs.push(goal_marker_arr[i].getLatLng());
                            
                            if (!goalRouteIsDrawn) {
                                let polylineRoute = L.polyline(latlngs, {color: 'red'});
                                goalLayerGroup.addLayer(polylineRoute);
                                console.log("test");
                                // DRAW A GHOST LINE BEFORE THE ACTUAL ROUTE *change opacity to 0 after it works
                                // create a circle at intersect point and start new ghost line where previous ghostline and circle
                                // intersect
                                let ghostLine = L.polyline(latlngs, {color: 'black', opacity: 1});
                                goalLayerGroup.addLayer(ghostLine);
                                let intersectPoints = 1;
                                for (let j = 0; j < vaasa['features'].length; j++) {
                                    if (intersectPoints == 1 || intersectPoints.features.length <= 0) {
                                        intersectPoints = turf.lineIntersect(turf.polygonToLine(vaasa['features'][i]), polylineRoute.toGeoJSON());
                                    }
                                } 
                                if (intersectPoints.features.length > 0) {
                                    // for some reason the latitude and longitude are switched in the geojson file.
                                    let radiusCircle = L.circle([intersectPoints.features[0].geometry.coordinates[1], intersectPoints.features[0].geometry.coordinates[0]], {radius: 100}).addTo(map);
                                    intersectPoints = turf.lineIntersect(radiusCircle.toGeoJSON(), ghostLine.toGeoJSON());
                                    /*console.log(intersectPoints);
                                    console.log(radiusCircle.toGeoJSON());
                                    console.log(ghostLine.toGeoJSON()); */
                                    L.geoJSON(intersectPoints.features).addTo(map);
                                }
                                goalLayerGroup.addTo(map);
                            }
                            
                            // GET PERCENTAGE OF DISTANCE MOVED
                            let userlatlng = new L.LatLng(latlngs[0]['lat'], latlngs[0]['lng']);
                            let goallatlng = new L.LatLng(latlngs[1]['lat'], latlngs[1]['lng']);
                            
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
    goalIsBeingCreated = true;
    const initialsArr = data.positionsdata.initials;
    
    let polylineCords = [];
    let latlngValue = 0.002;
    // CREATE THE POSITIONS
    for (let i = 0; i < initialsArr.length; i++) {
        goal_marker_pos[i] = new L.LatLng(current_position.getLatLng().lat + latlngValue, current_position.getLatLng().lng + latlngValue);
        polylineCords.push(goal_marker_pos[i]);
        latlngValue = latlngValue + 0.002;
    }
    styleSheetContent = createGoalLine(polylineCords, true);
    createStyle(styleSheetContent, 'js-style-goals');
}
// CREATE FUNCTION
function createGoalLine(polyLineCords, returnStyleSheet = false, isDraggable = true) {
    // REMOVE & CLEAR PREVIOUS GOALLINE
    map.removeLayer(goalLayerGroup);

/*    let polyline = new L.polyline(polyLineCords);
    goalLayerGroup.addLayer(polyline); */
    let classNameGoalMarkers, initial;
    const initialsArr = data.positionsdata.initials;
    const colorsArr = data.positionsdata.colors;

    for (let i = 0; i < initialsArr.length; i++) {
        goal_marker_arr[i] = new L.Marker(goal_marker_pos[i], {draggable: isDraggable, icon: otherUsersIcon});
        goalLayerGroup.addLayer(goal_marker_arr[i]);
        map.addLayer(goalLayerGroup);
        classNameGoalMarkers = 'user-goal-marker-' + i;
        styleSheetContent += '.' + classNameGoalMarkers + '{ background-color: ' + colorsArr[i] + '; border-radius: 0 !important;}';
        // INITIALS
        initial = '\"' + initialsArr[i] + '\"';
        styleSheetContent += '.' + classNameGoalMarkers + '::before { content: ' + initial + '; }';
        goal_marker_arr[i]._icon.classList.add(classNameGoalMarkers);
        // ASSIGN TO POLYLINE
    //    goal_marker_arr[i].parentLine = polyline;
        // ASSIGN EVENTHANDLERS TO MARKERS
        goal_marker_arr[i]
                //.on('dragstart', dragStartHandler)
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
    if (returnStyleSheet) {
        return styleSheetContent;
    }
}
// REMOVE FUNCTION
function removeDraggableGoal() {
    goalIsBeingCreated = false;
}
// SEND DATA FUNCTION
function sendGoalData() {
    goalIsBeingCreated = false;
    let xmlhttp = new XMLHttpRequest();
    let url = 'send-data.php?goalpos=' + goal_marker_pos + "&groupcode=" + groupCode;

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
    localStorage.clear();
    userPopupContent = [];
    goalRouteIsDrawn = false;
    map.removeLayer(goalLayerGroup);
    goalLayerGroup.eachLayer(function(layer) {goalLayerGroup.removeLayer(layer)});
}

// HANDLER EVENTS FOR MARKERS

/*function dragStartHandler(e) {
    let polyline = e.target.parentLine;
    if (polyline){
        let latlngPoly = polyline.getLatLngs(),     // Get the polyline's latlngs
        latlngMarker = this.getLatLng();        // Get the actual, cliked MARKER's start latlng
        for (let i = 0; i < latlngPoly.length; i++) {       // Iterate the polyline's latlngs
            if (latlngMarker.equals(latlngPoly[i])) {       // Compare marker's latlng ot the each polylines 
                this.polylineLatlng = i;            // If equals store key in marker instance
            }
        }
    }
} */

// Now you know the key of the polyline's latlng you can change it
// when dragging the marker on the dragevent:
function dragHandler(e) {
  /*  let polyline = e.target.parentLine;
    if (polyline){
        let latlngPoly = e.target.parentLine.getLatLngs(),    // Get the polyline's latlngs
        latlngMarker = this.getLatLng();            // Get the marker's current latlng
        latlngPoly.splice(this.polylineLatlng, 1, latlngMarker);        // Replace the old latlng with the new
        polyline.setLatLngs(latlngPoly);           // Update the polyline with the new latlngs
        */
        // We get the index of the marker by looking at the classname 'other-user-marker[index]'
        let markerClassNames = this._icon.className;
        let markerClasses = markerClassNames.split(" ");
        for (let i = 0; i < goal_marker_pos.length; i++) {
            if (markerClasses.includes("user-goal-marker-"+i)) {
                goal_marker_pos[i] = this.getLatLng();
            }
        }
   // }
}

// Just to be clean and tidy remove the stored key on dragend:
function dragEndHandler(e) {
    delete this.polylineLatlng;
}