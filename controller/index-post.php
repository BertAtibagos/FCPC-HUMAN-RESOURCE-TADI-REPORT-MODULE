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
    
    $rangeType = $_POST['rangeType'];
    $filterType = $_POST['filterType'];

    $queryFilter = "";
    $binding = "";

    if($rangeType == 'byDate'){
        if($filterType == 'deptName_all'){
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];

            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?";

            $values = [$startDate, $endDate];
            $bind = "ss";
        }else if($filterType == 'name_Search'){
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];
            $name = $_POST['name'];
            $bindName = "%". $name . "%";

            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?
            AND CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) LIKE ?";

            $values = [$startDate, $endDate, $bindName];
            $bind = "sss";
        }
        else if($filterType == 'dept_Search'){
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];
            $dept = $_POST['dept'];

            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?
            AND `dept`.`SchlDept_CODE` = ?";
            
            $values = [$startDate, $endDate, $dept];
            $bind = "sss";
        }
    }

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
        $prev_month_start = new DateTime($current_year . '-' . $current_month . '-01');
        $prev_month_start->modify('-1 month');
        $prev_month_str = $prev_month_start->format('Y-m');
        $prev_cutoff_start = $prev_month_str . '-16';
        $prev_cutoff_end = $prev_month_str . '-' . $prev_month_start->format('t');
    } else {
        $current_cutoff_start = date('Y-m-16');
        $current_cutoff_end = date('Y-m-t');
        
        // Previous cut-off is 1-15 of current month
        $prev_cutoff_start = date('Y-m-01');
        $prev_cutoff_end = date('Y-m-15');
    }

    if($rangeType == 'currCutOff'){
        $date_start = $current_cutoff_start;
        $date_end = $current_cutoff_end;
        
        if($filterType == 'deptName_all'){
            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?";

            $values = [$date_start, $date_end];
            $bind = "ss";
        }else if($filterType == 'name_Search'){
            $name = $_POST['name'];
            $bindName = "%". $name . "%";

            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?
            AND CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) LIKE ?";

            $values = [$date_start, $date_end, $bindName];
            $bind = "sss";
        }
        else if($filterType == 'dept_Search'){
            $dept = $_POST['dept'];

            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?
            AND `dept`.`SchlDept_CODE` = ?";
            
            $values = [$date_start, $date_end, $dept];
            $bind = "sss";
        }
    }

    if($rangeType == 'prevCutOff'){
        $date_start = $prev_cutoff_start;
        $date_end = $prev_cutoff_end;

        if($filterType == 'deptName_all'){
            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?";

            $values = [$date_start, $date_end];
            $bind = "ss";
        }else if($filterType == 'name_Search'){
            $name = $_POST['name'];
            $bindName = "%". $name . "%";

            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?
            AND CONCAT(emp.`SchlEmp_LNAME`, ', ', emp.`SchlEmp_FNAME`) LIKE ?";

            $values = [$date_start, $date_end, $bindName];
            $bind = "sss";
        }
        else if($filterType == 'dept_Search'){
            $dept = $_POST['dept'];

            $queryFilter = "WHERE tadi.schltadi_date BETWEEN ? AND ?
            AND `dept`.`SchlDept_CODE` = ?";

            $values = [$date_start, $date_end, $dept];
            $bind = "sss";
        }
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

            $queryFilter
            ORDER BY 
                emp.SchlEmp_LNAME, 
                subj.SchlAcadSubj_CODE,
                tadi.schltadi_date,
                tadi.schltadi_timein";

    $stmt = $dbPortal->prepare($qry);
    $stmt->bind_param($bind, ...$values);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetch = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $dbPortal->close();
}

if($type == 'GET_INSTRUCTOR_LIST_DEPT_SUMMARY'){

    $rangeType = $_POST['rangeType'];
    $dept = $_POST['dept'];

    $queryFilter = "";
    $bind = "";
    $values = [];

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
        $prev_month_start = new DateTime($current_year . '-' . $current_month . '-01');
        $prev_month_start->modify('-1 month');
        $prev_month_str = $prev_month_start->format('Y-m');
        $prev_cutoff_start = $prev_month_str . '-16';
        $prev_cutoff_end = $prev_month_str . '-' . $prev_month_start->format('t');
    } else {
        $current_cutoff_start = date('Y-m-16');
        $current_cutoff_end = date('Y-m-t');
        
        // Previous cut-off is 1-15 of current month
        $prev_cutoff_start = date('Y-m-01');
        $prev_cutoff_end = date('Y-m-15');
    }

    if($rangeType == 'byDate'){
        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];
        
        $values = [$startDate, $endDate, $dept];
        $bind = "sss";
    }

    if($rangeType == 'currCutOff'){
        $date_start = $current_cutoff_start;
        $date_end = $current_cutoff_end;
        
        $values = [$date_start, $date_end, $dept];
        $bind = "sss";
    }

    if($rangeType == 'prevCutOff'){
        $date_start = $prev_cutoff_start;
        $date_end = $prev_cutoff_end;

        $values = [$date_start, $date_end, $dept];
        $bind = "sss";
    }

    $qry = "SELECT 
                schl_dept.SchlDept_CODE AS dept_code,
                CONCAT(emp.SchlEmp_LNAME, ', ', emp.SchlEmp_FNAME, ' ', emp.SchlEmp_MNAME) AS prof_name,

                COUNT(CASE WHEN st.schltadi_status = 1 THEN 1 END) AS verified_count,

                COUNT(CASE WHEN st.schltadi_status = 0 THEN 1 END) AS unverified_count,

                COUNT(st.schltadi_id) AS total_count

            FROM schoolenrollmentsubjectoffered AS seso
            LEFT JOIN schoolacademiccourses AS schl_acad_crses
                ON seso.SchlAcadCrses_ID = schl_acad_crses.SchlAcadCrseSms_ID
            LEFT JOIN schooldepartment AS schl_dept
                ON schl_acad_crses.SchlDept_ID = schl_dept.SchlDeptSms_ID
            LEFT JOIN schoolemployee AS emp
                ON seso.SchlProf_ID = emp.SchlEmpSms_ID

            LEFT JOIN schooltadi AS st
                ON st.schlenrollsubjoff_id = seso.SchlEnrollSubjOffSms_ID
                AND st.schltadi_date BETWEEN ? AND ?

            WHERE 
                seso.SchlAcadLvl_ID = 2
                AND seso.SchlAcadYr_ID = 19
                AND seso.SchlAcadPrd_ID = 5
                AND schl_dept.SchlDept_CODE = ?
                AND seso.SchlEnrollSubjOff_ISACTIVE = 1
                AND emp.SchlEmp_ID IS NOT NULL

            GROUP BY 
                seso.SchlProf_ID,
                emp.SchlEmp_LNAME,
                emp.SchlEmp_FNAME,
                emp.SchlEmp_MNAME

            ORDER BY prof_name ASC
            ";

    $stmt = $dbPortal->prepare($qry);
    $stmt->bind_param($bind, ...$values);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetch = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $dbPortal->close();
}


echo json_encode($fetch);
?>