<?php
require './../../autoloader.php';

        $user = new User();
        $groupCodes = $user->getGroupCodes();
        $uniqueIDs = $user->getUniqueIDs();

        print_r($uniqueIDs);
        echo "<br>";
        print_r($groupCodes);