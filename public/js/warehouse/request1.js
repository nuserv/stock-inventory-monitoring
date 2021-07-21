$('table.requestDetails').DataTable().on('select', function () {
    var rowselected = requestdetails.rows( { selected: true } ).data();
    var rowcount = requestdetails.rows( { selected: true } ).count();
    if(rowselected.length > 0){
        for(var i=0;i<rowcount;i++){
            if (rowselected[i].stock == 0) {
                $('#prcBtn').prop('disabled', true);
                requestdetails.rows( { selected: true } ).deselect();
                alert(rowselected[i].item_name+' is out of stock.')
                return false;
            }else{
                $('#prcBtn').prop('disabled', false);
            }
        }  
    }
});

$('table.requestDetails').DataTable().on('deselect', function () {
    var rowselected = requestdetails.rows( { selected: true } ).data();
    if(rowselected.length == 0){
        $('#prcBtn').prop('disabled', true);
    }    
});

$(document).on('change', '#datesched', function(){
    var seldate = new Date($('#datesched').val());
    var dd = String(seldate.getDate()).padStart(2, '0');
    var mm = String(seldate.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = seldate.getFullYear();
    seldate = yyyy + '-' + mm + '-' + dd;
    var today = new Date();
    var datval = moment(seldate, 'YYYY-MM-DD', true).isValid();
    seldate = seldate.split("-");
    var newdate = new Date(seldate[2], seldate[0], seldate[1]);
    if (datval) {
        if(newdate < today) {
            alert('Invalid Date!');
        }
    }else{
        alert('Invalid Date!');
    }
});

$(document).on('change', '#resched', function(){
    var seldate = new Date($('#resched').val());
    var dd = String(seldate.getDate()).padStart(2, '0');
    var mm = String(seldate.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = seldate.getFullYear();
    seldate = yyyy + '-' + mm + '-' + dd;
    var today = new Date();
    var datval = moment(seldate, 'YYYY-MM-DD', true).isValid();
    seldate = seldate.split("-");
    var newdate = new Date(seldate[2], seldate[0], seldate[1]);
    if (datval) {
        if(newdate < today) {
            alert('Invalid Date!');
        }
    }else{
        alert('Invalid Date!');
    }
});
$(document).on('click', '#closeBtn', function(){
    $("#requestModal .close").click();

});

$(document).on('click', '#prcBtn', function(){
    $("#requestModal .closes").click();
    $('#loading').show();
    $('#sdate').val($('#date').val());
    $('#sreqno').val(reqnumber);
    $('#sbranch').val($('#branch').val());
    $('#sname').val($('#name').val());
    $('table.sendDetails').dataTable().fnDestroy();
    $('table.sendDetails').DataTable({ 
        "dom": 'rtp',
        "language": {
            "emptyTable": " "
        },
        processing: true,
        serverSide: true,
        ajax: "/requests/"+$('#sreqno').val(),
        columns: [
            { data: 'items_id', name:'items_id'},
            { data: 'item_name', name:'item_name'},
            { data: 'qty', name:'qty'},
            { data: 'stockuom', name:'stockuom'}
        ]
    });
    var rowselected = requestdetails.rows( { selected: true } ).data();
    var rowcount = requestdetails.rows( { selected: true } ).count();
    var requestcount = requestdetails.data().count();
    if (rowcount < requestcount) {
        requestgo = false;
    }else{
        requestgo = true;
        if (valid == 'no') {
            requestgo = false;
        }
    }
    
 
    for(var i=0;i<rowcount;i++){
        if (rowselected[i].uom == 'Unit') {
            if (rowselected[i].pending <= rowselected[i].stock) {
                for(var e=0;e<rowselected[i].pending;e++){
                    w++;
                    var additem = '<div class="row no-margin" id="row'+w+'"><div class="col-md-2 form-group"><select id="categ'+w+'" class="form-control categ" row_count="'+w+'" style="color:black"><option selected value="'+rowselected[i].category+'">'+rowselected[i].cat_name+'</option></select></div>  <div class="col-md-3 form-group"><select id="desc'+w+'" class="form-control desc" row_count="'+w+'" style="color:black"><option selected disabled>select item description</option><option value="'+rowselected[i].items_id+'">'+rowselected[i].item_name+'</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control serial" row_count="'+w+'" id="serial'+w+'" placeholder="input serial" style="color:black" autocomplete="off" onkeypress="return event.charCode >= 47" onkeyup="checkserial(this)"></div><div class="col-md-2 form-group"><input type="text" class="form-control uom" row_count="'+w+'" id="uom'+w+'" value="'+rowselected[i].uom+'" style="color:black" autocomplete="off" disabled></div><div class="col-md-2 form-group"><select id="item'+w+'" class="form-control item" row_count="'+w+'" style="color:black"><option selected value="'+rowselected[i].items_id+'">'+rowselected[i].items_id+'</option></select></div></div>'
                    //var additem = '<div class="row no-margin" id="row'+w+'"><div class="col-md-2 form-group"><select id="categ'+w+'" class="form-control categ" row_count="'+w+'" style="color:black"><option selected value="'+rowselected[i].category+'">'+rowselected[i].cat_name+'</option></select></div>  <div class="col-md-3 form-group"><select id="desc'+w+'" class="form-control desc" row_count="'+w+'" style="color:black"><option selected disabled>select item description</option><option value="'+rowselected[i].items_id+'">'+rowselected[i].item_name+'</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control inputqty" row_count="'+w+'" id="inputqty'+w+'" maxlength="2" min="0" max="'+maxqty+'" value="0" style="color:black" onkeypress="return event.charCode >= 47" onkeyup=imposeMinMax(this) onkeyup="checkserial(this)"></div><div class="col-md-2 form-group"><input type="text" class="form-control uom" row_count="'+w+'" id="uom'+w+'" value="'+rowselected[i].uom+'" style="color:black" autocomplete="off" disabled></div><div class="col-md-2 form-group"><select id="item'+w+'" class="form-control item" row_count="'+w+'" style="color:black"><option selected value="'+rowselected[i].items_id+'">'+rowselected[i].items_id+'</option></select></div></div>'
                    //var additem = '<div class="row no-margin" id="row'+w+'"><div class="col-md-2 form-group"><select id="item'+w+'" class="form-control item" row_count="'+w+'" style="color:black"><option selected value="'+rowselected[i].items_id+'">'+rowselected[i].items_id+'</option></select></div><div class="col-md-3 form-group"><select id="desc'+w+'" class="form-control desc" row_count="'+w+'" style="color:black"><option selected disabled>select item description</option><option value="'+rowselected[i].items_id+'">'+rowselected[i].item_name+'</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control serial" row_count="'+w+'" id="serial'+w+'" placeholder="input serial" style="color:black" autocomplete="off" onkeypress="return event.charCode >= 47" onkeyup="checkserial(this)"></div></div>'
                    $('#reqfield').append(additem);
                    if (rowselected[i].cat_name.toLowerCase().indexOf('kit') != -1) {
                        var descOp = " ";
                        var itemOp = " ";
                        $.ajax({
                            url: 'getitems',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                            },
                            dataType: 'json',
                            async: false,
                            type: 'get',
                            data: {
                                catid : rowselected[i].category,
                            },
                            success: function (data) {
                                var itemcode = $.map(data, function(value, index) {
                                    return [value];
                                });
                                descOp+='<option selected disabled>select item description</option>';
                                itemOp+='<option selected disabled>select item description</option>';
                                itemcode.forEach(value => {
                                    
                                    descOp+='<option value="'+value.items_id+'">'+value.item.toUpperCase()+'</option>';
                                });
                                itemOp+='<option value="'+rowselected[i].items_id+'">'+rowselected[i].items_id+'</option>';
                                $("#item" + w).find('option').remove().end().append(itemOp);
                                $("#desc" + w).find('option').remove().end().append(descOp);
                            }
                        });
                    }
                    $('#desc'+w).val(rowselected[i].items_id);
                    $('#item'+w).val(rowselected[i].items_id);
                        $('#item'+w).hide();
                }
            }else if(rowselected[i].pending > rowselected[i].stock){
                for(var e=0;e<rowselected[i].stock;e++){
                    w++;
                    var additem = '<div class="row no-margin" id="row'+w+'"><div class="col-md-2 form-group"><select id="categ'+w+'" class="form-control categ" row_count="'+w+'" style="color:black"><option selected value="'+rowselected[i].category+'">'+rowselected[i].cat_name+'</option></select></div>  <div class="col-md-3 form-group"><select id="desc'+w+'" class="form-control desc" row_count="'+w+'" style="color:black"><option selected disabled>select item description</option><option value="'+rowselected[i].items_id+'">'+rowselected[i].item_name+'</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control serial" row_count="'+w+'" id="serial'+w+'" placeholder="input serial" style="color:black" autocomplete="off" onkeypress="return event.charCode >= 47" onkeyup="checkserial(this)"></div><div class="col-md-2 form-group"><input type="text" class="form-control uom" row_count="'+w+'" id="uom'+w+'" value="'+rowselected[i].uom+'" style="color:black" autocomplete="off" disabled></div><div class="col-md-2 form-group"><select id="item'+w+'" class="form-control item" row_count="'+w+'" style="color:black"><option selected value="'+rowselected[i].items_id+'">'+rowselected[i].items_id+'</option></select></div></div>'
                    //var additem = '<div class="row no-margin" id="row'+w+'"><div class="col-md-2 form-group"><select id="item'+w+'" class="form-control item" row_count="'+w+'" style="color:black"><option selected value="'+rowselected[i].items_id+'">'+rowselected[i].items_id+'</option></select></div><div class="col-md-3 form-group"><select id="desc'+w+'" class="form-control desc" row_count="'+w+'" style="color:black"><option selected disabled>select item description</option><option value="'+rowselected[i].items_id+'">'+rowselected[i].item_name+'</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control serial" row_count="'+w+'" id="serial'+w+'" placeholder="input serial" style="color:black" autocomplete="off" onkeypress="return event.charCode >= 47" onkeyup="checkserial(this)"></div></div>'
                    $('#reqfield').append(additem);
                    if (rowselected[i].cat_name.toLowerCase().indexOf('kit') != -1) {
                        var descOp = " ";
                        var itemOp = " ";
                        $.ajax({
                            url: 'getitems',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                            },
                            dataType: 'json',
                            async: false,
                            type: 'get',
                            data: {
                                catid : rowselected[i].category,
                            },
                            success: function (data) {
                                var itemcode = $.map(data, function(value, index) {
                                    return [value];
                                });
                                descOp+='<option selected disabled>select item description</option>';
                                itemOp+='<option selected disabled>select item description</option>';
                                itemcode.forEach(value => {
                                    descOp+='<option value="'+value.items_id+'">'+value.item.toUpperCase()+'</option>';
                                });
                                itemOp+='<option value="'+rowselected[i].items_id+'">'+rowselected[i].items_id+'</option>';
                                $("#item" + w).find('option').remove().end().append(itemOp);
                                $("#desc" + w).find('option').remove().end().append(descOp);
                                console.log('pasok');
                            }
                        });
                       
                    }
                    $('#item'+w).val(rowselected[i].items_id);
                    $('#desc'+w).val(rowselected[i].items_id);
                    $('#item'+w).hide();
                    /*$('#item'+w).val(rowselected[i].items_id);
                    $('#desc'+w).val(rowselected[i].items_id);
                    console.log(rowselected[i].cat_name);
                    $('#item'+w).hide();*/

                }
            }
        }else{
            if (rowselected[i].pending <= rowselected[i].stock) {
                var maxqty = rowselected[i].pending;
            }else{
                var maxqty = rowselected[i].stock;
            }
            w++;
            uomcount = w;
            uomarray.push(w);
            var additem = '<div class="row no-margin" id="row'+w+'"><div class="col-md-2 form-group"><select id="categ'+w+'" class="form-control categ" row_count="'+w+'" style="color:black"><option selected value="'+rowselected[i].category+'">'+rowselected[i].cat_name+'</option></select></div>  <div class="col-md-3 form-group"><select id="desc'+w+'" class="form-control desc" row_count="'+w+'" style="color:black"><option selected disabled>select item description</option><option value="'+rowselected[i].items_id+'">'+rowselected[i].item_name+'</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control inputqty" row_count="'+w+'" id="inputqty'+w+'" maxlength="2" min="0" max="'+maxqty+'" value="0" style="color:black" onkeypress="return event.charCode >= 47" onkeyup=imposeMinMax(this) onkeyup="checkserial(this)"></div><div class="col-md-2 form-group"><input type="text" class="form-control uom" row_count="'+w+'" id="uom'+w+'" value="'+rowselected[i].uom+'" style="color:black" autocomplete="off" disabled></div><div class="col-md-2 form-group"><select id="item'+w+'" class="form-control item" row_count="'+w+'" style="color:black"><option selected value="'+rowselected[i].items_id+'">'+rowselected[i].items_id+'</option></select></div></div>'
            $('#reqfield').append(additem);
            $('#item'+w).val(rowselected[i].items_id);
            $('#desc'+w).val(rowselected[i].items_id);
            console.log(rowselected[i].cat_name);
            $('#item'+w).hide();

        }
    }
    $('#loading').hide();
    $('#sendModal').modal('show');

});
function checkserial(ex) {
    //var mycount = document.getElementById(ex.id).row_count.value;
    var myval = ex.id;
    var slicena = myval.slice(6)
    console.log(slicena);
    if ($('#serial'+slicena).val().toLowerCase() == 'n/a') {
        $.ajax({
            url: 'checkserial',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'get',
            async: false,
            data: {
                item: $('#item'+slicena).val(),
                type: 'na'
            },
            success: function (data) {
                if (data != "allowed") {
                    $('#serial'+slicena).val('');
                    alert('This item requires a valid serial number. If the item does not contain a serial number please contact the main office to generate a new one.');
                }
            },
            error: function (data) {
                alert(data.responseText);
                return false;
            }
        });
    }
    if ($('#serial'+slicena).val().toLowerCase() != 'n/a') {
        if ($('#serial'+slicena).val().toLowerCase() != '') {
            $.ajax({
                url: 'checkserial',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                },
                dataType: 'json',
                type: 'get',
                async: false,
                data: {
                    serial: $('#serial'+slicena).val(),
                    type: 'check'
                },
                success: function (data) {
                    if (data != "allowed") {
                        $('#serial'+slicena).val('');
                        alert('The serial number you selected is already existing('+data.retno+'). Please contact the administrator.');
                    }
                },
                error: function (data) {
                    alert(data.responseText);
                    return false;
                }
            });
        }
    }
}