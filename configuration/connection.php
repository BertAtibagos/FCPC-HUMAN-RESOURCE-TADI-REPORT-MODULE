<?php
    require('config.php');

    $dbPortal = new mysqli($servername, $serverusername, $serverpassword, $serverdb, $serverport);

    if ($dbPortal->connect_error) {
        die("Connection Failed: " . $dbPortal->connect_error);
    }

    $dbPortal->set_charset('utf8mb4');
?>