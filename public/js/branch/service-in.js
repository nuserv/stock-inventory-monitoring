$(document).on('click', '.good', function(){
    $("#inOptionModal .close").click();
    $('#status').val('in');
    $('#goodModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '.defective', function(){
    $("#inOptionModal .close").click();
    $('#status').val('defective');
    $('#goodModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '.pull-out', function(){
    $("#inOptionModal .close").click();
    $('#pulloutModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('change', '.goodcategory', function(){
    var catOp = " ";
    var count = $(this).attr('row_count');
    var customerid = $('#goodcustomer'+ count).val();
    var categoryid = $(this).val();
    $.ajax({
        type:'get',
        url:'description',
        async: false,
        data:{
            'customerid':customerid,
            'categoryid':categoryid,
        },
        success:function(data)
        {
            var category = $.map(data, function(value, index) {
                return [value];
            });
            catOp+='<option selected disabled>select item description</option>';
            category.forEach(value => {
                catOp+='<option value="'+value.items_id+'">'+value.item.toUpperCase()+'</option>';
            });
            $("#gooddesc" + count).find('option').remove().end().append(catOp);
        },
    });
});

$(document).on('change', '.goodcustomer', function(){
    var custOp = " ";
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $.ajax({
        type:'get',
        url:'category',
        data:{'id':id},
        async: false,
        success:function(data)
        {
            var category = $.map(data, function(value, index) {
                return [value];
            });
            custOp+='<option selected disabled>select category</option>';
            category.forEach(value => {
                custOp+='<option value="'+value.category_id+'">'+value.category.toUpperCase()+'</option>';
            });
            $("#goodcategory" + count).find('option').remove().end().append(custOp);
        },
    });
});

$(document).on('change', '.gooddesc', function(){
    var serialOp = " ";
    var count = $(this).attr('row_count');
    var customerid = $('#goodcustomer'+ count).val();
    var categoryid = $('#goodcategory'+ count).val();
    var descid = $(this).val();
    $.ajax({
        type:'get',
        url:'serial',
        async: false,
        data:{
            'customerid':customerid,
            'categoryid':categoryid,
            'descid':descid
        },
        success:function(data)
        {
            var itemcode = $.map(data, function(value, index) {
                return [value];
            });
            serialOp+='<option selected disabled>select serial</option>';
            itemcode.forEach(value => {
                serialOp+='<option value="'+value.id+'">'+value.serial.toUpperCase()+'</option>';
            });
            $("#goodserial" + count).find('option').remove().end().append(serialOp);
        },
    });
});

$(document).on('click', '.pout_add_item', function(){
    var rowcount = $(this).attr('btn_id');
    if ($(this).val() == 'Add Item') {
        if($('#poutcategory'+ rowcount).val() && $('#poutdesc'+ rowcount).val() && $('#poutserial'+ rowcount).val()) {
            y++;
            var additem = '<div class="row no-margin" id="poutrow'+y+'"><div class="col-md-2 form-group"><select id="poutcategory'+y+'" class="form-control poutcategory" row_count="'+y+'" style="color: black;"><option selected disabled>select category</option></select></div><div class="col-md-3 form-group"><select id="poutdesc'+y+'" class="form-control poutdesc" row_count="'+y+'" style="color: black;"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="text" id="poutserial'+y+'" class="form-control poutserial" row_count="'+y+'" style="color: black;" value="N/A"></div><div class="col-md-1 form-group"><input type="button" class="pout_add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            $(this).val('Remove');
            $('#poutcategory'+ rowcount).prop('disabled', true);
            $('#poutdesc'+ rowcount).prop('disabled', true);
            $('#poutserial'+ rowcount).prop('disabled', true);
            if (r < 20 ) {
                $('#poutfield').append(additem);
                $('#poutcategory'+ rowcount).find('option').clone().appendTo('#poutcategory'+y);
                r++;
            }
        }
    }else{
        if (r == 20) {
            y++;
            var additem = '<div class="row no-margin" id="poutrow'+y+'"><div class="col-md-2 form-group"><select id="poutcategory'+y+'" class="form-control poutcategory" row_count="'+y+'" style="color: black;"><option selected disabled>select category</option></select></div><div class="col-md-3 form-group"><select id="poutdesc'+y+'" class="form-control poutdesc" row_count="'+y+'" style="color: black;"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><input type="text" id="poutserial'+y+'" class="form-control poutserial" row_count="'+y+'" style="color: black;" value="N/A"></div><div class="col-md-1 form-group"><input type="button" class="pout_add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            $('#poutfield').append(additem);
            $('#poutcategory'+ rowcount).find('option').clone().appendTo('#poutcategory'+y);
            r++;
        }
        $('#poutcategory'+rowcount).val('select category');
        $('#poutdesc'+rowcount).val('select item description');
        $('#poutserial'+rowcount).val('input serial');
        $('#poutcategory'+rowcount).prop('disabled', false);
        $('#poutdesc'+rowcount).prop('disabled', false);
        $('#poutserial'+rowcount).prop('disabled', false);
        $('#poutrow'+rowcount).hide();
        $(this).val('Add Item');
        r--;
    }
});

$(document).on('click', '.pout_sub_Btn', function(){
    var cat = "";
    var serial = "";
    var item = "";
    var check = 1;
    if ($('#pcustomer-id').val() != "") {
        for(var q=1;q<=y;q++){
            if ($('#poutrow'+q).is(":visible")) {
                if ($('.pout_add_item[btn_id=\''+q+'\']').val() == 'Remove') {
                    $('.pout_sub_Btn').prop('disabled', true)
                    cat = $('#poutcategory'+q).val();
                    item = $('#poutdesc'+q).val();
                    serial = $('#poutserial'+q).val();
                    client = $('#pclient-id').val();
                    customer = $('#pcustomer-id').val();
                    $.ajax({
                        url: 'pull-out',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        type: 'POST',
                        async: false,
                        data: {
                            item: item,
                            serial: serial,
                            cat : cat,
                            customer: customer,
                            client: client
                        },
                        success:function()
                        {
                            check++;
                        },
                        error: function (data) {
                            alert(data.responseText);
                        }
                    });
                }
            }
        }
    }else{
        alert("Invalid Customer Name!");
        return false;
    }
    if (check > 1) {
        window.location.href = 'stocks';
    }
});

$(document).on('click', '.good_add_item', function(){
    var rowcount = $(this).attr('btn_id');
    if ($(this).val() == 'Add Item') {
        if($('#goodserial'+ rowcount).val()){
            y++;
            var additem = '<div class="row no-margin" id="goodrow'+y+'"><div class="col-md-3 form-group"><select id="goodcustomer'+y+'" class="form-control goodcustomer" row_count="'+y+'"><option selected disabled>select customer</option></select></div><div class="col-md-2 form-group"><select id="goodcategory'+y+'" class="form-control goodcategory" row_count="'+y+'"><option selected disabled>select category</option></select></div><div class="col-md-3 form-group"><select id="gooddesc'+y+'" class="form-control gooddesc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><select id="goodserial'+y+'" class="form-control goodserial" row_count="'+y+'"><option selected disabled>select serial</option></select></div><div class="col-md-1 form-group"><input type="button" class="good_add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>'
            $(this).val('Remove');
            $('#goodcustomer'+ rowcount).prop('disabled', true);
            $('#goodcategory'+ rowcount).prop('disabled', true);
            $('#gooddesc'+ rowcount).prop('disabled', true);
            $('#goodserial'+ rowcount).prop('disabled', true);
        }
        if (b < 10 ) {
            $('#service-unitfield').append(additem);
            $('#goodcustomer'+ rowcount).find('option').clone().appendTo('#goodcustomer'+y);
            b++;
        }
    }else{
        if (b == 10) {
            y++;
            var additem = '<div class="row no-margin" id="goodrow'+y+'"><div class="col-md-3 form-group"><select id="goodcustomer'+y+'" class="form-control goodcustomer" row_count="'+y+'"><option selected disabled>select customer</option></select></div><div class="col-md-2 form-group"><select id="goodcategory'+y+'" class="form-control goodcategory" row_count="'+y+'"><option selected disabled>select category</option></select></div><div class="col-md-3 form-group"><select id="gooddesc'+y+'" class="form-control gooddesc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><select id="goodserial'+y+'" class="form-control goodserial" row_count="'+y+'"><option selected disabled>select serial</option></select></div><div class="col-md-1 form-group"><input type="button" class="good_add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>'
            $('#service-unitfield').append(additem);
            $('#goodcustomer'+ rowcount).find('option').clone().appendTo('#goodcustomer'+y);
            b++;
        }
        $('#goodrow'+rowcount).hide();
        $(this).val('Add');
        b--;
    }
});

$(document).on('click', '#good_sub_Btn', function(){
    var serial = "";
    var check = 1;
    for(var q=1;q<=y;q++){
        if ($('#goodrow'+q).is(":visible")) {
            if ($('.good_add_item[btn_id=\''+q+'\']').val() == 'Remove') {
                serial = $('#goodserial'+q).val();
                cat = $('#goodcategory'+q).val();
                status = $('#status').val();
                $.ajax({
                    url: 'service-in',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'PUT',
                    async: false,
                    data: {
                        serial : serial,
                        cat: cat,
                        status : status
                    },
                    success:function()
                    {
                        check++;
                    },
                    error: function (data) {
                        alert(data.responseText);
                    }
                });
            }
        }
    }
    if (check > 1) {
       window.location.href = 'stocks';
    }
});

$(document).on('keyup', '#pclient', function(){
    var id = $(this).val();
    var op = " ";
    $('#pcustomer').val('');
    $("#pcustomer-name").find('option').remove();
    $.ajax({
        type:'get',
        url:'client-autocomplete',
        async: false,
        data:{
            'id':id
        },
        success:function(data)
        {
            var customer = $.map(data, function(value, index) {
                return [value];
            });
            op+=' ';
            customer.forEach(value => {
                op+='<option data-value="'+value.id+'" value="'+value.customer.toUpperCase()+'"></option>';
            });
            $("#pclient-name").find('option').remove().end().append(op);
            $('#pclient-id').val($('#pclient-name [value="'+$('#pclient').val()+'"]').data('value'));
        },
    });
});

$(document).on('keyup', '#pcustomer', function(){
    var id = $(this).val();
    var op = " ";
    if ($('#pclient-id').val()) {
        var client = $('#pclient-id').val();
    }else{
        alert("Incomplete Client Name!");
        return false;
    }
    $.ajax({
        type:'get',
        url:'customer-autocomplete',
        data:{
            'id':id,
            'client':client
        },
        success:function(data)
        {
            var itemcode = $.map(data, function(value, index) {
                return [value];
            });
            op+=' ';
            itemcode.forEach(value => {
                op+='<option data-value="'+value.id+'" value="'+value.customer_branch.toUpperCase()+'"></option>';
            });
            $("#pcustomer-name").find('option').remove().end().append(op);
            $('#pcustomer-id').val($('#pcustomer-name [value="'+$('#pcustomer').val()+'"]').data('value'));
        },
    });
});

$(document).on('change', '.poutdesc', function(){
    var count = $(this).attr('row_count');
    $('#poutserial'+count).val('N/A');
});

$(document).on('change', '.poutcategory', function(){
    var descOp = " ";
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $.ajax({
        type:'get',
        url:'itemcode',
        data:{'id':id},
        async: false,
        success:function(data)
        {
            var itemcode = $.map(data, function(value, index) {
                return [value];
            });
            descOp+='<option selected disabled>select item description</option>';
            itemcode.forEach(value => {
                descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>';
            });
            $("#poutdesc" + count).find('option').remove().end().append(descOp);
        },
    });
});