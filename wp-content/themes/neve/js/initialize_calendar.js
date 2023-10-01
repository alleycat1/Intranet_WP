function initializeWidgets() {
    jQuery("#widgetsContainer").html('<div id="jqxCalendar"></div><br /><div id="jqxButton">Change the date</div>');
    jQuery("#jqxCalendar").jqxCalendar({ theme: "orange", width: 220, height: 220 });
    jQuery("#jqxButton").jqxButton({ theme: "orange", width: 150 });
    jQuery("#jqxButton").click(function () {
        jQuery('#jqxCalendar ').jqxCalendar('setDate', new Date(2013, 11, 31));
    });
}