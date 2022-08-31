<?php
    if (isset($_GET['groupcode'])) {
        $positionsData = array();
        $positionsData['positions'] = array();
        $positionsData['initials'] = array();
        $positionsData['colors'] = array();

        $messagesData = array();
        $messagesData['messages'] = array();
        $messagesData['initials'] = array();
        $messagesData['colors'] = array();

        $goalsData = array();
        $goalsData['startpositions'] = array("empty");
        $goalsData['goalpositions'] = array("empty");

        // GET POSITIONS
        require './../required-files/connection.php';
        $sql = "SELECT position, initials, color, groups_groupcode FROM positions";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);
                if ($row['groups_groupcode'] == $_GET['groupcode']) {
                    array_push($positionsData['positions'] , $row['position']);
                    array_push($positionsData['initials'], $row['initials']);
                    array_push($positionsData['colors'], $row['color']);
                }
            }
        }
        mysqli_close($conn);

        // GET MESSAGES
        require './../required-files/connection.php';
        $sql = "SELECT message, initials, color, groups_groupcode FROM messages";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);
                if ($row['groups_groupcode'] == $_GET['groupcode']) {
                    array_push($messagesData['messages'], $row['message']);
                    array_push($messagesData['initials'], $row['initials']);
                    array_push($messagesData['colors'], $row['color']);
                }
            }
        }
        mysqli_close($conn);

        // GET GOALS
        require './../required-files/connection.php';
        $sql = "SELECT startpositions, goalpositions, groups_groupcode FROM goals";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);
                if ($row['groups_groupcode'] == $_GET['groupcode']) {
                    // Remove first 'LatLng' from string
                    $startPositions = substr($row['startpositions'], 6);
                    $startPositions = explode(",LatLng", $startPositions);
                    $goalsData['startpositions'] = $startPositions;
                    // Remove first 'LatLng' from string
                    $goalPositions = substr($row['goalpositions'], 6);
                    $goalPositions = explode(",LatLng", $goalPositions);
                    $goalsData['goalpositions'] = $goalPositions;
                }
            }
        }

        $data = array();
        $data['positionsdata'] = $positionsData;
        $data['messagesdata'] = $messagesData;
        $data['goalspositions'] = $goalsData;

        echo json_encode($data);
    }
?>