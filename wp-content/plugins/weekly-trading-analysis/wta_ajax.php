<?php 
if ( ! function_exists('get_wta_data') ) {
	function get_wta_data(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $outlet = $_POST['outlet'];
                $term = $_POST['term'];
                $date_str = $_POST['date'];

                $date = DateTime::createFromFormat('d/m/Y', $date_str);
                $week_start = clone $date;
                $week_end = clone $date;
                $week_start->modify('this week');
                $week_end->modify('this week +6 days');

                $week1 = clone $date;
                $week2 = clone $date;
                $week3 = clone $date;
                $week4 = clone $date;
                $week5 = clone $date;
                $week6 = clone $date;
                $week7 = clone $date;

                $week1->modify('this week');
                $week2->modify('this week +1 days');
                $week3->modify('this week +2 days');
                $week4->modify('this week +3 days');
                $week5->modify('this week +4 days');
                $week6->modify('this week +5 days');
                $week7->modify('this week +6 days');

                $week1_str = $week1->format('d/m/Y');
                $week2_str = $week2->format('d/m/Y');
                $week3_str = $week3->format('d/m/Y');
                $week4_str = $week4->format('d/m/Y');
                $week5_str = $week5->format('d/m/Y');
                $week6_str = $week6->format('d/m/Y');
                $week7_str = $week7->format('d/m/Y');

                $res = array(
                    $week1_str => array('id'=>-1, 'day'=>'MONDAY', 'date'=>$week1_str, 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0),
                    $week2_str => array('id'=>-1, 'day'=>'TUESDAY', 'date'=>$week2_str, 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0),
                    $week3_str => array('id'=>-1, 'day'=>'WEDNESDAY', 'date'=>$week3_str, 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0),
                    $week4_str => array('id'=>-1, 'day'=>'THURSDAY', 'date'=>$week4_str, 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0),
                    $week5_str => array('id'=>-1, 'day'=>'FRIDAY', 'date'=>$week5_str, 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0),
                    $week6_str => array('id'=>-1, 'day'=>'SATURDAY', 'date'=>$week6_str, 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0),
                    $week7_str => array('id'=>-1, 'day'=>'SUNDAY', 'date'=>$week7_str, 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0),
                    'TOTAL' => array('id'=>-1, 'day'=>'', 'date'=>'', 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0)
                );
                

                $week_start_str = $week_start->format('Y-m-d');
                $week_end_str = $week_end->format('Y-m-d');

                $sql = "SELECT WTA.ID AS ID, CONVERT(varchar(10), WTA.Date, 103) AS Date, ZRef, SalesEXVAT AS ExVat, VATAmount AS Vat, SalesEXVAT+VATAmount AS IncVat, Disrepancy, ISNULL(FromTill, 0) AS FromTill, ISNULL(FromSafe,0) AS FromSafe, AccountSales, AccountReceipts, CardsBanking, 
                            SalesEXVAT + VATAmount + Disrepancy - ISNULL(FromTill, 0) - AccountSales + AccountReceipts - CardsBanking AS Cash 
                        FROM WTA 
                            LEFT JOIN (SELECT ZRefID, Date, SUM(PayoutEXVat + PayoutVATAmount) FromTill FROM WTAEPOSPayouts WHERE ZRefID IN (SELECT ZRef FROM WTA WHERE Date>='$week_start_str' AND Date<='$week_end_str') GROUP BY ZRefID, Date ) t1 ON t1.ZRefID = WTA.ZRef AND t1.Date = WTA.Date
                            LEFT JOIN (SELECT ZRefID, Date, SUM(PayoutEXVat + PayoutVATAmount) FromSafe FROM WTASafePayouts WHERE ZRefID IN (SELECT ZRef FROM WTA WHERE Date>='$week_start_str' AND Date<='$week_end_str') GROUP BY ZRefID, Date ) t2 ON t2.ZRefID = WTA.ZRef AND t2.Date = WTA.Date
                        WHERE OutletID=$outlet AND TerminalID=$term AND WTA.Date>='$week_start_str' AND WTA.Date<='$week_end_str'";

                $stmt = sqlsrv_query($conn, $sql);

                if ($stmt === false) {
                    sqlsrv_close($conn);
                    die(print_r(sqlsrv_errors(), true));
                }

                $sale_ex_sum = 0;
                $vat_sum = 0;
                $sale_inc_sum = 0;
                $disrepancy_sum = 0;
                $paid_out_from_till_sum = 0;
                $paid_out_from_safe_sum = 0;
                $account_sales_sum = 0;
                $account_receipts_sum = 0;
                $cards_banking_sum = 0;
                $cash_retained_sum = 0;
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $res[$row['Date']]['id'] = $row['ID'];
                    $res[$row['Date']]['date'] = $row['Date'];
                    $res[$row['Date']]['zref'] = $row['ZRef'];
                    $res[$row['Date']]['sale_ex'] = $row['ExVat'];
                    $res[$row['Date']]['vat'] = $row['Vat'];
                    $res[$row['Date']]['sale_inc'] = $row['IncVat'];
                    $res[$row['Date']]['disrepancy'] = $row['Disrepancy'];
                    $res[$row['Date']]['paid_out_from_till'] = $row['FromTill'];
                    $res[$row['Date']]['paid_out_from_safe'] = $row['FromSafe'];
                    $res[$row['Date']]['account_sales'] = $row['AccountSales'];
                    $res[$row['Date']]['account_receipts'] = $row['AccountReceipts'];
                    $res[$row['Date']]['cards_banking'] = $row['CardsBanking'];
                    $res[$row['Date']]['cash_retained'] = $row['Cash'];

                    $sale_ex_sum += $row['ExVat'];
                    $vat_sum += $row['Vat'];
                    $sale_inc_sum += $row['IncVat'];
                    $disrepancy_sum += $row['Disrepancy'];
                    $paid_out_from_till_sum += $row['FromTill'];;
                    $paid_out_from_safe_sum += $row['FromSafe'];;
                    $account_sales_sum += $row['AccountSales'];
                    $account_receipts_sum += $row['AccountReceipts'];
                    $cards_banking_sum += $row['CardsBanking'];
                    $cash_retained_sum += $row['Cash'];
                }
                $res["TOTAL"]['id'] = '';
                $res["TOTAL"]['date'] = '';
                $res["TOTAL"]['zref'] = '';
                $res["TOTAL"]['sale_ex'] = $sale_ex_sum;
                $res["TOTAL"]['vat'] = $vat_sum;
                $res["TOTAL"]['sale_inc'] = $sale_inc_sum;
                $res["TOTAL"]['disrepancy'] = $disrepancy_sum;
                $res["TOTAL"]['paid_out_from_till'] = $paid_out_from_till_sum;
                $res["TOTAL"]['paid_out_from_safe'] = $paid_out_from_safe_sum;
                $res["TOTAL"]['account_sales'] = $account_sales_sum;
                $res["TOTAL"]['account_receipts'] = $account_receipts_sum;
                $res["TOTAL"]['cards_banking'] = $cards_banking_sum;
                $res["TOTAL"]['cash_retained'] = $cash_retained_sum;

                echo json_encode($res);
            }
            sqlsrv_close($conn);
        }
        else
        {
            $response = array(
                'status' => 'failed',
                'message' => 'Can not to connect to SQL Server.'
            );
            echo json_encode($response);
        }
        die;
	}
    add_action('wp_ajax_get_wta_data', 'get_wta_data');
}

if ( ! function_exists('get_summary_data') ) {
	function get_summary_data(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $outlet = $_POST['outlet'];
                $date_str = $_POST['date'];

                $date = DateTime::createFromFormat('d/m/Y', $date_str);
                $week_start = clone $date;
                $week_end = clone $date;
                $week_start->modify('this week');
                $week_end->modify('this week +6 days');

                $week1 = clone $date;
                $week2 = clone $date;
                $week3 = clone $date;
                $week4 = clone $date;
                $week5 = clone $date;
                $week6 = clone $date;
                $week7 = clone $date;

                $week1->modify('this week');
                $week2->modify('this week +1 days');
                $week3->modify('this week +2 days');
                $week4->modify('this week +3 days');
                $week5->modify('this week +4 days');
                $week6->modify('this week +5 days');
                $week7->modify('this week +6 days');

                $week1_str = $week1->format('d/m/Y');
                $week2_str = $week2->format('d/m/Y');
                $week3_str = $week3->format('d/m/Y');
                $week4_str = $week4->format('d/m/Y');
                $week5_str = $week5->format('d/m/Y');
                $week6_str = $week6->format('d/m/Y');
                $week7_str = $week7->format('d/m/Y');

                $res = array(
                    $week1_str => array('id'=>-1, 'day'=>'MONDAY', 'date'=>$week1_str, 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0),
                    $week2_str => array('id'=>-1, 'day'=>'TUESDAY', 'date'=>$week2_str, 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0),
                    $week3_str => array('id'=>-1, 'day'=>'WEDNESDAY', 'date'=>$week3_str, 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0),
                    $week4_str => array('id'=>-1, 'day'=>'THURSDAY', 'date'=>$week4_str, 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0),
                    $week5_str => array('id'=>-1, 'day'=>'FRIDAY', 'date'=>$week5_str, 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0),
                    $week6_str => array('id'=>-1, 'day'=>'SATURDAY', 'date'=>$week6_str, 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0),
                    $week7_str => array('id'=>-1, 'day'=>'SUNDAY', 'date'=>$week7_str, 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0),
                    'TOTAL' => array('id'=>-1, 'day'=>'', 'date'=>'', 'zref'=>'', 'sale_ex'=>0, 'vat'=>0, 'sale_inc'=>0, 'disrepancy'=>0, 'paid_out_from_till'=>0, 'paid_out_from_safe'=>0, 'account_sales'=>0, 'account_receipts'=>0, 'cards_banking'=>0, 'cash_retained'=>0)
                );
                

                $week_start_str = $week_start->format('Y-m-d');
                $week_end_str = $week_end->format('Y-m-d');
                
                $sql = "SELECT CONVERT(varchar(10), WTA.Date, 103) AS Date, SUM(CASE WHEN ZRef > 0 THEN 1 ELSE 0 END) AS ZRef, SUM(SalesEXVAT) AS ExVat, SUM(VATAmount) AS Vat, SUM(SalesEXVAT+VATAmount) AS IncVat, SUM(Disrepancy) Disrepancy, SUM(ISNULL(FromTill, 0)) AS FromTill, SUM(ISNULL(FromSafe,0)) AS FromSafe, SUM(AccountSales) AccountSales, SUM(AccountReceipts) AccountReceipts, SUM(CardsBanking) CardsBanking, 
                            SUM(SalesEXVAT) + SUM(VATAmount) + SUM(Disrepancy) - SUM(ISNULL(FromTill, 0)) - SUM(AccountSales) + SUM(AccountReceipts) - SUM(CardsBanking) AS Cash 
                        FROM WTA 
                            LEFT JOIN (SELECT ZRefID, Date, SUM(PayoutEXVat + PayoutVATAmount) FromTill FROM WTAEPOSPayouts WHERE ZRefID IN (SELECT ZRef FROM WTA WHERE Date>='$week_start_str' AND Date<='$week_end_str') GROUP BY ZRefID, Date ) t1 ON t1.ZRefID = WTA.ZRef AND t1.Date = WTA.Date
                            LEFT JOIN (SELECT ZRefID, Date, SUM(PayoutEXVat + PayoutVATAmount) FromSafe FROM WTASafePayouts WHERE ZRefID IN (SELECT ZRef FROM WTA WHERE Date>='$week_start_str' AND Date<='$week_end_str') GROUP BY ZRefID, Date ) t2 ON t2.ZRefID = WTA.ZRef AND t2.Date = WTA.Date
                        WHERE OutletID=$outlet AND WTA.Date>='$week_start_str' AND WTA.Date<='$week_end_str' GROUP BY WTA.Date";
                $stmt = sqlsrv_query($conn, $sql);

                if ($stmt === false) {
                    sqlsrv_close($conn);
                    die(print_r(sqlsrv_errors(), true));
                }

                $sale_ex_sum = 0;
                $vat_sum = 0;
                $sale_inc_sum = 0;
                $disrepancy_sum = 0;
                $paid_out_from_till_sum = 0;
                $paid_out_from_safe_sum = 0;
                $account_sales_sum = 0;
                $account_receipts_sum = 0;
                $cards_banking_sum = 0;
                $cash_retained_sum = 0;
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $res[$row['Date']]['id'] = '';
                    $res[$row['Date']]['date'] = $row['Date'];
                    $res[$row['Date']]['zref'] = $row['ZRef'];
                    $res[$row['Date']]['sale_ex'] = $row['ExVat'];
                    $res[$row['Date']]['vat'] = $row['Vat'];
                    $res[$row['Date']]['sale_inc'] = $row['IncVat'];
                    $res[$row['Date']]['disrepancy'] = $row['Disrepancy'];
                    $res[$row['Date']]['paid_out_from_till'] = $row['FromTill'];
                    $res[$row['Date']]['paid_out_from_safe'] = $row['FromSafe'];
                    $res[$row['Date']]['account_sales'] = $row['AccountSales'];
                    $res[$row['Date']]['account_receipts'] = $row['AccountReceipts'];
                    $res[$row['Date']]['cards_banking'] = $row['CardsBanking'];
                    $res[$row['Date']]['cash_retained'] = $row['Cash'];

                    $sale_ex_sum += $row['ExVat'];
                    $vat_sum += $row['Vat'];
                    $sale_inc_sum += $row['IncVat'];
                    $disrepancy_sum += $row['Disrepancy'];
                    $paid_out_from_till_sum += $row['FromTill'];;
                    $paid_out_from_safe_sum += $row['FromSafe'];;
                    $account_sales_sum += $row['AccountSales'];
                    $account_receipts_sum += $row['AccountReceipts'];
                    $cards_banking_sum += $row['CardsBanking'];
                    $cash_retained_sum += $row['Cash'];
                }
                $res["TOTAL"]['id'] = '';
                $res["TOTAL"]['date'] = '';
                $res["TOTAL"]['zref'] = '';
                $res["TOTAL"]['sale_ex'] = $sale_ex_sum;
                $res["TOTAL"]['vat'] = $vat_sum;
                $res["TOTAL"]['sale_inc'] = $sale_inc_sum;
                $res["TOTAL"]['disrepancy'] = $disrepancy_sum;
                $res["TOTAL"]['paid_out_from_till'] = $paid_out_from_till_sum;
                $res["TOTAL"]['paid_out_from_safe'] = $paid_out_from_safe_sum;
                $res["TOTAL"]['account_sales'] = $account_sales_sum;
                $res["TOTAL"]['account_receipts'] = $account_receipts_sum;
                $res["TOTAL"]['cards_banking'] = $cards_banking_sum;
                $res["TOTAL"]['cash_retained'] = $cash_retained_sum;

                echo json_encode($res);
            }
            sqlsrv_close($conn);
        }
        else
        {
            $response = array(
                'status' => 'failed',
                'message' => 'Can not to connect to SQL Server.'
            );
            echo json_encode($response);
        }
        die;
	}
    add_action('wp_ajax_get_summary_data', 'get_summary_data');
}

if ( ! function_exists('set_wta_data') ) {
	function set_wta_data(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $outlet = $_POST['outlet'];
                $term = $_POST['term'];
                $id = $_POST['id'];
                $date = $_POST['date'];
                $row = $_POST['row'];
                $user_id = $_POST['user_id'];
                $date_obj = DateTime::createFromFormat('d/m/Y', $date);
                $date_str = $date_obj->format('Y-m-d');

                if($id == -1)
                {
                    $sql = "SELECT MAX(ID)+1 new_id FROM WTA";
                    $stmt = sqlsrv_query($conn, $sql);
                    if ($stmt === false) {
                        sqlsrv_close($conn);
                        die(print_r(sqlsrv_errors(), true));
                    }
                    $new_id = 1;
                    while ($r = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $new_id = $r['new_id'];
                    }

                    $sql = sprintf("INSERT INTO WTA(ID, OutletID, TerminalID, Date, ZRef, SalesEXVAT, VATAmount, Disrepancy, AccountSales, AccountReceipts, CardsBanking, UsernameID) VALUES(%d, %d, %d, '%s', %d, %f, %f, %f, %f, %f, %f, %d)",
                                   $new_id, $outlet, $term, $date_str, $row["zref"], $row["sale_ex"], $row["vat"], $row["disrepancy"], $row["account_sales"], $row["account_receipts"], $row["cards_banking"], $user_id);
                    $stmt = sqlsrv_query($conn, $sql);
                    if ($stmt === false) {
                        sqlsrv_close($conn);
                        die(print_r(sqlsrv_errors(), true));
                    }
                }
                else
                {
                    $sql = sprintf("UPDATE WTA SET OutletID=%d, TerminalID=%d, Date='%s', ZRef=%d, SalesEXVAT=%f, VATAmount=%f, Disrepancy=%f, AccountSales=%f, AccountReceipts=%f, CardsBanking=%f, UsernameID=%f WHERE ID=%d",
                                   $outlet, $term, $date_str, $row["zref"], $row["sale_ex"], $row["vat"], $row["disrepancy"], $row["account_sales"], $row["account_receipts"], $row["cards_banking"], $user_id, $id);
                    $stmt = sqlsrv_query($conn, $sql);
                    if ($stmt === false) {
                        sqlsrv_close($conn);
                        die(print_r(sqlsrv_errors(), true));
                    }
                }

                $response = array(
                    'status' => 'success',
                    'message' => 'Save successed'
                );
                echo json_encode($response);
            }
        }
        else
        {
            $response = array(
                'status' => 'failed',
                'message' => 'Can not to connect to SQL Server.'
            );
            echo json_encode($response);
        }
        die;
	}
    add_action('wp_ajax_set_wta_data', 'set_wta_data');
}

if ( ! function_exists('get_paid_data') ) {
	function get_paid_data(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $paid_type = $_POST['paid_type'];
                $date_str = $_POST['date'];
                $zref = $_POST['zref'];

                $date = DateTime::createFromFormat('d/m/Y', $date_str);
                $res = array();
                $date_str = $date->format('Y-m-d');
                $tbl_name = '';
                if($paid_type + 0 == 0)
                    $tbl_name = "WTAEPOSPayouts";
                else
                    $tbl_name = "WTASafePayouts";
    
                $sql = sprintf("SELECT ID, CONVERT(varchar(10), Date, 103) Date, ZRefID, PayoutEXVAT, PayoutVATAmount, PayoutEXVAT + PayoutVATAmount total, PayoutType, SupplierID, Reference, Description FROM ".$tbl_name." WHERE Date='%s' AND ZRefID=%d ORDER BY ID", $date_str, $zref);

                $stmt = sqlsrv_query($conn, $sql);

                if ($stmt === false) {
                    sqlsrv_close($conn);
                    die(print_r(sqlsrv_errors(), true));
                }

                $ex_sum = 0;
                $amount_sum = 0;
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $res[$row['ID']]['id'] = $row['ID'];
                    $res[$row['ID']]['date'] = $row['Date'];
                    $res[$row['ID']]['zref'] = $row['ZRefID'];
                    $res[$row['ID']]['ex_vat'] = $row['PayoutEXVAT'];
                    $res[$row['ID']]['vat_amount'] = $row['PayoutVATAmount'];
                    $res[$row['ID']]['total'] = $row['total'];
                    $res[$row['ID']]['payout_type'] = $row['PayoutType'];
                    $res[$row['ID']]['supplier'] = $row['SupplierID'];
                    $res[$row['ID']]['reference'] = $row['Reference'];
                    $res[$row['ID']]['description'] = $row['Description'];

                    $ex_sum += $row['PayoutEXVAT'];
                    $amount_sum += $row['PayoutVATAmount'];
                }
                $res[$row['ID']]['id'] = '';
                $res[$row['ID']]['date'] = '';
                $res[$row['ID']]['zref'] = '';
                $res[$row['ID']]['ex_vat'] = $ex_sum;
                $res[$row['ID']]['vat_amount'] = $amount_sum;
                $res[$row['ID']]['total'] = $ex_sum + $amount_sum;
                $res[$row['ID']]['payout_type'] = '';
                $res[$row['ID']]['supplier'] = '';
                $res[$row['ID']]['reference'] = '';
                $res[$row['ID']]['description'] = '';

                echo json_encode($res);
            }
            sqlsrv_close($conn);
        }
        else
        {
            $response = array(
                'status' => 'failed',
                'message' => 'Can not to connect to SQL Server.'
            );
            echo json_encode($response);
        }
        die;
	}
    add_action('wp_ajax_get_paid_data', 'get_paid_data');
}

if ( ! function_exists('set_paid_data') ) {
	function set_paid_data(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $paid_type = $_POST['paid_type'];
                $data = $_POST['data'];
                $date_str = $data['date'];

                $date = DateTime::createFromFormat('d/m/Y', $date_str);
                $date_str = $date->format('Y-m-d');
                $tbl_name = '';
                if($paid_type + 0 == 0)
                    $tbl_name = "WTAEPOSPayouts";
                else
                    $tbl_name = "WTASafePayouts";

                if($data['id'] + 0 == -1)
                {
                    $sql = "SELECT MAX(ID)+1 new_id FROM $tbl_name";
                    $stmt = sqlsrv_query($conn, $sql);
                    if ($stmt === false) {
                        sqlsrv_close($conn);
                        die(print_r(sqlsrv_errors(), true));
                    }
                    $new_id = 1;
                    while ($r = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $new_id = $r['new_id'];
                    }
                    $sql = sprintf("INSERT INTO $tbl_name(ID, Date, ZRefID, PayoutEXVAT, PayoutVATAmount, PayoutType, SupplierID, Reference, Description) VALUES(%d, '%s', %d, %f, %f, %d, %d, '%s', '%s')",
                                   $new_id, $date_str, $data['zref'], $data['ex_vat'], $data['vat_amount'], $data["payout_type"], $data["supplier"], $data["reference"], $data["description"]);
                    $stmt = sqlsrv_query($conn, $sql);
                    if ($stmt === false) {
                        sqlsrv_close($conn);
                        die(print_r(sqlsrv_errors(), true));
                    }
                }
                else
                {
                    $sql = sprintf("UPDATE $tbl_name SET Date='%s', ZRefID=%d, PayoutEXVAT=%f, PayoutVATAmount=%f, PayoutType=%d, SupplierID=%d, Reference='%s', Description='%s' WHERE ID=%d",
                                    $date_str, $data['zref'], $data['ex_vat'], $data['vat_amount'], $data["payout_type"], $data["supplier"], $data["reference"], $data["description"], $data["id"]);
                    $stmt = sqlsrv_query($conn, $sql);
                    if ($stmt === false) {
                        sqlsrv_close($conn);
                        die(print_r(sqlsrv_errors(), true));
                    }
                }

                $response = array(
                    'status' => 'success',
                    'message' => 'Save successed'
                );
                echo json_encode($response);
            }
            sqlsrv_close($conn);
        }
        else
        {
            $response = array(
                'status' => 'failed',
                'message' => 'Can not to connect to SQL Server.'
            );
            echo json_encode($response);
        }
        die;
	}
    add_action('wp_ajax_set_paid_data', 'set_paid_data');
}

if ( ! function_exists('delete_paid_data') ) {
	function delete_paid_data(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $paid_type = $_POST['paid_type'];
                $id = $_POST['id'];

                $tbl_name = '';
                if($paid_type + 0 == 0)
                    $tbl_name = "WTAEPOSPayouts";
                else
                    $tbl_name = "WTASafePayouts";

                
                $sql = sprintf("DELETE FROM $tbl_name WHERE ID=%d", $id);
                $stmt = sqlsrv_query($conn, $sql);
                if ($stmt === false) {
                    sqlsrv_close($conn);
                    die(print_r(sqlsrv_errors(), true));
                }
                
                $response = array(
                    'status' => 'success',
                    'message' => 'Save successed'
                );
                echo json_encode($response);
            }
            sqlsrv_close($conn);
        }
        else
        {
            $response = array(
                'status' => 'failed',
                'message' => 'Can not to connect to SQL Server.'
            );
            echo json_encode($response);
        }
        die;
	}
    add_action('wp_ajax_delete_paid_data', 'delete_paid_data');
}

if ( ! function_exists('get_cash_on_site') ) {
	function get_cash_on_site(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $outlet = $_POST['outlet'];
                $date_str = $_POST['date'];

                $date = DateTime::createFromFormat('d/m/Y', $date_str);
                $week_start = clone $date;
                $week_end = clone $date;
                $week_start->modify('this week');
                $week_end->modify('this week +6 days');

                $week_start_str = $week_start->format('Y-m-d');
                $week_end_str = $week_end->format('Y-m-d');
                
                $sql = "SELECT (SELECT PremisesFloat FROM Outlets WHERE ID = $outlet) + 
                        (SELECT ISNULL(ISNULL(SUM(SalesEXVAT),0) + ISNULL(SUM(VATAmount),0) + ISNULL(SUM(Disrepancy),0) - SUM(ISNULL(FromTill, 0)) - ISNULL(SUM(AccountSales),0) + ISNULL(SUM(AccountReceipts),0) - ISNULL(SUM(CardsBanking),0),0) AS Cash 
                        FROM WTA 
                            LEFT JOIN (SELECT ZRefID, Date, SUM(PayoutEXVat + PayoutVATAmount) FromTill FROM WTAEPOSPayouts WHERE ZRefID IN (SELECT ZRef FROM WTA WHERE Date>='$week_start_str' AND Date<='$week_end_str') GROUP BY ZRefID, Date ) t1 ON t1.ZRefID = WTA.ZRef AND t1.Date = WTA.Date
                            LEFT JOIN (SELECT ZRefID, Date, SUM(PayoutEXVat + PayoutVATAmount) FromSafe FROM WTASafePayouts WHERE ZRefID IN (SELECT ZRef FROM WTA WHERE Date>='$week_start_str' AND Date<='$week_end_str') GROUP BY ZRefID, Date ) t2 ON t2.ZRefID = WTA.ZRef AND t2.Date = WTA.Date
                        WHERE OutletID=$outlet AND WTA.Date>='$week_start_str' AND WTA.Date<='$week_end_str') 
                        - (SELECT ISNULL(SUM(Amount),0) FROM WTABanking WHERE OutletId=$outlet AND Date>'$week_start_str' AND Date<'$week_end_str') AS Cash";
                $stmt = sqlsrv_query($conn, $sql);

                if ($stmt === false) {
                    sqlsrv_close($conn);
                    die(print_r(sqlsrv_errors(), true));
                }
                $Cash = 0;
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $Cash = $row['Cash'];
                }
                $res = array('CashOnSite' => $Cash);
                echo json_encode($res);
            }
            sqlsrv_close($conn);
        }
        else
        {
            $response = array(
                'status' => 'failed',
                'message' => 'Can not to connect to SQL Server.'
            );
            echo json_encode($response);
        }
        die;
	}
    add_action('wp_ajax_get_cash_on_site', 'get_cash_on_site');
}

if ( ! function_exists('get_paidout_view_data') ) {
	function get_paidout_view_data(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $outlet = $_POST['outlet'];
                $date_str = $_POST['date'];

                $date = DateTime::createFromFormat('d/m/Y', $date_str);
                $week_start = clone $date;
                $week_end = clone $date;
                $week_start->modify('this week');
                $week_end->modify('this week +6 days');

                $week_start_str = $week_start->format('Y-m-d');
                $week_end_str = $week_end->format('Y-m-d');
                
                $sql = "SELECT CONVERT(varchar(10), Date, 103) date, ZRefID zref, PayoutEXVAT ex_vat, PayoutVATAmount vat_amount, SupplierName supplier_name, t.Description as payout_type, reference, p.description 
                        FROM WTAEPOSPayouts as p LEFT JOIN SettingsPayoutTypes AS t ON t.ID = p.PayoutType LEFT JOIN Suppliers AS s ON s.ID = p.SupplierID 
                        WHERE Date>='$week_start_str' AND Date<'$week_end_str' ORDER BY date";
                $stmt = sqlsrv_query($conn, $sql);

                if ($stmt === false) {
                    sqlsrv_close($conn);
                    die(print_r(sqlsrv_errors(), true));
                }

                $vat_sum = 0;
                $amount_sum = 0;
                $index = 0;
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $res1[$index]['date'] = $row['date'];
                    $res1[$index]['zref'] = $row['zref'];
                    $res1[$index]['supplier_name'] = $row['supplier_name'];
                    $res1[$index]['payout_type'] = $row['payout_type'];
                    $res1[$index]['ex_vat'] = $row['ex_vat'];
                    $res1[$index]['vat_amount'] = $row['vat_amount'];
                    $res1[$index++]['description'] = $row['description'];

                    $vat_sum += $row['ex_vat'];
                    $amount_sum += $row['vat_amount'];
                }
                $res1[$index]['date'] = '<center>TOTAL</center>';
                $res1[$index]['zref'] = '';
                $res1[$index]['supplier_name'] = '';
                $res1[$index]['payout_type'] = '';
                $res1[$index]['ex_vat'] = $vat_sum;
                $res1[$index]['vat_amount'] = $amount_sum;
                $res1[$index++]['description'] = '';

                $sql = "SELECT CONVERT(varchar(10), Date, 103) date, ZRefID zref, PayoutEXVAT ex_vat, PayoutVATAmount vat_amount, SupplierName supplier_name, t.Description as payout_type, reference, p.description 
                        FROM WTASafePayouts as p LEFT JOIN SettingsPayoutTypes AS t ON t.ID = p.PayoutType LEFT JOIN Suppliers AS s ON s.ID = p.SupplierID 
                        WHERE Date>='$week_start_str' AND Date<'$week_end_str' ORDER BY date";
                $stmt = sqlsrv_query($conn, $sql);

                if ($stmt === false) {
                    sqlsrv_close($conn);
                    die(print_r(sqlsrv_errors(), true));
                }

                $vat_sum = 0;
                $amount_sum = 0;
                $index = 0;
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $res2[$index]['date'] = $row['date'];
                    $res2[$index]['zref'] = $row['zref'];
                    $res2[$index]['supplier_name'] = $row['supplier_name'];
                    $res2[$index]['payout_type'] = $row['payout_type'];
                    $res2[$index]['ex_vat'] = $row['ex_vat'];
                    $res2[$index]['vat_amount'] = $row['vat_amount'];
                    $res2[$index++]['description'] = $row['description'];

                    $vat_sum += $row['ex_vat'];
                    $amount_sum += $row['vat_amount'];
                }
                $res2[$index]['date'] = '<center>TOTAL</center>';
                $res2[$index]['zref'] = '';
                $res2[$index]['supplier_name'] = '';
                $res2[$index]['payout_type'] = '';
                $res2[$index]['ex_vat'] = $vat_sum;
                $res2[$index]['vat_amount'] = $amount_sum;
                $res2[$index++]['description'] = '';

                $res['data1'] = $res1;
                $res['data2'] = $res2;

                echo json_encode($res);
            }
            sqlsrv_close($conn);
        }
        else
        {
            $response = array(
                'status' => 'failed',
                'message' => 'Can not to connect to SQL Server.'
            );
            echo json_encode($response);
        }
        die;
	}
    add_action('wp_ajax_get_paidout_view_data', 'get_paidout_view_data');
}
?>