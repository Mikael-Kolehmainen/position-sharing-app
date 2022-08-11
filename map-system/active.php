<?php
    // Man kan inte komma på sidan utan en gruppkod
    if (isset($_GET['groupcode'])) {
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
    } else {
        echo "
            <script>
                alert('Couldn\'t find a group with the given code, try again.');
                window.location.href = './../index.php';
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
                    <a class='btn round' id='message-btn' style='display: block;' onclick='openMenu(this, document.getElementById("chat"))'>
                        <i class='fa-solid fa-message'></i>
                    </a>
                    <div class='chat' style='display: none;' id='chat'>
                        <a class='btn round' onclick='openMenu(document.getElementById("chat"), document.getElementById("message-btn"))'>
                            <i class='fa-solid fa-xmark'></i>
                        </a>
                        <div class='messages'>
                            <!-- PLACEHOLDER -->
                            <div class='message'>
                                <div class='profile'>
                                    <p>MK</p>
                                </div>
                                <p class='text'>Hello, this is a placeholder message.</p>
                            </div>
                            <div class='message'>
                                <div class='profile'>
                                    <p>MK</p>
                                </div>
                                <p class='text'>Hello, this is a placeholder message.</p>
                            </div>
                            <div class='message'>
                                <div class='profile'>
                                    <p>MK</p>
                                </div>
                                <p class='text'>Hello, this is a placeholder message.</p>
                            </div>
                            <div class='message'>
                                <div class='profile'>
                                    <p>MK</p>
                                </div>
                                <p class='text'>Hello, this is a placeholder message.</p>
                            </div>
                            <div class='message'>
                                <div class='profile'>
                                    <p>MK</p>
                                </div>
                                <p class='text'>Hello, this is a placeholder message.</p>
                            </div>
                        </div>
                        <form action='' method='POST' class='textbox'>
                            <input type='text' name='message' placeholder='Please be kind'>
                            <input type='submit' value='' id='send-btn'>
                        </form>
                    </div>
                </div>
            </article>
        </section>

    </body>
</html>