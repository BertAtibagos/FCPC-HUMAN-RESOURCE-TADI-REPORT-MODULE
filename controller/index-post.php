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

    $stmt = $dbPortal->prepare($qry);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetch = $result->fetch_assoc();
    $stmt->close();
    $dbPortal->close();
}

if($type == 'GET_TOTAL_PER_MONTH'){

    $qry = "SELECT 
                MONTHNAME(`schltadi_date`) AS month_name,
                SUM(schltadi_status = 1) AS verified,
                SUM(schltadi_status = 0) AS unverified,
                COUNT(*) AS total
            FROM schooltadi
            GROUP BY MONTH(`schltadi_date`)
            ORDER BY MONTH(`schltadi_date`)";
    
    $stmt = $dbPortal->prepare($qry);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetch = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $dbPortal->close();
}

if($type == 'GET_TOTAL_PER_CUTOFF'){

    $qry = "SELECT 
                CONCAT(MONTHNAME(`schltadi_date`), ' ', 
                    CASE 
                        WHEN DAY(`schltadi_date`) <= 15 THEN '1-15'
                        ELSE CONCAT('16-', DAY(LAST_DAY(`schltadi_date`)))
                    END) AS cutoff_period,
                SUM(schltadi_status = 1) AS verified,
                SUM(schltadi_status = 0) AS unverified,
                COUNT(*) AS total
            FROM schooltadi
            GROUP BY YEAR(`schltadi_date`), MONTH(`schltadi_date`),
                    CASE 
                        WHEN DAY(`schltadi_date`) <= 15 THEN 1
                        ELSE 2
                    END
            ORDER BY YEAR(`schltadi_date`), MONTH(`schltadi_date`),
                    CASE 
                        WHEN DAY(`schltadi_date`) <= 15 THEN 1
                        ELSE 2
                    END";
    
    $stmt = $dbPortal->prepare($qry);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetch = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $dbPortal->close();
}

echo json_encode($fetch);
?>