<?php
    require './../required-files/dbHandler.php';
    require './../required-files/constants.php';
    require './../db/Group.php';
    require './../db/User.php';

    session_start();

    if (isset($_POST['create-group'])) {
        $group = new Group();
        $groupCode = $group->groupCode;
        
        $group->save();
        saveMarkerToSession();
        $group->redirectUserToGroupMap();
    } else if (isset($_POST['search-group'])) {
        $groupCode = filter_input(INPUT_POST, GROUPCODE, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        if (findGroupInDatabase($groupCode)) {
            saveMarkerToSession();

            $group = new Group($groupCode);
            $group->redirectUserToGroupMap();
        } else {
            redirectUserToSearchGroupForm();
        }
    } else if (!isset($_GET[GROUPCODE]) || !isset($_SESSION[INITIALS]) || !isset($_SESSION[COLOR])) {
        redirectUserToSearchGroupForm();
    }

    function saveMarkerToSession() 
    {
        $user = new User();
        
        $user->saveMarkerToSession();
    }

    function findGroupInDatabase($groupCode)
    {
        $group = new Group($groupCode);

        return $group->getRowCount();
    }

    function redirectUserToSearchGroupForm()
    {
        echo "
                <script>
                    alert('Couldn\'t find a group with the given code or you need to create a marker.');
                    window.location.href = './../group-system/search-form.php';
                </script>
            ";
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link href='./../styles/css/markers.css' rel='stylesheet' type='text/css'>
        <?php require './../required-files/head.php'; ?>
        <link rel='icon' type='image/svg' href='./../media/'>
        <link href='./../styles/css/main.css' rel='stylesheet' type='text/css'>
        <link href='./../leaflet/leaflet.css' rel='stylesheet' type='text/css'/>
        <script src='./../js/turf.min.js'></script>
        <script src='./../geojson/vaasa.geojson' type='text/javascript'></script>
        <script src='./../js/open.js' defer></script>
        <script src='./../js/remove-children.js' defer></script>
        <script src='./../js/Style.js' defer></script>
        <script src='./../js/remove-style.js' defer></script>
        <script src='./../js/create-style.js' defer></script>
        <script src='./../leaflet/leaflet.js'></script>
        <script src='./geolocation.js' defer></script>
        <script src='./beforeunload.js' defer></script>
        <script src='./chat/Message.js' defer></script>
        <script src='./chat/Chat.js' defer></script>
        <script src='./goal/Goal.js' defer></script>
        <script src='./goal/percentage-moved.js' defer></script>
        <script src='./goal/drag-events.js' defer></script>
        <script src='./goal/waypoint/waypoint-lines.js' defer></script>
        <script src='./goal/waypoint/add-waypoint.js' defer></script>
        <script src='./goal/waypoint/remove-waypoint.js' defer></script>
        <script src='./goal/remove-goal.js' defer></script>
        <script src='./goal/distance.js' defer></script>
        <script src='./goal/onclick-events.js' defer></script>
        <script src='./water-switch/show-water.js' defer></script>
        <title>Active group</title>
    </head>
    <body class='active-page'>
        <section>
            <article>
                <div class='top'>
                    <p>Group code:</p>
                    <p><?php echo $_GET[GROUPCODE]; ?></p>
                </div>
                <div class='disclaimer' id='active-goal-disclaimer' style='display: none;' onclick='removeActiveGoal();'>
                    <p>There's an active goal</p>
                </div>
                <label class='switch'>
                    <input type='checkbox' id='water-switch'>
                    <span class='slider'></span>
                </label>
                <div id='map'></div>
                <div class='bottom'>
                    <a class='btn round' id='message-btn' style='display: inline-block;' onclick='openMenu("message-btn", "chat", "block", ["goal-btn", "delete-btn"])'>
                        <i class='fa-solid fa-message'></i>
                    </a>
                    <div class='chat' style='display: none;' id='chat'>
                        <div class='btn-container'>
                            <a class='btn round' onclick='openMenu("chat", "message-btn", "inline-block", ["goal-btn", "delete-btn"])'>
                                <i class='fa-solid fa-xmark'></i>
                            </a>
                        </div>
                        <div class='messages' id='messages'>
                            
                        </div>
                        <form method='POST' action='chat/send-message.php?groupcode=<?php echo $_GET[GROUPCODE]; ?>' class='textbox'>
                            <input type='text' name='message' placeholder='Please be kind' maxlength='255' required>
                            <input type='submit' value='' id='send-btn'>
                        </form>
                    </div>
                    <a class='btn small' onclick='openMenu("delete-btn", "delete-popup", "block");' id='delete-btn'>
                        <p>Delete group</p>
                    </a>
                    <a class='btn round' onclick='openMenu("goal-btn", "goal-popup", "block");' id='goal-btn' style='display: inline-block;'>
                        <i class='fa-solid fa-location-dot'></i>
                    </a>
                    <div class='options' style='display: none;' id='goal-options'>
                        <a class='btn' onclick='openMenu("goal-options", "goal-btn", "inline-block", ["message-btn", "delete-btn"]);removeDraggableGoal();'>
                            <i class='fa-solid fa-xmark'></i>
                        </a>
                        <div class='distance'>
                            <p>Distance</p>
                            <input type='number' class='btn number-input' id='distance-number' onchange='applyDistance()' min='0' max='99' placeholder='99'>
                        </div>
                        <a class='btn' id='send-goal-data' onclick='openMenu("goal-options", "goal-btn", "block", ["message-btn", "delete-btn"]);'>
                            <i class='fa-solid fa-check'></i>
                        </a>
                        <a class='btn small' onclick='removeWaypoint();'>
                            <p>Remove waypoint</p>
                        </a>
                    </div>
                </div>
                <div class='popup' id='delete-popup' style='display: none;'>
                    <p>Are you sure you want to delete this group?</p>
                    <a class='btn' onclick='openMenu("delete-popup", "delete-btn", "inline-block");'>
                        <p>No</p>
                    </a>
                    <a class='btn' href='./../group-system/delete-group.php?groupcode=<?php echo $_GET[GROUPCODE]; ?>'>
                        <p>Yes</p>
                    </a>
                </div>
                <div class='popup' id='goal-popup' style='display: none;'>
                    <p>Choose which users get a goal?</p>
                    <table id='users-table'>
                    </table>
                    <a class='btn' onclick='openMenu("goal-popup", "goal-btn", "block");'>
                        <p>No</p>
                    </a>
                    <a class='btn' id='show-draggable-goal' onclick='openMenu("goal-popup", "goal-options", "block", ["message-btn", "delete-btn"]);'>
                        <p>Yes</p>
                    </a>
                </div>
            </article>
        </section>
    </body>
</html>