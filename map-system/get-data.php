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

        $data = array();
        $data['positionsdata'] = $positionsData;
        $data['messagesdata'] = $messagesData;

        echo json_encode($data);
    }
?>