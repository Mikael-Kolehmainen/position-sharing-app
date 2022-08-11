<!-- För användaren till gruppkartan om det finns en grupp med angivna gruppkoden. -->
<?php
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['groupcode'])) {
        require './../required-files/connection.php';
        $sql = "SELECT groupcode FROM groups";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) >= 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);
                if ($_POST['groupcode'] == $row['groupcode']) {
                    $groupCode = $row['groupcode'];
                }
            }
        }
        mysqli_close($conn);
        if (isset($groupCode)) {
            saveAvatar($_POST['initials'], $_POST['color']);
            header("LOCATION: ./../map-system/active.php?groupcode=$groupCode");
        } else {
            echo "
                <script>
                    alert('Couldn\'t find a group with the given code, try again.');
                    window.location.href = './search-form.php';
                </script>
            ";
        }
    } else if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['initials'])) {
        saveAvatar($_POST['initials'], $_POST['color']);
        header("LOCATION: ./create.php");
    }

    function saveAvatar($initials, $color) {
        session_start();
        if ($color == "") {
            $color = "#FF0000";
        }
        $initials = strtoupper($initials);
        $_SESSION['initials'] = $initials;
        $_SESSION['color'] = $color;
    }
?>