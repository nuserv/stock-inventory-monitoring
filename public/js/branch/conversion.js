$(document).on('click', '#addBtn', function(){
    $('#addModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '.sub_Btn', function(){
    var cat = "";
    var item = "";
    var check = 1;
    $('#loading').show();
    for(var q=1;q<=y;q++){
        if ($('#row'+q).is(":visible")) {
            if ($('.add_item[btn_id=\''+q+'\']').val() == 'Remove') {
                check++;
                $('.sub_Btn').prop('disabled', true)
                cat = $('#category'+q).val();
                item = $('#desc'+q).val();
                serial = $('#serial'+q).val();
                $.ajax({
                    url: 'conversion',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'PUT',
                    data: {
                        category: cat,
                        serial: serial,
                        item: item
                    },
                    success: function(){
                        location.reload(); 
                    },
                    error: function (data) {
                        alert(data.responseText);
                    }
                });
            }
        }
    }
    if (check > 1) {
        location.reload();
    }
});

$(document).on('click', '.add_item', function(){
    var rowcount = $(this).attr('btn_id');
    if ($(this).val() == 'Add Item') {
        if ($('#serial'+ rowcount).val().toLowerCase() != 'n/a') {
            if ($('#serial'+ rowcount).val().toLowerCase().replace(/-/g, '')) {
                $.ajax({
                    url: 'verifyserial',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'get',
                    async: false,
                    data: {
                        serial: $('#serial'+ rowcount).val().toLowerCase()
                    },
                    success: function (data) {
                        if (data != "allowed") {
                            console.log(data);
                            alert('Serial Number already exist!');
                            $('#serial'+ rowcount).val('');
                            return false;
                        }else{
                            if($('#category'+ rowcount).val() && $('#desc'+ rowcount).val() && $('#serial'+ rowcount).val()) {
                                y++;
                                var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" style="color: black" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" style="color: black" class="form-control desc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="text" id="serial'+y+'" class="form-control serial" row_count="'+y+'" placeholder="serial number" style="color: black" onkeyup="checkserial(this)" disabled></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
                                $('.add_item[btn_id=\''+rowcount+'\']').val('Remove');
                                $('#category'+ rowcount).prop('disabled', true);
                                $('#desc'+ rowcount).prop('disabled', true);
                                $('#serial'+ rowcount).val($('#serial'+ rowcount).val().toLowerCase().replace(/-/g, ''));
                                $('#serial'+ rowcount).prop('disabled', true);
                                if (r < 20 ) {
                                    $('#reqfield').append(additem);
                                    $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
                                    $('#itemdiv'+ y).hide();
                                    r++;
                                }
                            }
                        }
                    },
                    error: function (data) {
                        alert(data.responseText);
                        return false;
                    }
                });
            }
        }else{
            if($('#category'+ rowcount).val() && $('#desc'+ rowcount).val() && $('#serial'+ rowcount).val()) {
                y++;
                var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" style="color: black" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" style="color: black" class="form-control desc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="text" id="serial'+y+'" class="form-control serial" row_count="'+y+'" placeholder="serial number" style="color: black" onkeyup="checkserial(this)" disabled></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
                $('.add_item[btn_id=\''+rowcount+'\']').val('Remove');
                $('#category'+ rowcount).prop('disabled', true);
                $('#desc'+ rowcount).prop('disabled', true);
                $('#serial'+ rowcount).val($('#serial'+ rowcount).val().toLowerCase().replace(/-/g, ''));
                $('#serial'+ rowcount).prop('disabled', true);
                if (r < 20 ) {
                    $('#reqfield').append(additem);
                    $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
                    $('#itemdiv'+ y).hide();
                    r++;
                }
            }
        }
    }else{
        if (r == 20) {
            y++;
            var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" style="color: black" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" style="color: black" class="form-control desc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="text" id="serial'+y+'" class="form-control serial" row_count="'+y+'" placeholder="serial number" style="color: black" onkeyup="checkserial(this)" disabled></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            $('#reqfield').append(additem);
            $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
            $('#itemdiv'+ y).hide();
            r++;
        }
        $('#category'+rowcount).val('select category');
        $('#desc'+rowcount).val('select item description');
        $('#serial'+rowcount).val('select serial');
        $('#category'+rowcount).prop('disabled', false);
        $('#desc'+rowcount).prop('disabled', false);
        $('#serial'+rowcount).prop('disabled', false);
        $('#row'+rowcount).hide();
        $(this).val('Add Item');
        r--;
    }
});

$(document).on('change', '.category', function(){
    var codeOp = " ";
    var descOp = " ";
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $('#serial' + count).prop('disabled', true);
    $.ajax({
        type:'get',
        url:'itemcode',
        data:{'id':id},
        success:function(data)
        {
            var itemcode = $.map(data, function(value, index) {
                return [value];
            });
            codeOp+='<option selected disabled>select item code</option>';
            descOp+='<option selected disabled>select item description</option>';
            itemcode.forEach(value => {
                codeOp+='<option value="'+value.id+'">'+value.id+'</option>';
                descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>';
            });
            $("#desc" + count).find('option').remove().end().append(descOp);
        },
    });
});
$(document).on('change', '.item', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();        
    $('#desc' + count).val(id);
});
$(document).on('change', '.desc', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $('#serial' + count).prop('disabled', false);
    $('#serial' + count).val('');
    $('#item' + count).val(id);
});
$('input[type="text"]').keyup(function() {
    $(this).val().replace('-','');
});
function checkserial(ex) {
    //var mycount = document.getElementById(ex.id).row_count.value;
    var myval = ex.id;
    var slicena = myval.slice(6)
    if ($('#serial'+slicena).val().toLowerCase().includes('n/a')) {
        $.ajax({
            url: 'checkserial',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'get',
            async: false,
            data: {
                item: $('#desc'+slicena).val(),
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
    }else{
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
                console.log(data);
                if (data != "allowed") {
                    $('#serial'+slicena).val('');
                    alert('The serial number you selected is already existing. Please contact the administrator.');
                }
            },
            error: function (data) {
                alert(data.responseText);
                return false;
            }
        });
    }
}