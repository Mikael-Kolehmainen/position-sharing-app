<?php

function autoLoader($className)
{
    $file = __DIR__ . '/required-files/classes/' . $className . '.php';
    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('autoLoader');
