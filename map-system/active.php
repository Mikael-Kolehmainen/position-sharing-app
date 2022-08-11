<?php
    // Man kan inte komma på sidan utan en gruppkod
    if (isset($_GET['groupcode'])) {
        require './../required-files/connection.php';
        $sql = "SELECT id, groupcode, members FROM groups";
        $result = mysqli_query($conn, $sql);
        $foundGroupCode = false;
        if (mysqli_num_rows($result) > 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);
                if ($_GET['groupcode'] == $row['groupcode']) {
                    $foundGroupCode = true;
                    $amountOfMembers = $row['members'];
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
        } else {
            // Lägg till en aktiv medlem då användaren anländer på sidan
            require './../required-files/connection.php';
            $amountOfMembers = $amountOfMembers + 1;
            $sql = "UPDATE groups SET members = $amountOfMembers WHERE id = $groupID";
            if (!mysqli_query($conn, $sql)) {
                echo "
                    <script>
                        alert('Something went wrong with updating the amount of active members, try again.');
                        window.location.href = './../index.php';
                    </script>
                ";
            }
            mysqli_close($conn);
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
        <?php require './../required-files/head.php'; ?>
        <link rel='icon' type='image/svg' href='./../media/'>
        <link href='./../styles/css/main.css' rel='stylesheet' type='text/css'>
        <link href='./../leaflet/leaflet.css' rel='stylesheet' type='text/css'/>
        <script src='./../leaflet/leaflet.js'></script>
        <script src='./geolocation.js' async></script>
        <script src='./detection.js' asyncs></script>
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
                    <a class='btn round' style='display: none;'>
                        <i class='fa-solid fa-message'></i>
                    </a>
                    <div class='chat' style='display: block;'>
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