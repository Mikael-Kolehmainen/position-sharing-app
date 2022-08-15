var map = L.map('map', {zoomControl: false});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '© OpenStreetMap'
}).addTo(map);

var current_position;
var counter = 0;
var data;

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

function onLocationFound(e) {

    if (current_position) {
        map.eachLayer(function (layer) {
            if (layer.options.attribution == null) {
                map.removeLayer(layer);
            }
        });
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
                var styleSheetContent =  "";
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
// setInterval(locate, 3000);

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
    // SHOW THE GOAL MARKERS IN SAME COLORS BUT LOWER OPACITY AND CONNECT A LINE BETWEEN ALL MARKERS

    const initialsArr = data.positionsdata.initials;
    const colorsArr = data.positionsdata.colors;

    let marker_arr = [];
    let marker_pos = [];

    let classNameGoalMarkers;
    let styleSheetContent = "";
    // CREATE THE POSITIONS
    for (var i = 0; i < initialsArr.length; i++) {
        marker_pos[i] = new L.LatLng(current_position.getLatLng().lat + 0.0001, current_position.getLatLng().lng + 0.0001);
    }

    for (var i = 0; i < initialsArr.length; i++) {
        marker_arr[i] = new L.Marker(marker_pos[i], {draggable: true, icon: otherUsersIcon}).addTo(map);

        classNameGoalMarkers = 'user-goal-marker-' + i;
        styleSheetContent += '.' + classNameGoalMarkers + '{ background-color: ' + colorsArr[i] + '; }';
        // INITIALS
        styleSheetContent += '.' + classNameGoalMarkers + '::before { content: ' + initialsArr[i] + '; }';

        marker_arr[i]._icon.classList.add(classNameGoalMarkers);
    }
    createStyle(styleSheetContent, 'js-style');
}