<?php
    session_start();
    if (isset($_GET['pos'])) {
        if (isset($_SESSION['uniqueID'])) {
            require './../required-files/connection.php';
            $sql = "SELECT id, uniqueID FROM positions";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                    $row = mysqli_fetch_assoc($result);
                    if ($_SESSION['uniqueID'] == $row['uniqueID']) {
                        $positionID = $row['id'];
                        $newPosition = $_GET['pos'];
                        $sql2 = "UPDATE positions SET position = '$newPosition' WHERE id = '$positionID'";
                        mysqli_query($conn, $sql2);
                    }
                }
            }
        } else {
            $position = $_GET['pos'];
            $uniqueID = getUniqueID();
            $_SESSION['uniqueID'] = $uniqueID;
            $initials = $_SESSION['initials'];
            $color = $_SESSION['color'];
            $groupCode = $_GET['groupcode'];

            require './../required-files/connection.php';

            $sql = "INSERT INTO positions (position, uniqueID, initials, color, groups_groupcode) VALUES ('$position', '$uniqueID', '$initials', '$color', '$groupCode')";

            mysqli_query($conn, $sql);
        }
    }
    // Kollar om unika id:et är duplikat
    function getUniqueID() {
        require './../required-files/connection.php';
        require './../required-files/random-string.php';
        $uniqueID = getRandomString(10);
        $sql = "SELECT uniqueID FROM positions";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);
                if ($uniqueID == $row['uniqueID']) {
                    $uniqueID = getUniqueID();
                }
            }
        }
        mysqli_close($conn);

        return $uniqueID;
    }
?>