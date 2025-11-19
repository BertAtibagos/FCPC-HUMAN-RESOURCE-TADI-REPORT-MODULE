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

if($type == 'GET_ALL_PROG_TOTAL'){

    $qry = "SELECT
            dept.`SchlDept_NAME` AS program_name,
            COUNT(tadi.`schltadi_id`) AS total_records,
            SUM(
                CASE
                WHEN tadi.`schltadi_status` = 1 
                THEN 1 
                ELSE 0 
                END
            ) AS verified_count,
            SUM(
                CASE
                WHEN tadi.`schltadi_status` = 0 
                THEN 1 
                ELSE 0 
                END
            ) AS unverified_count,
            ROUND(
                (
                SUM(
                    CASE
                    WHEN tadi.`schltadi_status` = 1 
                    THEN 1 
                    ELSE 0 
                    END
                ) / COUNT(tadi.`schltadi_id`)
                ) * 100,
                2
            ) AS verification_rate,
            COUNT(DISTINCT emp.`SchlEmpSms_ID`) AS total_instructors,
            COUNT(
                DISTINCT off.`SchlEnrollSubjOffSms_ID`
            ) AS total_subjects 
            FROM
            schooltadi tadi 
            LEFT JOIN schoolstudent stud 
                ON tadi.`schlstud_id` = stud.`SchlStudSms_ID` 
            LEFT JOIN schoolenrollmentsubjectoffered off 
                ON tadi.`schlenrollsubjoff_id` = off.`SchlEnrollSubjOffSms_ID` 
            LEFT JOIN schoolacademiccourses crse 
                ON off.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID` 
            LEFT JOIN schooldepartment dept 
                ON crse.`SchlDept_ID` = dept.`SchlDeptSms_ID` 
            LEFT JOIN schoolemployee emp  
                ON tadi.`schlprof_id` = emp.`SchlEmpSms_ID` 
            WHERE off.`SchlAcadLvl_ID` = 2 
            AND off.`SchlAcadYr_ID` = 19 
            AND off.`SchlAcadPrd_ID` = 5  
            GROUP BY dept.`SchlDept_NAME` 
            ORDER BY unverified_count DESC ";

    $stmt = $dbPortal->prepare($qry);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetch = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $dbPortal->close();

}

if($type == 'GET_TADI_DETAILS_BY_CUTOFF'){
    
    $cutoff = isset($_POST['cutoff']) ? $_POST['cutoff'] : 'previous'; // 'current' or 'previous'
    $schlAcadLvl_ID = isset($_POST['level']) ? $_POST['level'] : 2;
    $schlAcadYr_ID = isset($_POST['year']) ? $_POST['year'] : 19;
    $schlAcadPrd_ID = isset($_POST['period']) ? $_POST['period'] : 5;
    $schlAcadYrLvl_ID = isset($_POST['year_level']) ? $_POST['year_level'] : 6;

    // Calculate current and previous cut-off dates
    $today = date('Y-m-d');
    $current_day = date('d');
    $current_month = date('m');
    $current_year = date('Y');

    // Determine current cut-off period
    if ($current_day <= 15) {
        $current_cutoff_start = date('Y-m-01');
        $current_cutoff_end = date('Y-m-15');
        
        // Previous cut-off is 16-end of previous month
        $prev_month = date('Y-m-d', strtotime('-1 month', strtotime($current_month . '-01')));
        $prev_cutoff_start = date('Y-m-16', strtotime($prev_month));
        $prev_cutoff_end = date('Y-m-t', strtotime($prev_month));
    } else {
        $current_cutoff_start = date('Y-m-16');
        $current_cutoff_end = date('Y-m-t');
        
        // Previous cut-off is 1-15 of current month
        $prev_cutoff_start = date('Y-m-01');
        $prev_cutoff_end = date('Y-m-15');
    }

    if ($cutoff == 'previous') {
        $date_start = $prev_cutoff_start;
        $date_end = $prev_cutoff_end;
    } else {
        $date_start = $current_cutoff_start;
        $date_end = $current_cutoff_end;
    }

    $qry = "SELECT  
				CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) AS prof_name,
				subj.`SchlAcadSubj_CODE` AS subject_code,
				subj.`SchlAcadSubj_DESC` AS subject_desc,
				sec.`SchlAcadSec_NAME` AS section_name,
				tadi.`schltadi_id`,
				tadi.`schltadi_date` AS tadi_date,
				tadi.`schltadi_timein` AS time_in,
				tadi.`schltadi_timeout` AS time_out,
				TIMEDIFF(tadi.schltadi_timeout, tadi.schltadi_timein) AS duration,
				tadi.`schltadi_mode` AS mode,
				tadi.`schltadi_type` AS type,
				tadi.`schltadi_activity` AS activity,
				tadi.`schltadi_status` AS status,
				CONCAT(info.`SchlEnrollRegStudInfo_LAST_NAME`, ', ', info.`SchlEnrollRegStudInfo_FIRST_NAME`) AS student_name

			FROM schooltadi tadi

			LEFT JOIN schoolstudent stud
				ON tadi.`schlstud_id` = stud.`SchlStudSms_ID`
			LEFT JOIN schoolenrollmentregistrationstudentinformation info
				ON stud.`SchlEnrollRegColl_ID` = info.`SchlEnrollReg_ID`
			LEFT JOIN schoolenrollmentsubjectoffered off
				ON tadi.`schlenrollsubjoff_id` = off.`SchlEnrollSubjOffSms_ID`
			LEFT JOIN schoolacademicsubject subj
				ON off.`SchlAcadSubj_ID` = subj.`SchlAcadSubjSms_ID`
			LEFT JOIN schoolacademicsection sec
				ON off.`SchlAcadSec_ID` = sec.`SchlAcadSecSms_ID`
			LEFT JOIN schoolacademiccourses crse
				ON off.`SchlAcadCrses_ID` = crse.`SchlAcadCrseSms_ID`
			LEFT JOIN schooldepartment dept
				ON crse.`SchlDept_ID` = dept.`SchlDeptSms_ID`
			LEFT JOIN schoolemployee emp
				ON tadi.`schlprof_id` = emp.`SchlEmpSms_ID`

			WHERE off.`SchlAcadLvl_ID` = ?
			AND off.`SchlAcadYr_ID` = ?
			AND off.`SchlAcadPrd_ID` = ?
			AND off.`SchlAcadYrLvl_ID` = ?
            AND tadi.schltadi_date BETWEEN ? AND ?
            ORDER BY 
                emp.SchlEmp_LNAME, 
                subj.SchlAcadSubj_CODE,
                tadi.schltadi_date,
                tadi.schltadi_timein";

    $stmt = $dbPortal->prepare($qry);
    $stmt->bind_param("iiiiss", $schlAcadLvl_ID, $schlAcadYr_ID, $schlAcadPrd_ID, $schlAcadYrLvl_ID, $date_start, $date_end);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetch = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $dbPortal->close();
}

echo json_encode($fetch);
?>