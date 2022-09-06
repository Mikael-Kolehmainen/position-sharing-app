<?php
    require './../required-files/dbHandler.php';

    session_start();
    // Man kan inte komma på sidan utan en gruppkod
    if (isset($_GET['groupcode']) && isset($_SESSION['initials'])) 
    {
        $result = selectGroups();
        $foundGroupCode = false;
        $groupCode = filter_input(INPUT_GET, 'groupcode', FILTER_DEFAULT);

        if (mysqli_num_rows($result) > 0) 
        {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) 
            {
                $row = mysqli_fetch_assoc($result);
                if ($groupCode == $row['groupcode']) 
                {
                    $foundGroupCode = true;
                    $groupID = $row['id'];
                }
            }
        }
        if ($foundGroupCode == false) 
        {
            echo "
                <script>
                    alert('Couldn\'t find a group with the given code, try again.');
                    window.location.href = './../index.php';
                </script>
            ";
        }
    } 
    else if (isset($_GET['groupcode'])) 
    {
        echo "
            <script>
                alert('Please create a marker before joining a group.');
                window.location.href = './../group-system/search-form.php';
            </script>
        ";
    } 
    else 
    {
        echo "
            <script>
                alert('Couldn\'t find a group with the given code, try again.');
                window.location.href = './../group-system/search-form.php';
            </script>
        ";
    }

    function selectGroups()
    {
        return dbHandler::query("SELECT id, groupcode FROM groups");
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
        <script src='https://unpkg.com/@turf/turf@6/turf.min.js'></script>
        <script src='./../geojson/vaasa.geojson' type='text/javascript'></script>
        <script src='./../js/open.js' async></script>
        <script src='./../js/remove-children.js' async></script>
        <script src='./../js/remove-style.js' async></script>
        <script src='./../js/create-style.js' async></script>
        <script src='./../leaflet/leaflet.js'></script>
        <script src='./geolocation.js' async></script>
        <script src='./beforeunload.js' async></script>
        <script src='./chat.js' async></script>
        <script src='./percentage-moved.js' async></script>
        <script src='./drag-events.js' async></script>
        <script src='./add-waypoint.js' async></script>
        <script src='./show-water.js' async></script>
        <script src='./remove-goal.js' async></script>
        <script src='./create-goal.js' async></script>
        <title>Active group</title>
    </head>
    <body class='active-page'>
        <section>
            <article>
                <div class='top'>
                    <p>Group code:</p>
                    <p><?php echo $_GET['groupcode']; ?></p>
                </div>
                <div class='disclaimer' id='active-goal-disclaimer' style='display: none;' onclick='removeActiveGoal();'>
                    <p>There's an active goal</p>
                </div>
                <label class='switch'>
                    <input type='checkbox' id='water-checkbox' onclick='showWaterEntities();'>
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
                        <form method='POST' action='send-message.php?groupcode=<?php echo $_GET['groupcode']; ?>' class='textbox'>
                            <input type='text' name='message' placeholder='Please be kind' maxlength='255' required>
                            <input type='submit' value='' id='send-btn'>
                        </form>
                    </div>
                    <a class='btn small' onclick='openMenu("delete-btn", "delete-popup", "block");' id='delete-btn'>
                        <p>Delete group</p>
                    </a>
                    <a class='btn round' onclick='openMenu("goal-btn", "goal-options", "block", ["message-btn", "delete-btn"]);showDraggableGoal();' id='goal-btn' style='display: inline-block;'>
                        <i class='fa-solid fa-location-dot'></i>
                    </a>
                    <div class='options' style='display: none;' id='goal-options'>
                        <a class='btn' onclick='openMenu("goal-options", "goal-btn", "inline-block", ["message-btn", "delete-btn"]);removeDraggableGoal();'>
                            <i class='fa-solid fa-xmark'></i>
                        </a>
                        <a class='btn' onclick='openMenu("goal-options", "goal-btn", "block", ["message-btn", "delete-btn"]);sendGoalData();'>
                            <i class='fa-solid fa-check'></i>
                        </a>
                    </div>
                </div>
                <div class='popup' id='delete-popup' style='display: none;'>
                    <p>Are you sure you want to delete this group?</p>
                    <a class='btn' onclick='openMenu("delete-popup", "delete-btn", "inline-block");'>
                        <p>No</p>
                    </a>
                    <a class='btn' href='./../group-system/delete-group.php?groupcode=<?php echo $_GET['groupcode']; ?>'>
                        <p>Yes</p>
                    </a>
                </div>
            </article>
        </section>
    </body>
</html>