<?php
    function cleanString($string) {

        $string = str_replace("'", "", $string);

        return $string;
    }
?>