<?php
if ( ! function_exists('save_gift_image') ) {
	function save_gift_image(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $Description = $_POST['data']['Description'];
                $GroupID = $_POST['data']['GroupID'];
                $imageData = $_POST['data']['Image'];
                $imageContent = base64_decode(substr($imageData, 22));

                sqlsrv_query($conn, "BEGIN TRANSACTION");
                $sql = "SELECT MAX(ID)+1 new_id FROM GiftCardImages";
                $stmt = sqlsrv_query($conn, $sql);
                if ($stmt === false) {
                    sqlsrv_close($conn);
                    die(print_r(sqlsrv_errors(), true));
                }
                $new_id = 1;
                while ($r = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $new_id = $r['new_id'];
                }
                if($new_id == "")
                    $new_id = 1;

                $imagePath = "../wp-content/uploads/gift_images_upload/" . $new_id . ".png";
                $handle = fopen($imagePath, "w");
                if($handle)
                {
                    fwrite($handle, $imageContent);
                    fclose($handle);
                }
                else{
                    $response = array(
                        'status' => 'failed',
                        'message' => 'Can not write image file.'
                    );
                    echo json_encode($response);
                    die;
                }

                $sql = sprintf("INSERT INTO GiftCardImages(ID, Description, GroupID) VALUES(%d, '%s', %d)",
                                $new_id, $Description, $GroupID);
                $stmt = sqlsrv_query($conn, $sql);
                if ($stmt === false) {
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
    add_action('wp_ajax_save_gift_image', 'save_gift_image');
}

if ( ! function_exists('get_gift_images') ) {
	function get_gift_images(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $group = $_POST['group'];
                $outlet = $_POST['outlet'];

                $pv_base_fields = array();
                $pv_fields = array();
                $sql = "SELECT ID FROM Outlets WHERE Deleted <> 1";
                $stmt = sqlsrv_query($conn, $sql);
                if ($stmt === false) {
                    print_r(sqlsrv_errors());
                    sqlsrv_close($conn);
                    die();
                }
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $pv_base_fields[count($pv_base_fields)] = '[' . $row['ID'] . '] AS outlet_' . $row['ID'];
                    $pv_fields[count($pv_fields)] = '[' . $row['ID'] . ']';
                }
                if($outlet == 0 && $group == 0)
                    $sql = "SELECT ID, Description, GroupID, " . join(",", $pv_base_fields) . "
                            FROM (SELECT i.ID, i.Description, GroupID, ImageID, OutletID FROM GiftCardImages i LEFT JOIN OutletsGiftCards o ON o.ImageID=i.ID LEFT JOIN GiftCardCategories g ON g.ID=i.GroupID WHERE g.Active=1) p
                            PIVOT(COUNT(ImageID) FOR OutletID IN (" . join(",", $pv_fields) . ")) AS pv";
                else if($outlet != 0 && $group == 0)
                    $sql = "SELECT ID, Description, GroupID, " . join(",", $pv_base_fields) . "
                            FROM (SELECT i.ID, i.Description, GroupID, ImageID, OutletID FROM GiftCardImages i LEFT JOIN OutletsGiftCards o ON o.ImageID=i.ID LEFT JOIN GiftCardCategories g ON g.ID=i.GroupID WHERE g.Active=1 AND OutletID=$outlet) p
                            PIVOT(COUNT(ImageID) FOR OutletID IN (" . join(",", $pv_fields) . ")) AS pv";
                else if($outlet == 0 && $group != 0)
                    $sql = "SELECT ID, Description, GroupID, " . join(",", $pv_base_fields) . "
                            FROM (SELECT i.ID, i.Description, GroupID, ImageID, OutletID FROM GiftCardImages i LEFT JOIN OutletsGiftCards o ON o.ImageID=i.ID LEFT JOIN GiftCardCategories g ON g.ID=i.GroupID WHERE g.Active=1 AND GroupID=$group) p
                            PIVOT(COUNT(ImageID) FOR OutletID IN (" . join(",", $pv_fields) . ")) AS pv";
                else
                    $sql = "SELECT ID, Description, GroupID, " . join(",", $pv_base_fields) . "
                            FROM (SELECT i.ID, i.Description, GroupID, ImageID, OutletID FROM GiftCardImages i LEFT JOIN OutletsGiftCards o ON o.ImageID=i.ID LEFT JOIN GiftCardCategories g ON g.ID=i.GroupID WHERE g.Active=1 AND OutletID=$outlet AND GroupID=$group) p
                            PIVOT(COUNT(ImageID) FOR OutletID IN (" . join(",", $pv_fields) . ")) AS pv";
                $stmt = sqlsrv_query($conn, $sql);

                if ($stmt === false) {
                    print_r(sqlsrv_errors());
                    sqlsrv_close($conn);
                    die();
                }

                $count = 0;
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    foreach($row as $id => $value)
                    {
                        $res[$count][$id] = $value;
                    }
                    $count++;
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
    add_action('wp_ajax_get_gift_images', 'get_gift_images');
}

if ( ! function_exists('update_gift_image') ) {
	function update_gift_image(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $ID = $_POST['data']['ID'];
                $Description = $_POST['data']['Description'];
                $GroupID = $_POST['data']['GroupID'];
                $outlets = array();

                sqlsrv_query($conn, "BEGIN TRANSACTION");
                $sql = sprintf("DELETE FROM OutletsGiftCards WHERE ImageID=%d", $ID);
                sqlsrv_query($conn, $sql);
                foreach($_POST['data'] as $id => $value)
                {
                    if(strpos($id, "outlet_") !== false)
                    {
                        if($value == "true")
                        {
                            $sql = sprintf("INSERT INTO OutletsGiftCards VALUES(%d, %d)", $ID, substr($id, 7) + 0);
                            sqlsrv_query($conn, $sql);
                        }
                    }
                }
                $sql = sprintf("UPDATE GiftCardImages SET Description='%s', GroupID=%d WHERE ID=%d",
                                $Description, $GroupID, $ID);
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
    add_action('wp_ajax_update_gift_image', 'update_gift_image');
}

if ( ! function_exists('delete_card_image') ) {
	function delete_card_image(){
        header('Content-Type: application/json');
        require __DIR__ ."/../../../db_config.php";
        global $serverName;
        global $connectionInfo;
        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if($conn)
        {
            if(isset($_POST) && !empty($_POST)){
                $ID = $_POST['ID'];

                $imagePath = "../wp-content/uploads/gift_images_upload/" . $ID . ".png";
                unlink($imagePath);
                
                sqlsrv_query($conn, "BEGIN TRANSACTION");
                $sql = sprintf("DELETE FROM OutletsGiftCards WHERE ImageID=%d", $ID);
                $stmt = sqlsrv_query($conn, $sql);
                if ($stmt === false) {
                    sqlsrv_close($conn);
                    die(print_r(sqlsrv_errors(), true));
                }
                $sql = sprintf("DELETE FROM GiftCardImages WHERE ID=%d", $ID);
                $stmt = sqlsrv_query($conn, $sql);
                if ($stmt === false) {
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
    add_action('wp_ajax_delete_card_image', 'delete_card_image');
}

?>