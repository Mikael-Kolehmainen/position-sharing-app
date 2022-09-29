let map = L.map('map', {zoomControl: false});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '© OpenStreetMap'
}).addTo(map);

let refreshedLayerGroup = L.layerGroup();
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

let counter = 0;

let start_marker_arr = [];
let start_marker_pos = [];

let user_markers = [];
let userPopupContent = [];

let idsOfGoals = [];
let goal_marker_arr = [];
let goal_marker_pos = [];
let goalRouteIsDrawn = false;
let goalIsBeingPlanned = false;
let goal_waypoints = [];

let goalLayerGroup = L.layerGroup();
let draggableRouteLayerGroup = L.layerGroup();
let goalWaypointsLayerGroup = L.layerGroup();

let goal, chat;

window.onload = function()
{
    goal = new Goal();
    chat = new Chat();
}

function onLocationFound(e) 
{
    map.removeLayer(refreshedLayerGroup);

    const sendData = new Data("data/send-position.php?lat=" + e.latlng.lat + "&lng=" + e.latlng.lng + "&groupcode=" + groupCode);

    sendData.sendToPHP(function() 
    {
        const getData = new Data("data/get-data.php?groupcode=" + groupCode);

        getData.getFromPHP(function(data) {
            const userMarkerStyle = new Style('user-marker-style');
            userMarkerStyle.removeStyle();

            user_markers = [];
            usersData = data.usersdata;
            let classNameOtherUsers;
            let markerStyleSheetContent = "";
            for (let i = 0; i < usersData.length; i++) {
                marker = L.marker(L.latLng(usersData[i].position), {icon: userIcon});
                refreshedLayerGroup.addLayer(marker);
                user_markers.push(marker);
                let initials = '\"' + usersData[i].initials + '\"';

                // GIVES COLOR & INITIALS TO OTHER MARKERS
                classNameOtherUsers = 'user-marker-' + i;
                markerStyleSheetContent += '.' + classNameOtherUsers + '{ background-color: ' + usersData[i].color + '; }';
                // INITIALS
                markerStyleSheetContent += '.' + classNameOtherUsers + '::before { content: ' + initials + '; }';

                marker._icon.classList.add(classNameOtherUsers);
            }
            userMarkerStyle.styleSheetContent = markerStyleSheetContent;
            userMarkerStyle.createStyle();

            chat.messagesData = data.messagesdata;
            chat.updateChat();
            
            // GOALS
            const goalsData = data.goalsdata;
            goal.goalsData = data.goalsdata;
            goal.usersData = data.usersdata;
            goal.current_position = e.latlng;

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
                goal.createGoalLine(false, false);

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
        });
    });

    refreshedLayerGroup.addTo(map);
}

function onLocationError(e) 
{
    alert(e.message);
}

map.on('locationfound', onLocationFound);
map.on('locationerror', onLocationError);

function locate() 
{
    if (counter == 0) {
        map.locate({setView: true, enableHighAccuracy: true});
        counter = 1;
    } else if (counter == 1) {
        map.locate({setView: false, enableHighAccuracy: true});
    }
}

locate();
setInterval(locate, 3000);