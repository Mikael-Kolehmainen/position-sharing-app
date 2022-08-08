<?php
    $servername = "localhost";
    $dbusername = "root";
    $password = "";
    $dbname = "positionappDB";

    $conn = mysqli_connect($servername, $dbusername, $password, $dbname);

    if (!$conn){
        echo "connection failed" . mysqli_connect_error();
    }
?>