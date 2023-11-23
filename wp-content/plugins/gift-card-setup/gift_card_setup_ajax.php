<?php
if ( ! function_exists('save_gift_card') ) {
	function save_gift_card(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $ID = $_POST['data']['ID'];
                $PinNumber = $_POST['data']['PinNumber'];
                $StartDate = $_POST['data']['StartDate'];
                $EndDate = $_POST['data']['EndDate'];
                $date_obj1 = DateTime::createFromFormat('d/m/Y', $StartDate);
                $date_str1 = $date_obj1->format('Y-m-d');
                $date_obj2 = DateTime::createFromFormat('d/m/Y', $EndDate);
                $date_str2 = $date_obj2->format('Y-m-d');
                $Description = $_POST['data']['Description'];
                $GroupID = $_POST['data']['GroupID'];
                $imageData = $_POST['data']['Image'];
                $imageContent = base64_decode(substr($imageData, 22));
                $imagePath = "../wp-content/uploads/gift_cards_upload/" . $ID . ".png";
                $handle = fopen($imagePath, "w");
                if($handle)
                {
                    fwrite($handle, $imageContent);
                    fclose($handle);

                    sqlsrv_query($conn, "BEGIN TRANSACTION");
                    $sql = sprintf("INSERT INTO GiftCardImages(ID, PinNumber, StartDate, EndDate, Description, GroupID) VALUES(%d, %d, '%s', '%s', '%s', %d)",
                                   $ID, $PinNumber, $date_str1, $date_str2, $Description, $GroupID);
                    $stmt = sqlsrv_query($conn, $sql);
                    if ($stmt === false) {
                        sqlsrv_close($conn);
                        die(print_r(sqlsrv_errors(), true));
                    }
                    sqlsrv_query($conn, "COMMIT");
                }
                else{
                    $response = array(
                        'status' => 'failed',
                        'message' => 'Can not write image file.'
                    );
                    echo json_encode($response);
                    die;
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
                'message' => 'Can not connect to SQL Server.'
            );
            echo json_encode($response);
        }
        die;
	}
    add_action('wp_ajax_save_gift_card', 'save_gift_card');
}

if ( ! function_exists('get_gift_cards') ) {
	function get_gift_cards(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $group = $_POST['group'];
                $status = $_POST['stats'];

                $sql = "WITH t AS (SELECT GiftCardImages.ID, PinNumber, CONVERT(VARCHAR(10), StartDate, 103) StartDate, CONVERT(VARCHAR(10), EndDate, 103) EndDate, Description, GroupID ,(CASE WHEN GiftCardPurchases.ID IS NULL THEN CASE WHEN StartDate<=CONVERT(DATE, GETDATE()) AND EndDate>=CONVERT(DATE, GETDATE()) THEN 1 WHEN StartDate>=CONVERT(DATE, GETDATE()) THEN 0 ELSE 3 END ELSE 2 END) AS Status FROM GiftCardImages LEFT JOIN GiftCardPurchases ON GiftCardImages.ID=GiftCardPurchases.ImageID) SELECT * FROM t";
                if($group>=0 || $status>=0)
                    $sql .= " WHERE 1=1";
                if($group>=0)
                    $sql .= " AND GroupId=$group";
                if($status>=0)
                    $sql .= " AND Status=$status";
                $stmt = sqlsrv_query($conn, $sql);

                if ($stmt === false) {
                    sqlsrv_close($conn);
                    die(print_r(sqlsrv_errors(), true));
                }

                $count = 0;
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $res[$count]['ID'] = $row['ID'];
                    $res[$count]['PinNumber'] = $row['PinNumber'];
                    $res[$count]['StartDate'] = $row['StartDate'];
                    $res[$count]['EndDate'] = $row['EndDate'];
                    $res[$count]['Description'] = $row['Description'];
                    $res[$count]['GroupID'] = $row['GroupID'];
                    $res[$count]['deleteID'] = $row['ID'];
                    $res[$count++]['Status'] = $row['Status'];
                }
                echo json_encode($res);
            }
            sqlsrv_close($conn);
        }
        else
        {
            $response = array(
                'status' => 'failed',
                'message' => 'Can not connect to SQL Server.'
            );
            echo json_encode($response);
        }
        die;
	}
    add_action('wp_ajax_get_gift_cards', 'get_gift_cards');
}

if ( ! function_exists('update_gift_card') ) {
	function update_gift_card(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $ID = $_POST['data']['ID'];
                $PinNumber = $_POST['data']['PinNumber'];
                $StartDate = $_POST['data']['StartDate'];
                $EndDate = $_POST['data']['EndDate'];
                $date_obj1 = DateTime::createFromFormat('d/m/Y', $StartDate);
                $date_str1 = $date_obj1->format('Y-m-d');
                $date_obj2 = DateTime::createFromFormat('d/m/Y', $EndDate);
                $date_str2 = $date_obj2->format('Y-m-d');
                $Description = $_POST['data']['Description'];
                $GroupID = $_POST['data']['GroupID'];

                $imagePath = "../wp-content/uploads/gift_cards_upload/" . $ID . ".png";
                unlink($file);

                sqlsrv_query($conn, "BEGIN TRANSACTION");
                $sql = sprintf("UPDATE GiftCardImages SET PinNumber=%d, StartDate='%s', EndDate='%s', Description='%s', GroupID=%d WHERE ID=%d",
                                $PinNumber, $date_str1, $date_str2, $Description, $GroupID, $ID);
                $stmt = sqlsrv_query($conn, $sql);
                if ($stmt === false) {
                    sqlsrv_query($conn, "ROLL BACK");
                    sqlsrv_close($conn);
                    die(print_r(sqlsrv_errors(), true));
                }
                sqlsrv_query($conn, "COMMIT");

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
                'message' => 'Can not connect to SQL Server.'
            );
            echo json_encode($response);
        }
        die;
	}
    add_action('wp_ajax_update_gift_card', 'update_gift_card');
}

if ( ! function_exists('delete_card_data') ) {
	function delete_card_data(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $ID = $_POST['imageId'];

                $imagePath = "../wp-content/uploads/gift_cards_upload/" . $ID . ".png";
                unlink($file);
                
                $sql = sprintf("DELETE FROM GiftCardImages WHERE ID=%d", $ID);
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
                'message' => 'Can not connect to SQL Server.'
            );
            echo json_encode($response);
        }
        die;
	}
    add_action('wp_ajax_delete_card_data', 'delete_card_data');
}

?>