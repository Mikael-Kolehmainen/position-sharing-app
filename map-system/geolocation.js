var map = L.map('map', {zoomControl: false});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '© OpenStreetMap'
}).addTo(map);

var current_position;
var counter = 0;
var data;
var styleSheetContent =  "";

var userIcon = L.divIcon ({
    iconSize: [25, 25],
    iconAnchor: [12.5, 25],
    className: 'user-marker'
});
var otherUsersIcon = L.divIcon ({
    iconSize: [25, 25],
    iconAnchor: [12.5, 25],
    className: 'other-user-marker'
});

let goal_marker_arr = [];
let goal_marker_pos = [];

function onLocationFound(e) {

    if (current_position) {
        map.eachLayer(function (layer) {
            if (layer.options.attribution == null) {
                map.removeLayer(layer);
            }
        });
    }

    // SHOW GOAL MARKERS ON MAP IF THEY'RE DEFINED
    if (goal_marker_arr != undefined && goal_marker_arr.length != 0
        && goal_marker_pos != undefined && goal_marker_pos != 0) {
        
        let polyline = new L.Polyline(goal_marker_pos).addTo(map);
        const initialsArr = data.positionsdata.initials;
        const colorsArr = data.positionsdata.colors;
        
        for (var i = 0; i < goal_marker_arr.length; i++) {
            // MAKE A FUNCTION OF THIS AND CALL IT
            goal_marker_arr[i] = new L.Marker(goal_marker_pos[i], {draggable: true, icon: otherUsersIcon}).addTo(map);
            classNameGoalMarkers = 'user-goal-marker-' + i;
            styleSheetContent += '.' + classNameGoalMarkers + '{ background-color: ' + colorsArr[i] + '; border-radius: 0 !important;}';
            // INITIALS
            initial = '\"' + initialsArr[i] + '\"';
            styleSheetContent += '.' + classNameGoalMarkers + '::before { content: ' + initial + '; }';
            goal_marker_arr[i]._icon.classList.add(classNameGoalMarkers);

            // ASSIGN TO POLYLINE
            goal_marker_arr[i].parentLine = polyline;

            // ASSIGN EVENTHANDLERS TO MARKERS
            goal_marker_arr[i]
                .on('dragstart', dragStartHandler)
                .on('drag', dragHandler)
                .on('dragend', dragEndHandler);
        }
    }

    current_position = L.marker(e.latlng, {icon: userIcon}).addTo(map);
    // GET THE GROUPCODE FROM SEARCH FIELD
    const groupCode = new URLSearchParams(window.location.search).get('groupcode');
    // SEND POSITION DATA & GROUPCODE TO PHP
    var index = ['send-data', 'get-data'];
    var xmlhttp = new XMLHttpRequest();
    (function loop(i, length) {
        if (i >= length) {
            return;
        }
        var url = index[i] + ".php?pos=" + e.latlng + "&groupcode=" + groupCode;
        
        if (i == 1) {
            xmlhttp.onload = function() {
                // MARKERS
                removeStyles('js-style');
                data = JSON.parse(this.responseText);
                var positionsArr = data.positionsdata.positions;
                var initialsArr = data.positionsdata.initials;
                var colorsArr = data.positionsdata.colors;
                var classNameOtherUsers;
                for (var i = 0; i < positionsArr.length; i++) {
                    positionsArr[i] = positionsArr[i].replace(/[^\d.,-]/g,'');
                    latlngArr = positionsArr[i].split(",");
                    marker = L.marker(L.latLng(latlngArr[0], latlngArr[1]), {icon: otherUsersIcon}).addTo(map);
                    var initial = '\"' + initialsArr[i] + '\"';
                    if (marker.getLatLng().equals(current_position.getLatLng())) {
                        // REMOVES USERS OWN MARKER WHICH IS ALREADY ON THE MAP
                        map.removeLayer(marker);
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
                var messagesArr = data.messagesdata.messages;
                var initialsArr = data.messagesdata.initials;
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
                for (var i = 0; i < messagesArr.length; i++) {
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
    var head = document.head;
    var style = document.createElement('style');
    style.classList.add(className);

    if (style.stylesheet) {
        style.stylesheet = content;
    } else {
        style.appendChild(document.createTextNode(content));
    }
    head.appendChild(style);
}
function removeStyles(className) {
    var styles = document.getElementsByClassName(className);

    for (var i = 0; i < styles.length; i++) {
        styles[i].remove();
    }
}
const removeChilds = (parent) => {
    while (parent.lastChild) {
        parent.removeChild(parent.lastChild);
    }
};

// ONCLICK FUNCTIONS

function showDraggableGoal() {
    const initialsArr = data.positionsdata.initials;
    const colorsArr = data.positionsdata.colors;

    let classNameGoalMarkers;
    let initial;
    let polylineCords = [];
    let latlngValue = 0.002;
    // CREATE THE POSITIONS
    for (var i = 0; i < initialsArr.length; i++) {
        goal_marker_pos[i] = new L.LatLng(current_position.getLatLng().lat + latlngValue, current_position.getLatLng().lng + latlngValue);
        polylineCords.push(goal_marker_pos[i]);
        latlngValue = latlngValue + 0.002;
    }
    
    // POLYLINE BETWEEN ALL GOAL MARKERS
    let polyline = new L.Polyline(polylineCords).addTo(map);

    // CREATE MARKERS
    for (var i = 0; i < initialsArr.length; i++) {
        goal_marker_arr[i] = new L.Marker(goal_marker_pos[i], {draggable: true, icon: otherUsersIcon}).addTo(map);

        classNameGoalMarkers = 'user-goal-marker-' + i;
        styleSheetContent += '.' + classNameGoalMarkers + '{ background-color: ' + colorsArr[i] + '; border-radius: 0 !important;}';
        // INITIALS
        initial = '\"' + initialsArr[i] + '\"';
        styleSheetContent += '.' + classNameGoalMarkers + '::before { content: ' + initial + '; }';

        goal_marker_arr[i]._icon.classList.add(classNameGoalMarkers);

        // ASSIGN TO POLYLINE
        goal_marker_arr[i].parentLine = polyline;

        // ASSIGN EVENTHANDLERS TO MARKERS
        goal_marker_arr[i]
            .on('dragstart', dragStartHandler)
            .on('drag', dragHandler)
            .on('dragend', dragEndHandler);
    }
    createStyle(styleSheetContent, 'js-style');
}

// HANDLER EVENTS FOR MARKERS

function dragStartHandler(e) {
    var polyline = e.target.parentLine;
    if (polyline){
        var latlngPoly = polyline.getLatLngs(),     // Get the polyline's latlngs
        latlngMarker = this.getLatLng();        // Get the actual, cliked MARKER's start latlng
        for (var i = 0; i < latlngPoly.length; i++) {       // Iterate the polyline's latlngs
            if (latlngMarker.equals(latlngPoly[i])) {       // Compare marker's latlng ot the each polylines 
                this.polylineLatlng = i;            // If equals store key in marker instance
            }
        }
    }
}

// Now you know the key of the polyline's latlng you can change it
// when dragging the marker on the dragevent:
function dragHandler(e) {
    var polyline = e.target.parentLine;
    if (polyline){
        var latlngPoly = e.target.parentLine.getLatLngs(),    // Get the polyline's latlngs
        latlngMarker = this.getLatLng();            // Get the marker's current latlng
        latlngPoly.splice(this.polylineLatlng, 1, latlngMarker);        // Replace the old latlng with the new
        polyline.setLatLngs(latlngPoly);           // Update the polyline with the new latlngs
        
        // We get the index of the marker by looking at the classname 'other-user-marker[index]'
        let markerClassNames = this._icon.className;
        let markerClasses = markerClassNames.split(" ");
        for (var i = 0; i < goal_marker_pos.length; i++) {
            if (markerClasses.includes("user-goal-marker-"+i)) {
                goal_marker_pos[i] = this.getLatLng();
            }
        }
    }
}

// Just to be clean and tidy remove the stored key on dragend:
function dragEndHandler(e) {
    delete this.polylineLatlng;
}