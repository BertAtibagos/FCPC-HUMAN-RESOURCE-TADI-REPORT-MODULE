<?php
require("../configuration/connection.php");
session_start();

$type = $_POST['type'];

if($type == 'GET_ALL_TOTAL'){

    $qry="SELECT COUNT(*) total_rec,
        (SELECT COUNT(*)
        FROM `schooltadi`
        WHERE `schltadi_status` = 1) verified,
        (SELECT COUNT(*)
        FROM schooltadi
        WHERE `schltadi_status` = 0) unverified
        FROM schooltadi";
        
}
?>