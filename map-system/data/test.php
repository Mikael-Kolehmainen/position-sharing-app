<?php
require './../../autoloader.php';

        
        $group = new Group('09A');
        $rowCount = $group->getRowCount();

        print_r($rowCount);