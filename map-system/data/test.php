<?php
require './../../autoloader.php';

        $user = new User();
        $groupCodes = $user->getGroupCodes();
        $uniqueIDs = $user->getUniqueIDs();
        $user->groupCode = "08A";
        $IDs = $user->getIDs();

        print_r($IDs);