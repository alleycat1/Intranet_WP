var card_data = new Array();
var image_content = null;

function delete_card(id) {
    jQuery('#card_grid').jqxGrid('endcelledit');
    if(confirm("Are you sure you want to delete this Gif card?"))
        delete_card_data(id);
}
function initializeCardWidgets() {
    jQuery("#jqxCardGroup").jqxDropDownList({ source: groups, selectedIndex: 0, width: '200px', height: '30px'});
    jQuery("#jqxCardGroup").on('select', function (e) {
        jQuery('#card_grid').jqxGrid('endcelledit');
        group = jQuery("#jqxCardGroup").val();
        stats = jQuery("#jqxCardStatus").val();
        get_gift_cards(group, stats);
    });

    jQuery("#jqxCardStatus").jqxDropDownList({ source: statusList, selectedIndex: 0, width: '200px', height: '30px'});
    jQuery("#jqxCardStatus").on('select', function (e) {
        jQuery('#card_grid').jqxGrid('endcelledit');
        group = jQuery("#jqxCardGroup").val();
        stats = jQuery("#jqxCardStatus").val();
        get_gift_cards(group, stats);
    });

    jQuery("#jqxInputCardGroup").jqxDropDownList({ source: groups1, selectedIndex: 0, width: '177px', height: '30px'});
    jQuery("#jqxStartDate").jqxDateTimeInput({ animationType: 'fade', width: '177px', height: '30px', dropDownHorizontalAlignment: 'left'});
    jQuery("#jqxEndDate").jqxDateTimeInput({ animationType: 'fade', width: '177px', height: '30px', dropDownHorizontalAlignment: 'left'});

    jQuery("#popupAddCard").jqxWindow({
        width: 750, height:460, resizable: false,  isModal: true, autoOpen: false, cancelButton: jQuery("#CancelCard"), modalOpacity: 0.01
    });

    var sourceCards =
    {
         localdata: card_data,
         datatype: "array",
         updaterow: function (rowid, rowdata, commit) {
            var formattedDate1 = rowdata.StartDate.getDate().toString().padStart(2, '0') + '/' +
                            (rowdata.StartDate.getMonth() + 1).toString().padStart(2, '0') + '/' +
                            rowdata.StartDate.getFullYear();
            var formattedDate2 = rowdata.EndDate.getDate().toString().padStart(2, '0') + '/' +
                            (rowdata.StartDate.getMonth() + 1).toString().padStart(2, '0') + '/' +
                            rowdata.StartDate.getFullYear();
            var data = { ID: rowdata.ID,
            PinNumber: rowdata.PinNumber,
            GroupID: rowdata.GroupID,
            StartDate: formattedDate1,
            EndDate: formattedDate2,
            Description: rowdata.Description};
            update_gift_card(data);
         },
         datafields:
         [
              { name: 'ID', type: 'number' },
              { name: 'PinNumber', type: 'number' },
              { name: 'StartDate', type: 'date', format: 'dd/MM/yyyy' },
              { name: 'EndDate', type: 'date', format: 'dd/MM/yyyy' },
              { name: 'Description', type: 'string' },
              { name: 'GroupID', type: 'number' },
              { name: 'Status', type: 'string' },
              { name: 'deleteID', type: 'number' },
         ]
    };
    var dataAdapterCard = new jQuery.jqx.dataAdapter(sourceCards);

    var cellsrenderer_no = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        element[0].innerHTML = row + 1;
        return element[0].outerHTML;
    }

    var cellsrenderer_group = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        element[0].innerHTML = groups1[parseInt(value)];
        return element[0].outerHTML;
    }

    var cellsrenderer_status = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        element[0].innerHTML = status_names[parseInt(value)].label;
        return element[0].outerHTML;
    }

    var imagerenderer = function (row, datafield, value) {
        return '<img style="width: 100%; height: 100%; object-fit: cover; cursor:pointer" height="60" width="60" src="' + upload_url + "/gift_cards_upload/" + card_data[row].ID + '.png" onclick="javascript:gift_card_show_from_table(this)" />';
    }

    var cellsrenderer_group = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        element[0].innerHTML = "<center>" + groups1[value] + "</center>";
        return element[0].outerHTML;
    }

    var createGridEditor_group = function(row, cellValue, editor, cellText, width, height)
    {
        if(width == undefined)
        {
            width = 80;
            height = 60;
        }
        editor.jqxDropDownList({autoDropDownHeight: true,  width: width, height: height, source: groups1});
    }

    var initGridEditor = function (row, cellValue, editor, cellText, width, height) {
        editor.jqxDropDownList('selectItem', cellValue);
    }

    var gridEditorValue = function (row, cellValue, editor) {
        return editor.val();
    }

    jQuery("#card_grid").jqxGrid(
    {
         width: 810,
         height: 450,
         source: dataAdapterCard,
         columnsresize: true,
         editable: true,
         rowsheight: 60,
         selectionmode: 'singlerow',
         columns: [
              { text: 'NO', columntype: 'integerinput', width: 60, align: 'center', cellsalign: 'right', pinned: true, cellsrenderer:cellsrenderer_no, editable:false },
              { text: 'ID', datafield:'ID', width: 100, align: 'center', cellsalign: 'right', pinned: true, editable:false },
              { text: 'IMAGE', width: 60, align: 'center', cellsrenderer: imagerenderer, pinned: true, editable:false },
              { text: 'PIN', datafield:"PinNumber", columntype: 'integerinput', width: 60, align: 'center', cellsalign: 'right'},
              { text: 'START DATE', datafield: 'StartDate' , columntype: 'datetimeinput', cellsformat: 'dd/MM/yyyy', width: 100, align: 'center', cellsalign: 'right'},
              { text: 'END DATE', datafield: 'EndDate' , columntype: 'datetimeinput', cellsformat: 'dd/MM/yyyy', width: 100, align: 'center', cellsalign: 'right'},
              { text: 'DESCRIPTION', datafield: 'Description', columntype: 'textbox', align: 'center', cellsalign: 'left', width: 100},
              { text: 'GROUP', datafield: 'GroupID', columntype: 'numberinput', align: 'center', cellsalign: 'center', width: 80, cellsrenderer:cellsrenderer_group, createeditor:createGridEditor_group, initeditor:initGridEditor, geteditorvalue:gridEditorValue},
              { text: 'STATUS', datafield: 'Status', columntype: 'textbox', align: 'center', cellsalign: 'center', width: 80, cellsrenderer:cellsrenderer_status, editable:false},
              { text: '', datafield:'deleteID', width: 25, resizable:false,
                createwidget: function (row, column, value, htmlElement) {
                    if(value + "" != "")
                    {
                        var element = jQuery(htmlElement);
                        element[0].innerHTML = "<span class='material-symbols-outlined' style='cursor:pointer;margin-top:16px' onclick='javascript:delete_card(" + value + ")'>delete</span>";
                    }
                },
                initwidget: function (row, column, value, htmlElement) {
                    if(value + "" != "")
                    {
                        var element = jQuery(htmlElement);
                        element[0].innerHTML = "<span class='material-symbols-outlined' style='cursor:pointer;margin-top:16px' onclick='javascript:delete_card(" + value + ")'>delete</span>";
                    }
                }
            }
         ]
    });

    jQuery("#card_grid").on("rowselect", function (event) {
        var args = event.args;
        var rowData = args.row;
        document.getElementById('card_image_showing').src = upload_url + "/gift_cards_upload/" + rowData.ID + ".png";
    });

    jQuery("#card_add").click(function(event) {
        jQuery('#card_grid').jqxGrid('endcelledit');
        jQuery("#popupAddCard").jqxWindow({ position: { x: 400, y: 230 } });
        jQuery("#popupAddCard").jqxWindow('open');
        document.getElementById("card_number").value = "";
        document.getElementById("pin_number").value = "";
        document.getElementById("card_image_path").value = "";
        event.preventDefault();
    });

    jQuery("#CardSave").click(function () {
        if(document.getElementById("card_number").value == "" || isNaN(parseInt(document.getElementById("card_number").value)) || document.getElementById("card_number").value.length != 9)
        {
            alert("Please input the correct card number.");
            return;
        }

        if(document.getElementById("pin_number").value == "" || isNaN(parseInt(document.getElementById("pin_number").value)) || document.getElementById("pin_number").value.length != 4)
        {
            alert("Please input the correct PIN number.");
            return;
        }

        if(document.getElementById("card_image_path").files.length == 0)
        {
            alert("Please select the gift image.");
            return;
        }

        let locale = "en-CA";
        let options = {year: "numeric", month: "numeric", day: "numeric"};
        var date_str1 = jQuery("#jqxStartDate").jqxDateTimeInput('getDate').toLocaleDateString(locale, options);
        var date_str2 = jQuery("#jqxEndDate").jqxDateTimeInput('getDate').toLocaleDateString(locale, options);
        if(date_str1 > date_str2)
        {
            alert("End Date must be after Start Date.");
            return;
        }
        if(image_content == null)
        {
            alert("Please wait just a second until image is uploaded.");
            return;
        }
        var data = { ID:document.getElementById("card_number").value,
            PinNumber:document.getElementById("pin_number").value,
            GroupID:jQuery("#jqxInputCardGroup").val(),
            StartDate: jQuery("#jqxStartDate").val(),
            EndDate: jQuery("#jqxEndDate").val(),
            Description:document.getElementById("description").value,
            Image: image_content};
        save_gift_card(data);
    });
}

function gift_card_show(e)
{
    if(e.files.length > 0)
    {
        var reader = new FileReader();
        reader.readAsDataURL(e.files[0]);
        image_content = null;
        reader.onload = function(e) {
            image_content = e.target.result;
            document.getElementById('card_image_temp_showing').src = image_content;
        };
    }
}

function gift_card_show_from_table(e)
{
    document.getElementById('card_image_showing').src = e.src;
}
