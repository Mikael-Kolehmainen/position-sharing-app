<?php
    if (isset($_GET['groupcode'])) {
        // Kollar hur många aktiva medlemmar sedan tar bort en
        require './../required-files/connection.php';
        $sql = "SELECT id, groupcode, members FROM groups";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) >= 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);
                if ($_GET['groupcode'] == $row['groupcode']) {
                    $groupID = $row['id'];
                    $amountOfMembers = $row['members'];
                }
            }
        }
        mysqli_close($conn);
        require './../required-files/connection.php';
        $amountOfMembers = $amountOfMembers - 1;
        $sql = "UPDATE groups SET members = $amountOfMembers WHERE id = $groupID";
        if (!mysqli_query($conn, $sql)) {
            echo "
                <script>
                    alert('Something went wrong with removing the amount of active members, try again.');
                    window.location.href = './../index.php';
                </script>
            ";
        }
        mysqli_close($conn);
    }
?>