<?php
    require './../required-files/constants.php';
    require './../autoloader.php';

    session_start();

    if (isset($_POST[CREATE_GROUP])) {
        $group = new Group();
        $groupCode = $group->groupCode;
        
        $group->save();
        saveMarkerToSession();
        $group->redirectUserToGroupMap();
    } else if (isset($_POST[SEARCH_GROUP])) {
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
        <?php require './../required-files/head.php'; ?>
<!--        <link rel='icon' type='image/svg' href='./../media/'> -->
        <link href='./../styles/css/main.css' rel='stylesheet' type='text/css'>
        <link href='./../leaflet/leaflet.css' rel='stylesheet' type='text/css'/>
        <script src='./../js/turf.min.js'></script>
        <script src='./../geojson/vaasa.geojson' type='text/javascript'></script>
        <script src='./../js/open.js' defer></script>
        <script src='./../js/remove-children.js' defer></script>
        <script src='./../js/ElementDisplay.js' defer></script>
        <script src='./../js/Style.js' defer></script>
        <script src='./../js/LayerManagement.js' defer></script>
        <script src='./../leaflet/leaflet.js'></script>
        <script src='./../leaflet/leaflet.geometryutil.js'></script>
        <script src='./global-objects.js' defer></script>
        <script src='./geolocation.js' defer></script>
        <script src='./map.js' defer></script>
        <script src='./before-closing.js' defer></script>
        <script src='./user/User.js' defer></script>
        <script src='./chat/Message.js' defer></script>
        <script src='./chat/Chat.js' defer></script>
        <script src='./../js/onclick-events.js' defer></script>
        <script src='./data/Data.js' defer></script>
        <script src='./goal/Goal.js' defer></script>
        <script src='./goal/Instructions.js' defer></script>
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
                <div class='disclaimer onclick' id='active-goal-disclaimer' style='display: none;'>
                    <p>There's an active goal</p>
                </div>
                <label class='switch'>
                    <input type='checkbox' class='onclick' id='water-switch'>
                    <span class='slider'></span>
                </label>
                <div id='map'></div>
                <div class='instructions'>
                    <p style='display: none;' id='instruction-text'>Instruction text</p>
                </div>
                <div class='bottom'>
                    <a class='btn round onclick' id='open-chat-btn' style='display: inline-block;'>
                        <i class='fa-solid fa-message'></i>
                    </a>
                    <div class='chat' style='display: none;' id='chat'>
                        <div class='btn-container'>
                            <a class='btn round onclick' id='close-chat-btn'>
                                <i class='fa-solid fa-xmark'></i>
                            </a>
                        </div>
                        <div class='messages' id='messages'>
                            
                        </div>
                        <form method='POST' action='./chat/send-message.php?groupcode=<?php echo $_GET[GROUPCODE]; ?>' class='textbox'>
                            <input type='text' name='message' placeholder='Please be kind' maxlength='255' required>
                            <a href='./../camera-system/camera.php?groupcode=<?php echo $_GET[GROUPCODE]; ?>' class='camera-btn'></a>
                            <input type='submit' value='' id='send-btn'>
                        </form>
                    </div>
                    <a class='btn small onclick' id='delete-group-btn'>
                        <p>Delete group</p>
                    </a>
                    <a class='btn round onclick' id='add-goal-btn' style='display: inline-block;'>
                        <i class='fa-solid fa-location-dot'></i>
                    </a>
                    <div class='options' style='display: none;' id='goal-options'>
                        <a class='btn onclick' id='remove-draggable-goal'>
                            <i class='fa-solid fa-xmark'></i>
                        </a>
                        <a class='btn onclick' id='confirm-goal-positions-btn'>
                            <i class='fa-solid fa-check'></i>
                        </a>
                    </div>
                    <div class='options' style='display: none;' id='goal-route-options'>
                        <a class='btn onclick' id='remove-draggable-goal'>
                            <i class='fa-solid fa-xmark'></i>
                        </a>
                        <a class='btn onclick' id='confirm-route-btn'>
                            <i class='fa-solid fa-check'></i>
                        </a>
                    </div>
                    <a class='btn round onclick center small-circle' id='check-map-legends-btn' style='display: inline-block;'>
                        <i class='fa-solid fa-layer-group'></i>
                    </a>
                </div>
                <div class='popup' id='delete-popup' style='display: none;'>
                    <p>Are you sure you want to delete this group?</p>
                    <a class='btn onclick' id='reject-group-delete-btn'>
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
                    <a class='btn onclick' id='reject-add-goal-btn'>
                        <p>No</p>
                    </a>
                    <a class='btn onclick' id='show-draggable-goal'>
                        <p>Yes</p>
                    </a>
                </div>
                <div class='popup' id='map-legends-popup' style='display: none;'>
                    <div class='btn-container'>
                        <a class='btn round onclick' id='close-map-legends-btn'>
                            <i class='fa-solid fa-xmark'></i>
                        </a>
                    </div>
                    <p>Map legends</p>
                    <table>
                        <tr>
                            <td>
                                <div class='user-marker map-legend'>
                                    <p>initials</p>
                                </div>
                            </td>
                            <td>
                                <p>User Marker</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class='start-marker map-legend'>
                                    <p>initials</p>
                                </div>
                            </td>
                            <td>
                                <p>Start Marker</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class='goal-marker map-legend'>
                                    <p>initials</p>
                                </div>
                            </td>
                            <td>
                                <p>Goal Marker</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class='route-polyline map-legend'></div>
                            </td>
                            <td>
                                <p>Route Line</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </article>
        </section>
    </body>
</html>