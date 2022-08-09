<?php
    // Man kan inte komma på sidan utan en gruppkod
    if (isset($_GET['groupcode'])) {
        require './../required-files/connection.php';
        $sql = "SELECT groupcode FROM groups";
        $result = mysqli_query($conn, $sql);
        $foundGroupCode = false;
        if (mysqli_num_rows($result) >= 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);
                if ($_GET['groupcode'] == $row['groupcode']) {
                    $foundGroupCode = true;
                }
            }
        }
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
        <?php require './../required-files/head.php'; ?>
        <link rel='icon' type='image/svg' href='./../media/'>
        <link href='./../styles/css/main.css' rel='stylesheet' type='text/css'>
        <link href='./../leaflet/leaflet.css' rel='stylesheet' type='text/css'/>
        <script src='./../leaflet/leaflet.js'></script>
        <script src='./geolocation.js' async></script>
        <title>Active group</title>
    </head>
    <body class='active-page'>
        <?php require './../required-files/header.php'; ?>

        <section>
            <article>
                <div id='map'></div>
            </article>
        </section>

        <?php require './../required-files/footer.php'; ?>
    </body>
</html>