$(document).on('click', '#addBtn', function(){
    $('#addModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '.sub_Btn', function(){
    $('#loading').show();
    if ($('#branchlist').is(":visible")) {
        alert('Invalid client name!');
        $('#loading').hide();
        return false;
    }
    if ($('#branch').val() == "") {
        alert('Invalid branch name!');
        $('#loading').hide();
        return false;
    }
    if ($('#drno').val() == "") {
        alert('Invalid DR reference no.!');
        $('#loading').hide();
        return false;
    }
    if ($('#datepullout').val() == "") {
        alert('Invalid pullout date!');
        $('#loading').hide();
        return false;
    }
    var cat = "";
    var item = "";
    var check = 1;
    for(var q=1;q<=y;q++){
        if ($('#row'+q).is(":visible")) {
            if ($('.add_item[btn_id=\''+q+'\']').val() == 'Remove') {
                check++;
                $('.sub_Btn').prop('disabled', true)
                cat = $('#category'+q).val();
                item = $('#desc'+q).val();
                qty = $('#qty'+q).val();
                drno = $('#drno').val();
                cname = $('#incustomer').val();
                bid = $('#branch').val();
                $.ajax({
                    url: 'conversion',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'PUT',
                    data: {
                        category: cat,
                        qty: qty,
                        item: item,
                        details: 'items',
                        drno: drno,
                        cname: cname,
                        bid: bid,
                        pulldate: $('#datepullout').val()
                    },
                    error: function (data) {
                        alert(data.responseText);
                    }
                });
            }
        }
    }
    
    for(var q=1;q<=posy;q++){
        if ($('#posrow'+q).is(":visible")) {
            if ($('.addpos[pos_count=\''+q+'\']').val() == 'Remove') {
                check++;
                $('.sub_Btn').prop('disabled', true)
                pos = $('#possel'+q).val();
                serial = $('#posserial'+q).val();
                drno = $('#drno').val();
                cname = $('#incustomer').val();
                bid = $('#branch').val();
                $.ajax({
                    url: 'conversion',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'PUT',
                    data: {
                        pos: pos,
                        serial: serial,
                        details: 'POS',
                        drno: drno,
                        cname: cname,
                        bid: bid,
                        pulldate: $('#datepullout').val()
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
        if ($('#qty'+ rowcount).val() > 0) {
            if($('#category'+ rowcount).val() && $('#desc'+ rowcount).val() && $('#qty'+ rowcount).val()) {
                y++;
                var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-3 form-group"><select id="category'+y+'" style="color: black" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-4 form-group"><select id="desc'+y+'" style="color: black" class="form-control desc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="number" min="0" id="qty'+y+'" class="form-control qty" row_count="'+y+'" placeholder="qty" style="color: black" ></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
                $('.add_item[btn_id=\''+rowcount+'\']').val('Remove');
                $('#category'+ rowcount).prop('disabled', true);
                $('#desc'+ rowcount).prop('disabled', true);
                $('#qty'+ rowcount).prop('disabled', true);
                if (r < 30 ) {
                    $('#reqfield').append(additem);
                    $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
                    $('#itemdiv'+ y).hide();
                    r++;
                }
            }
            if (r > 1) {
                $('#sub_Btn').prop('disabled', false);
            }
        }
    }else{
        if (r == 30) {
            y++;
            var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-3 form-group"><select id="category'+y+'" style="color: black" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-4 form-group"><select id="desc'+y+'" style="color: black" class="form-control desc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="number" min="0" id="qty'+y+'" class="form-control qty" row_count="'+y+'" placeholder="qty" style="color: black" ></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            // var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" style="color: black" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" style="color: black" class="form-control desc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="text" id="serial'+y+'" class="form-control serial" row_count="'+y+'" placeholder="serial number" style="color: black" onkeyup="checkserial(this)" disabled></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            $('#reqfield').append(additem);
            $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
            $('#itemdiv'+ y).hide();
            r++;
        }
        $('#category'+rowcount).val('select category');
        $('#desc'+rowcount).val('select item description');
        $('#category'+rowcount).prop('disabled', false);
        $('#desc'+rowcount).prop('disabled', false);
        $('#qty'+rowcount).prop('disabled', false);
        $('#row'+rowcount).hide();
        $(this).val('Add Item');
        r--;
        if (r > 1) {
            $('#sub_Btn').prop('disabled', false);
        }
    }
    
});

$(document).on('click', '.addpos', function(){
    var rowcount = $(this).attr('pos_count');
    if ($(this).val() == 'Add POS') {
        if ($('#posserial'+ rowcount).val().toLowerCase() != 'n/a') {
            if($('#posserial'+ rowcount).val()) {
                posy++;
                var additem = '<div class="row no-margin" id="posrow'+posy+'"><div class="col-md-3 form-group"><select style="color: black" class="form-control form-control-sm " id="possel'+posy+'"><option selected disabled>Select POS Model</option><option value="4800-722">4800-722</option><option value="4800-723">4800-723</option><option value="4800-743">4800-743</option><option value="4900-745">4900-745</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control form-control-sm serial" id="posserial'+posy+'" placeholder="Serial number" style="color: black" ></div><div class="col-md-1 form-group"><input type="button" class="btn btn-xs btn-primary form-control-sm addpos" pos_count="'+posy+'" value="Add POS"></div></div>';
                $('.addpos[pos_count=\''+rowcount+'\']').val('Remove');
                $('#possel'+ rowcount).prop('disabled', true);
                $('#posserial'+ rowcount).prop('disabled', true);
                if (posr < 12 ) {
                    $('#posreqfield').append(additem);
                    posr++;
                }
                if (posr > 1) {
                    $('#sub_Btn').prop('disabled', false);
                }
            }
        }
    }else{
        if (posr == 12) {
            posy++;
            var additem = '<div class="row no-margin" id="posrow'+posy+'"><div class="col-md-3 form-group"><select style="color: black" class="form-control form-control-sm " id="possel'+posy+'"><option selected disabled>Select POS Model</option><option value="4800-722">4800-722</option><option value="4800-723">4800-723</option><option value="4800-743">4800-743</option><option value="4900-745">4900-745</option></select></div><div class="col-md-2 form-group"><input type="text" class="form-control form-control-sm serial" id="posserial'+posy+'" placeholder="Serial number" style="color: black" ></div><div class="col-md-1 form-group"><input type="button" class="btn btn-xs btn-primary form-control-sm addpos" pos_count="'+posy+'" value="Add POS"></div></div>';
            $('#posreqfield').append(additem);
            posr++;
        }
        $('#posrow'+rowcount).hide();
        posr--;
        if (posr > 1) {
            $('#sub_Btn').prop('disabled', false);
        }
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
$(document).on('keyup', '#incustomer', function(){ 
    var withclient = 'yes';
    var clientname = 'MERCURY DRUG';
    var query = $(this).val();
    var ul = '<ul class="dropdown-menu" style="display:block; position:relative;overflow: scroll;height: 13em;z-index: 200;">';
    if(query != ''){
        $.ajax({
            url:"pulloutclient",
            type:"get",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                hint:query
            },
            success:function(data){
                var datas = $.map(data, function(value, index) {
                    return [value];
                });
                datas.forEach(value => {
                    ul+='<li style="color:black" id="licustomer">'+value.customer_branch+'</li>';
                });
                $('#branchlist').fadeIn();  
                $('#branchlist').html(ul);
                // $('#out_sub_Btn').prop('disabled', true);
            }
        });
    }
});

$(document).on('click', 'li', function(){  
    var select = $(this).text();
    var id = $(this).attr('id');
    $('#incustomer').val($(this).text());  
    $('#branchlist').fadeOut();  
});