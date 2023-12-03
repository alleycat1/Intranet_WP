var card_data = new Array();
var image_content = null;

function delete_card(id) {
    jQuery('#card_grid').jqxGrid('endcelledit');
    if(confirm("Are you sure you want to delete this Gif card?"))
        delete_card_image(id);
}
function initializeCardWidgets() {
    jQuery("#jqxCardGroup").jqxDropDownList({ source: groups, selectedIndex: 0, width: '200px', height: '30px'});
    jQuery("#jqxCardGroup").on('select', function (e) {
        jQuery('#card_grid').jqxGrid('endcelledit');
        group = jQuery("#jqxCardGroup").val();
        outlets = jQuery("#jqxOutlets").val();
        get_gift_images(group, outlets);
    });

    jQuery("#jqxOutlets").jqxDropDownList({ source: outlets, selectedIndex: 0, width: '200px', height: '30px'});
    jQuery("#jqxOutlets").on('select', function (e) {
        jQuery('#card_grid').jqxGrid('endcelledit');
        group = jQuery("#jqxCardGroup").val();
        outlets = jQuery("#jqxOutlets").val();
        get_gift_images(group, outlets);
    });

    jQuery("#jqxInputCardGroup").jqxDropDownList({ source: groups1, selectedIndex: 0, width: '177px', height: '30px'});

    jQuery("#popupAddCard").jqxWindow({
        width: 390, height:630, resizable: false,  isModal: true, autoOpen: false, cancelButton: jQuery("#CancelCard"), modalOpacity: 0.01
    });

    var sourceCards =
    {
         localdata: card_data,
         datatype: "array",
         updaterow: function (rowid, rowdata, commit) {
            var data = { ID: rowdata.ID,
            Description: rowdata.Description,
            GroupID: rowdata.GroupID };
            for (var outlet in outlets1) {
                data['outlet_' + outlets1[outlet].value] = rowdata['outlet_' + outlets1[outlet].value];
            }
            update_gift_image(data);
         },
         datafields:
         [
              { name: 'ID', type: 'number' },
              { name: 'Description', type: 'string' },
              { name: 'GroupID', type: 'number' },
         ]
    };
    for (var outlet in outlets1) {
        sourceCards.datafields.push({name: 'outlet_' + outlets1[outlet].value, type: 'bool'});
    }
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

    var imagerenderer = function (row, datafield, value) {
        return '<img style="width: 100%; height: 100%; object-fit: cover; cursor:pointer" height="100%" width="100%" src="' + upload_url + "/gift_images_upload/" + card_data[row].ID + '.png" onclick="javascript:gift_card_show_from_table(this)" />';
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

    var columnData = [
        { text: 'NO', columntype: 'integerinput', width: 60, align: 'center', cellsalign: 'right', pinned: true, cellsrenderer:cellsrenderer_no, editable:false },
        { text: 'IMAGE', width: 70, align: 'center', cellsrenderer: imagerenderer, pinned: true, editable:false },
        { text: 'DESCRIPTION', datafield: 'Description', columntype: 'textbox', align: 'center', cellsalign: 'left', width: 100},
        { text: 'GROUP', datafield: 'GroupID', columntype: 'numberinput', align: 'center', cellsalign: 'center', width: 80, cellsrenderer:cellsrenderer_group, createeditor:createGridEditor_group, initeditor:initGridEditor, geteditorvalue:gridEditorValue},
    ];
    for (var outlet in outlets1) {
        columnData.push({text: outlets1[outlet].label, datafield: 'outlet_' + outlets1[outlet].value, threestatecheckbox: false, columntype: 'checkbox', width: 90});
    }
    columnData.push({ 
        text: '', datafield:'ID', width: 25, resizable:false,
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
    });

    jQuery("#card_grid").jqxGrid(
    {
         width: 810,
         height: 450,
         source: dataAdapterCard,
         columnsresize: true,
         editable: true,
         rowsheight: 60,
         selectionmode: 'singlerow',
         columns: columnData
    });

    jQuery("#card_grid").on("rowselect", function (event) {
        var args = event.args;
        var rowData = args.row;
        document.getElementById('card_image_showing').src = upload_url + "/gift_images_upload/" + rowData.ID + ".png";
    });

    jQuery("#card_add").click(function(event) {
        jQuery('#card_grid').jqxGrid('endcelledit');
        jQuery("#popupAddCard").jqxWindow({ position: { x: 400, y: 230 } });
        jQuery("#popupAddCard").jqxWindow('open');
        document.getElementById("card_image_path").value = "";
        document.getElementById("card_image_temp_showing").src = "";
        event.preventDefault();
    });

    jQuery("#CardSave").click(function () {
        if(document.getElementById("card_image_path").files.length == 0)
        {
            alert("Please select the gift image.");
            return;
        }
        if(image_content == null)
        {
            alert("Please wait just a second until image is uploaded.");
            return;
        }
        var data = {
            GroupID:jQuery("#jqxInputCardGroup").val(),
            Description:document.getElementById("description").value,
            Image: image_content
        };
        save_gift_image(data);
        jQuery("#popupAddCard").jqxWindow('close');
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
