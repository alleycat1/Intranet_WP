<style type="text/css">
     .paid_out:hover {
          color: #FF00FF;
     }
     .paid_out {
          cursor: pointer;
     }
     .my_td{
        padding-top:5px;
        padding-bottom:5px;
        border:0px;
     }
</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
<?php
wp_enqueue_script( 'jqxcore' );
wp_enqueue_script( 'jqxdatetimeinput' );
wp_enqueue_script( 'jqxcalendar' );
wp_enqueue_script( 'jqxtooltip' );
wp_enqueue_script( 'jqxbuttons' );
wp_enqueue_script( 'jqxmenu' );
wp_enqueue_script( 'jqxdata' );
wp_enqueue_script( 'jqxscrollbar' );
wp_enqueue_script( 'jqxgrid' );
wp_enqueue_script( 'jqxgrid.edit' );
wp_enqueue_script( 'jqxgrid.columnsresize', );
wp_enqueue_script( 'jqxgrid.sort' );
wp_enqueue_script( 'jqxgrid.selection' );
wp_enqueue_script( 'jqxlistbox' );
wp_enqueue_script( 'jqxdropdownlist' );
wp_enqueue_script( 'jqxcheckbox' );
wp_enqueue_script( 'jqxnumberinput' );
wp_enqueue_script( 'jqxsplitter' );
wp_enqueue_script( 'jqxdata.export' );
wp_enqueue_script( 'jqxgrid.export' );
wp_enqueue_script( 'jqxcombobox' );
wp_enqueue_script( 'jqxpopover' );
wp_enqueue_script( 'jqxwindow' );
wp_enqueue_script( 'jqxinput' );

// enqueue jQWidgets CSS files
wp_enqueue_style( 'jqx.base' );
wp_enqueue_style( 'jqx.orange' );

wp_enqueue_script(GIFT_CARD_SETUP_NAME . '_setup',  GIFT_CARD_SETUP_PLUGIN_DIR . '/gift_card_setup.js', array('jquery'), GIFT_CARD_SETUP_VAR, true);
wp_enqueue_script(GIFT_CARD_SETUP_NAME . '_ajax',  GIFT_CARD_SETUP_PLUGIN_DIR . '/gift_card_setup_ajax.js', array('jquery'), GIFT_CARD_SETUP_VAR, true);

require_once(ABSPATH . 'wp-admin/includes/file.php');

$uploads = wp_upload_dir();
$upload_url = $uploads['baseurl'];

global $serverName;
global $connectionInfo;

$conn = sqlsrv_connect($serverName, $connectionInfo);

if(!$conn)
    print_r(sqlsrv_errors());

function getOutLetData($conn, &$outlets, &$outlets1)
{
     $sql = "SELECT ID, OutletCode, OutletName FROM Outlets WHERE Deleted <> 1";
     $stmt = sqlsrv_query($conn, $sql);
     if ($stmt === false) {
          return;
     }
     $outlets[0] = ['label'=>'ALL', 'value'=>'0'];
     while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $outlets[count($outlets)] = ['label'=>$row['OutletName'], 'value'=>$row['ID']];
          $outlets1[count($outlets1)] = ['label'=>$row['OutletName'], 'value'=>$row['ID']];
     }
}

function getCardGroups($conn, &$cardGroups)
{
     $sql = "SELECT ID, Description FROM GiftCardCategories";
     $stmt = sqlsrv_query($conn, $sql);
     if ($stmt === false) {
          return;
     }
     while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $cardGroups[$row['ID']] = $row['Description'];
     }
}

$user = wp_get_current_user();
if(in_array("administrator", $user->roles) || in_array("editor", $user->roles))
{
    $is_admin = true;
}
if($is_admin == false)
    echo "Please sign up first!";
else {
    $outlets = array();
    $outlets1 = array();
    getOutLetData($conn, $outlets, $outlets1);

    $cardCroups = array();
    getCardGroups($conn, $cardCroups);

    echo "<script>";
    echo "var outlets=" . json_encode($outlets) . ";";
    echo "var outlets1=" . json_encode($outlets1) . ";";

    echo "var upload_url='".$upload_url."';";
    $group_obj = array();
    $group_obj1 = array();
    $first_id = "";
    $group_obj[count($group_obj)] = ['label'=>'ALL', 'value'=>'0'];
    foreach($cardCroups as $groupId => $group_desc)
    {
        $group_obj[count($group_obj)] = ['label'=>$group_desc, 'value'=>$groupId];
        $group_obj1[count($group_obj1)] = ['label'=>$group_desc, 'value'=>$groupId];
    }
    echo "var groups=" . json_encode($group_obj) . ";";
    echo "var groups1=" . json_encode($cardCroups) . ";";

    echo "</script>";
?>

<script>
jQuery(document).ready(function ($) {
    initializeCardWidgets();
    get_gift_images(0, 0);
});
</script>

<table border=0 height=500 style="border: none; width:1250px">
    <tr>
        <td style="border: none; width:1250px">
            <div style='width: 1250px;'>
                <span style="border:0px; width:1250px; text-align:center"><H1>Gift Card Setup</H1></span>
            </div>
            <div style="height:50px">
                <table border=0 height=50>
                    <tr>
                        <td style="padding:10px; border:none">Group:</td>
                        <td style="padding:0px; border:none"><div style='float: left;' id='jqxCardGroup'></div></td>
                        <td style="padding:10px; padding-left:20px; border:none">Outlets:</td>
                        <td style="padding:0px; border:none"><div style='float: left;' id='jqxOutlets'></div></td>
                        <td width=100% style="border:none">&nbsp;</td>
                    </tr>
                </table>
            </div>
            <div style="border: none; padding-top:10px" id='jqxCards'>
                <table border=0 height=450>
                    <tr>
                        <td style="border:none; padding:0px">
                            <div id="card_grid" style="width:800px"></div>
                        </td>
                        <td style="border:none; padding:0px">
                            <div id="card_image_div" style="width:450px; height:450px; border:1px solid lightgray">
                                <img id="card_image_showing" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </td>
                    </tr>
                </table>
                <button style="padding:4px 16px;" id="card_add">&nbsp;ADD CARD&nbsp;</button> 
            </div>
        </td>
    </tr>
</table>

<div id="popupAddCard" hidden>
    <div>ADD GIFT CARD</div>
    <div style="overflow: hidden;">
        <form id="card_form" enctype="multipart/form-data" name="card_form">
            <table width=100%>
                <tr>
                    <td style="padding:0px; border:none; padding-top:10px" valign=top>
                        <div style="width:380px; height:380px; border:1px solid lightgray">
                            <img id="card_image_temp_showing" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0px; border:none" class="my_td">
                        <table>
                            <tr>
                                <td class="my_td" style="text-align:right; width:80px;">GROUP:</td>
                                <td class="my_td" style="text-align:left"><div style='float: left' id='jqxInputCardGroup'></td>
                            </tr>
                            <tr>
                                <td class="my_td" style="text-align:right; width:80px;">IMAGE:</td>
                                <td class="my_td" style="text-align:left" ><input type="file" id="card_image_path" onchange="javascript:gift_card_show(this)" accept="image/png" style="height:30px; width:177px; margin-top:5px" required/></td>
                            </tr>
                            <tr>
                                <td class="my_td" style="text-align:right; width:80px;">DESCRIPTION:</td>
                                <td class="my_td" style="text-align:left"><input type="text" id="description" style="height:30px; width:177px" /></td>
                            </tr>
                            <tr>
                                <td class="my_td" style="text-align:right; width:80px;">&nbsp;</td>
                                <td class="my_td" style="">
                                    <input id="CancelCard" type="button" value="CLOSE" />
                                    <input type="button" id="CardSave" value="  ADD  " />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>                
            </table>
        </form>
    </div>
</div>
<div style="width:100%; height:100%; position:absolute; left:0px; top:0px; visibility:hidden; z-order:1000" id="disable_pane">
</div>
<?php } ?>