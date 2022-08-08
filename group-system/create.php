<!-- Lagar en grupp och kollar att gruppkoden är unik sedan för användare till gruppkartan med gruppkoden. -->
<?php
    createGroup();

    function createGroup() {
        require './../required-files/random-string.php';
        
        $groupCode = getRandomString(10);

        require './../required-files/connection.php';
        $sql = "SELECT groupcode FROM groups";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_assoc($result);
                if ($groupCode == $row['groupcode']) {
                    createGroup();
                }
            }
        }

        insertGroup($groupCode);

        mysqli_close($conn, $sql);
    }

    function insertGroup($groupCode) {
        require './../required-files/connection.php';
        $sql = "INSERT INTO groups (groupcode) VALUES ('$groupCode')";

        if (mysqli_query($conn, $sql)) {
            header("LOCATION: ./active.php?groupcode=$groupCode");
        } else {
            echo "
                <script>
                    alert('Something went wrong with group creation.');
                    window.location.href = './../index.php';
                </script>
            ";
        }
        mysqli_close($conn, $sql);
    }
?>