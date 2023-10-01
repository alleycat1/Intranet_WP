<?php
     global $serverName;
     global $connectionInfo;
 
     $conn = sqlsrv_connect($serverName, $connectionInfo);
 
     if($conn)
     {
?>


<script>
    jQuery(document).ready(function ($) {
        initializeWidgets();
    });
</script>
<div id="widgetsContainer">
</div>


<?php
     }
     else
         print_r(sqlsrv_errors());
?>