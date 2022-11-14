<?php
require './../../autoloader.php';

        $user = new User();
        $groupCodes = $user->getGroupCodes();
        $user->groupCode = "DgG";
        $IDs = $user->getIDs();

        print_r($IDs);
        echo "<br>";
        print_r($groupCodes);