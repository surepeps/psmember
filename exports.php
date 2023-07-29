<?php

require('assets/init.php');


$file = filter('f');

if ($file) {

    $dir = "export/{$file}.php";
    if (file_exists($dir)) {
        require($dir);

        exit;
    }
} else {
    pre("file not found");
    exit;
}
