<?php
    session_start();
    // Man kan inte komma på sidan utan en gruppkod
    if (isset($_GET['groupcode']) && isset($_SESSION['initials'])) {
        require './../required-files/connection.php';
        $sql = "SELECT id, groupcode FROM groups";
        $result = mysqli_query($conn, $sql);
        $foundGroupCode = false;
        if (mysqli_num_rows($result) > 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);
                if ($_GET['groupcode'] == $row['groupcode']) {
                    $foundGroupCode = true;
                    $groupID = $row['id'];
                }
            }
        }
        mysqli_close($conn);
        if ($foundGroupCode == false) {
            echo "
                <script>
                    alert('Couldn\'t find a group with the given code, try again.');
                    window.location.href = './../index.php';
                </script>
            ";
        }
    } else if (isset($_GET['groupcode'])) {
        echo "
            <script>
                alert('Please create a marker before joining a group.');
                window.location.href = './../group-system/search-form.php';
            </script>
        ";
    } else {
        echo "
            <script>
                alert('Couldn\'t find a group with the given code, try again.');
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
        <script src='./../leaflet/leaflet.js'></script>
        <script src='./geolocation.js' async></script>
        <script src='./detection.js' asyncs></script>
        <script src='./../js/open.js' async></script>
        <title>Active group</title>
    </head>
    <body class='active-page'>
        <section>
            <article>
                <div class='top'>
                    <p>Group code:</p>
                    <p><?php echo $_GET['groupcode']; ?></p>
                </div>
                <div id='map'></div>
                <div class='bottom'>
                    <a class='btn round' id='message-btn' style='display: inline-block;' onclick='openMenu("message-btn", "chat", "block", ["goal-btn"])'>
                        <i class='fa-solid fa-message'></i>
                    </a>
                    <div class='chat' style='display: none;' id='chat'>
                        <div class='btn-container'>
                            <a class='btn round' onclick='openMenu("chat", "message-btn", "inline-block", ["goal-btn"])'>
                                <i class='fa-solid fa-xmark'></i>
                            </a>
                        </div>
                        <div class='messages' id='messages'>
                            
                        </div>
                        <form method='POST' action='send-message.php?groupcode=<?php echo $_GET['groupcode'] ?>' class='textbox'>
                            <input type='text' name='message' placeholder='Please be kind' maxlength='255' required>
                            <input type='submit' value='' id='send-btn'>
                        </form>
                    </div>
                    <a class='btn round' onclick='openMenu("goal-btn", "goal-options", "block", ["message-btn"]);showDraggableGoal();' id='goal-btn' style='display: inline-block;'>
                        <i class='fa-solid fa-location-dot'></i>
                    </a>
                    <div class='options' style='display: none;' id='goal-options'>
                        <a class='btn' onclick='openMenu("goal-options", "goal-btn", "inline-block", ["message-btn"]);'>
                            <i class='fa-solid fa-xmark'></i>
                        </a>
                        <a class='btn' onclick=''>
                            <i class='fa-solid fa-check'></i>
                        </a>
                    </div>
                </div>
            </article>
        </section>
    </body>
</html>