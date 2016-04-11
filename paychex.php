<?
$host = "";
$dbName = "rpower";
$userName = "";
$pass = "
";
$conn = new mysqli("$host", "$userName", "$pass", "$dbName", "13306");


$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];
$endDateReport = $_GET['endDate'];
$store = $_GET['store'];
$cg = $_SESSION['cg'];
$store = str_replace("'", "''", $store);

$sql4am = "SELECT
                view5142_employee.payroll_id AS payrollId, view5142_employee.name AS employee, view5142_time_clock.job_id as jobCode,
                view5142_job.name as job, view5142_time_clock.reg_rate AS regRate, view5142_time_clock.ot_rate AS otRate,
                view5142_time_clock.dt_rate AS dtRate,
                SUM(view5142_time_clock.reg_pay) AS regPay,
                SUM(view5142_time_clock.ot_pay) AS otPay,
                SUM(view5142_time_clock.dt_pay) AS dtPay,
                SUM(view5142_time_clock.reg_hours) AS regHours,
                SUM(view5142_time_clock.ot_hours) AS otHours,
                SUM(view5142_time_clock.dt_hours) AS dtHours,
                SUM(view5142_time_clock.tip_net+view5142_time_clock.tip_cash) AS tips,
                SUM(view5142_time_clock.tip_charge_sls) AS salesCharge,
                SUM(view5142_time_clock.tip_cash_sls) AS salesCash,
                COUNT(view5142_employee.payroll_id) AS shifts,
                view5142_store.name AS store
        FROM
                rpower.view5142_time_clock
                INNER JOIN rpower.view5142_store
                ON view5142_time_clock.store_mid = view5142_store.mid INNER JOIN rpower.view5142_employee
                ON view5142_time_clock.emp_mid = view5142_employee.mid
                INNER JOIN rpower.view5142_job
                ON view5142_time_clock.job_mid = view5142_job.mid WHERE view5142_time_clock.pp_start_date = '2015-09-14'
                AND view5142_job.is_salary < '1'
        GROUP BY
                employee, job";

$result = $conn ->query($sql4am) or die($mysqli->error.__LINE__);
        if($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {
                        $payrollId = $row['payrollId'];
                        $employee = $row['employee'];
                        $jobCode = $row['jobCode'];
                        $job = $row['job'];
                        $regRate = $row['regRate'];
                        $otRate = $row['otRate'];
                        $dtRate = $row['dtRate'];
                        $regHours = $row['regHours'];
                        $otHours = $row['otHours'];
                        $dtHours = $row['dtHours'];
                        $regPay = $row['regPay'];
                        $otPay = $row['otPay'];
                        $dtPay = $row['dtPay'];
                        $salesCharge = $row['salesCharge'];
                        $sales = $salesCharge;
                        $tips = $row['tips'];
                        $shifts = $row['shifts'];
                        $rateCode = null;
                        //Format to paychex
                        $regRate = number_format($regRate, 4, '.', '');
                        $otRate = number_format($otRate, 4, '.', '');
                        $dtRate = number_format($dtRate, 4, '.', '');
                        $payrollId = str_pad($payrollId, 6, " ", STR_PAD_RIGHT); //Empl #
                        $employee = str_pad($employee, 25, " ", STR_PAD_RIGHT); // Name
                        $jobCode = str_pad($jobCode, 6, " ", STR_PAD_RIGHT); //Override Dept
                        // $job = Handled below
                        $shift = " "; //Shift
                        $de = str_pad("E", 1, " ", STR_PAD_RIGHT); // D/E
                        $earnCode = str_pad($earnCode, 2, " ", STR_PAD_RIGHT); //Earn Code
                        $regRate = str_pad($regRate, 9, " ", STR_PAD_RIGHT); // Rate
                        $regHours = str_pad($regHours, 8, " ", STR_PAD_RIGHT); // Hours
                        $otHours = str_pad($otHours, 8, " ", STR_PAD_RIGHT); // OT Rate
                        $otRate = str_pad($otRate, 9, " ", STR_PAD_RIGHT); // OT Hours
                        $regPay = str_pad($regPay, 9, " ", STR_PAD_RIGHT);
                        $otPay = str_pad($otPay, 9, " ", STR_PAD_RIGHT);
                        $dtPay = str_pad($dtPay, 9, " ", STR_PAD_RIGHT);
                        $year = str_pad("", 2, " ", STR_PAD_RIGHT); // Year
                        $month = str_pad("", 2, " ", STR_PAD_RIGHT); // Month
                        $day = str_pad("", 2, " ", STR_PAD_RIGHT); // Day
                        $hour = str_pad("", 2, " ", STR_PAD_RIGHT); // Hour
                        $min = str_pad("", 2, " ", STR_PAD_RIGHT); // Min
                        $ymdhm = "$year$month$day$hour$min";

                        if (stristr($job, "banquet") != FALSE)
                        {
                                $rateCode = "BQ";
                                $isBq = "1";
                        }
                        else {
                                $rateCode = "1";
                        }
                        if ($otPay > 0)
                        {

                                if ($otPay > 0 && $rateCode == "BQ") {
                                        $rateCode = "B2";
                                }
                                else {
                                        $rateCode = "2";
                                }

                        $rateCode = str_pad($rateCode, 2, " ", STR_PAD_RIGHT);
                        $eol = str_pad("", 43, " ", STR_PAD_RIGHT);

                        if ($otPay > "0"){
                                $job = str_pad("", 12, " ", STR_PAD_RIGHT);
                                $write .= "$payrollId$employee$jobCode$job$shift$de$rateCode$otRate$otHours\r\n";
                        }

                        // Doubletime
                        if ($dtPay > 0){
                                $rateCode = "3 ";
                                $job = str_pad("", 12, " ", STR_PAD_RIGHT);
                                $write .= "$payrollId$employee$jobCode$job$shift$de$rateCode$dtRate$dtHours\r\n";
                        }
                        //// Doubletime ////

                        }

                        if ($isBq == "1"){
                                $rateCode = "BQ";
                        }
                        else {
                                $rateCode = "1";
                        }
                        $job = str_pad("", 12, " ", STR_PAD_RIGHT);
                        $rateCode = str_pad($rateCode, 2, " ", STR_PAD_RIGHT);
                        $eol = str_pad("", 43, " ", STR_PAD_RIGHT);
                        $write .= "$payrollId$employee$jobCode$job$shift$de$rateCode$regRate$regHours\r\n";
                        $isBq = "";

                        if($tips > 0){
                        $ymdhm = str_pad(" ", 10, " ", STR_PAD_RIGHT);
                        $rcRr = str_pad(" ", 17, " ", STR_PAD_RIGHT);
                        $eol = str_pad("", 24, " ", STR_PAD_RIGHT);
                        $job = str_pad("", 12, " ", STR_PAD_RIGHT);
                        $write .= "$payrollId$employee$jobCode$job$shift$de T$rcRr$ymdhm$tips$eol\r\n";
                        }

                        if ($jobCode == 200 || $jobCode == 300){
                        $meComp = number_format($shifts*2,2, '.', '');
                        $addAmt = str_pad($meComp, 9, " ", STR_PAD_RIGHT);
                        $me = "ME";
                        $write .= "$payrollId$employee$jobCode$job$shift$de$me$rcRr$ymdhm$addAmt$eol\r\n";
                        }

                        if($sales > 0 && $rateCode != "BQ"){

$sqlSales = "
        SELECT
                view5142_employee.payroll_id AS payrollId,
                view5142_employee.name AS name
                , view5142_ticket_payment.date AS date
                , SUM(view5142_ticket_payment.taxable1) AS sales FROM
                rpower.view5142_ticket_payment
                INNER JOIN
                        rpower.view5142_employee
                        ON view5142_ticket_payment.cashier_mid = view5142_employee.mid
        WHERE date BETWEEN '2015-09-14' AND '2015-09-27' AND view5142_employee.payroll_id = '$payrollId'
        GROUP BY payrollId";

                $resultSls = $conn ->query($sqlSales) or die($mysqli->error.__LINE__);
        if($resultSls->num_rows > 0) {

                 while($row = $resultSls->fetch_assoc()) {
                        $sales = $row['sales'];
                        }
                        $ymdhm = str_pad(" ", 10, " ", STR_PAD_RIGHT);
                        $rcRr = str_pad(" ", 17, " ", STR_PAD_RIGHT);
                        $eol = str_pad("", 24, " ", STR_PAD_RIGHT);
                        $code = "TS";
                        $write .= "$payrollId$employee$jobCode$job$shift$de$code$rcRr$ymdhm$sales$eol\r\n";
                        }
        }
}
}
                       else
                        {
                        }
//echo $write;
$fp = fopen("Q263_TA.TXT", "w");
fwrite($fp, $write);

$file = 'Q263_TA.TXT';

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: text');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
}



?>
