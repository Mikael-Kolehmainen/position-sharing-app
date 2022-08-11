<?php
    if (isset($_GET['groupcode'])) {
        require './../required-files/connection.php';
        $sql = "SELECT position, initials, color, groups_groupcode FROM positions";
        $result = mysqli_query($conn, $sql);
        $positions = array();
        $initials = array();
        $colors = array();
        if (mysqli_num_rows($result) > 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);
                if ($row['groups_groupcode'] == $_GET['groupcode']) {
                    array_push($positions, $row['position']);
                    array_push($initials, $row['initials']);
                    array_push($colors, $row['color']);
                }
            }
        }
        $data = array();
        $data['positions'] = $positions;
        $data['initials'] = $initials;
        $data['colors'] = $colors;

        echo json_encode($data);
    }
?>