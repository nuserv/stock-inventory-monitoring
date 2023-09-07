var sunit;
var service = '';
var serial = '';
var desc = '';
var status = '';
var trdata;
var repdesc;
$(document).ready(function()
{
    sunit = $('table.sUnitTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "emptyTable": "No data found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> '
        },
        processing: true,
        serverSide: true,
        ajax: 'sUnit',
        
        columns: [
            { data: 'date', name:'date'},
            { data: 'client', name:'client'},
            { data: 'category', name:'category'},
            { data: 'description', name:'description'},
            { data: 'serial', name:'serial'},
            { data: 'status', name:'status'},
            { data: 'serviceby', name:'serviceby'}
        ]
    });
    $('#in_sub_Btn').prop('disabled', true);
    $('#repserial').prop('disabled', true);
});

$(document).on('click', '#out_Btn', function(){
    $('#service-unitModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '#in_Btn', function(){
    $('#pull_unitModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '.in-close', function(){
    $('#service-unitModal').modal('toggle');
    $('#loading').show();
    location.reload();
});

$(document).on('click', '.close', function(){
    location.reload();
});

$(document).on("click", "#sUnitTable tr", function () {
    trdata = sunit.row(this).data();
    if (trdata.user_id != $('#userid').val()) {
        if ($('#userlevel').val() != 'Head') {
            return false;
        }
    }
    console.log(trdata.status);
    if (trdata.status == 'PULL OUT') {
        Swal.fire({
            title: 'Choose an option',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Repaired',
            cancelButtonText: 'Replacement'
        }).then((result) => {
            if (result.isConfirmed) {
                handleRepaired();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // Fetch the select options via AJAX
                $.ajax({
                    url: '/get_serial', // Modify the URL to match your route
                    type: 'GET',
                    dataType: 'json',
                    data:{
                        items_id: trdata.items_id
                    },
                    success: function (serialOptions) {
                        console.log(serialOptions);
                        // When the options are fetched successfully, show the Swal modal with the options
                        Swal.fire({
                            title: 'Choose a Serial Number',
                            input: 'select',
                            inputOptions: serialOptions,
                            showCancelButton: true,
                            confirmButtonText: 'Submit',
                            cancelButtonText: 'Cancel',
                            inputValidator: (value) => {
                                if (!value) {
                                    return 'Serial number is required!';
                                }
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const selectedSerialNumber = result.value;
                                handleReplacement(selectedSerialNumber);
                            }
                        });
                    },
                    error: function () {
                        // Handle the error case
                        Swal.fire('Error fetching serial options', '', 'error');
                    }
                });
            }
        });
    }
    else{
        $('#service-inModal').modal({backdrop: 'static', keyboard: false});
        $('#inclient').val(trdata.client_name.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
        $('#incustomer').val(trdata.customer_name.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
        $('#outitem').val(trdata.description.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
        var itemop;
        $.ajax({
            type:'get',
            url:'itemcode',
            data:{'id':trdata.category_id},
            success:function(data)
            {
                var itemcode = $.map(data, function(value, index) {
                    return [value];
                });
                itemop+='<option selected disabled>select item description</option>';
                itemcode.forEach(value => {
                    itemop+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>';
                });
                $("#repdesc").find('option').remove().end().append(itemop);
            },
        });
        $('#indesc').val(trdata.description);
        $('#indescid').val(trdata.id);
        $('#inserial').hide();
        $('#inserial').val(trdata.serial);
        $('#inserial').prop('disabled', true);
        $('#repserial').prop('disabled', true);
        $('#repserial').show();
    }
});

$(document).on('change', '#intype', function(){
    if ($(this).val() == 'service-unit') {
        $('#indesc').show();
        $('#repdesc').hide();
        $('#inserial').prop('disabled', false);
        $('#repserial').prop('disabled', true);
        $('#repserial').hide();
        $('#inserial').show();
        $('#repstatus').hide();
        $('#instatus').show();
        status = '';
        desc = '';
        $('#instatus').prop('disabled', false);
        $('#in_sub_Btn').prop('disabled', false);

    }else if ($(this).val() == 'replacement') {
        $('#indesc').hide();
        $('#repdesc').val('');
        $('#repdesc').show();
        $('#repdesc').prop('disabled', false);
        // $('#repdesc').prop('readonly', true);
        $('#repdesc').click();
        $('#inserial').prop('disabled', true);
        $('#repserial').prop('disabled', true);
        $('#repserial').show();
        $('#repserial').val('');
        $('#inserial').hide();
        $('#instatus').prop('disabled', true);
        $('#instatus').hide();
        $('#instatus').val('select item status');
        $('#repstatus').show();
        $('#in_sub_Btn').prop('disabled', true);
        status = '';
        desc = '';
    }
});
$(document).on('keyup', '#repserial', function(){
    $(this).val($(this).val().toUpperCase());
    if ($(this).val() && $(this).val().length >= 3) {
        if ($(this).val().toLowerCase().includes('n/a') || $(this).val().toLowerCase() ==  "n/a" || $(this).val().toLowerCase() ==  "faded" || $(this).val().toLowerCase() ==  "none") {
            $.ajax({
                url: 'checkserials',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                },
                dataType: 'json',
                type: 'get',
                async: false,
                data: {
                    item: $('#repdesc').val(),
                    type: 'na'
                },
                success: function (data) {
                    if (data != "allowed") {
                        $('#in_sub_Btn').prop('disabled', true);
                        $('#repserial').val('');
                        alert('This item requires a valid serial number. If the item does not contain a serial number please contact the main office to generate a new one.');
                    }else{
                        $('#in_sub_Btn').prop('disabled', false);
                    }
                },
                error: function (data) {
                    alert(data.responseText);
                    return false;
                }
            });
        }else if($(this).val().match(".*\\d.*")){
            $.ajax({
                url: 'checkserial',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                },
                dataType: 'json',
                type: 'get',
                async: false,
                data: {
                    serial: $('#repserial').val(),
                    type: 'check'
                },
                success: function (data) {
                    if (data != "allowed") {
                        $('#in_sub_Btn').prop('disabled', true);
                        $('#repserial').val('');
                        alert('The serial number you entered is already existing. Please check the serial number again.');
                    }else{
                        $('#in_sub_Btn').prop('disabled', false);
                    }
                },
                error: function (data) {
                    alert(data.responseText);
                    return false;
                }
            });
        }
    }else{
        $('#in_sub_Btn').prop('disabled', true);
    }
    $(this).val($(this).val().replace('-',''));
});

$(document).on('change', '#instatus', function(){
    if ($(this).val() == 'good') {
        status = 'in';
    }else if ($(this).val() == 'defective') {
        status = $(this).val();
    }
});

$(document).on('click', '#repdesc', function(){
    $('#in_sub_Btn').prop('disabled', true);
    desc = '';
    var query = trdata.category_id;
    var ul = '<ul class="dropdown-menu" style="display:block; position:relative;overflow: scroll;height: 13em;z-index: 200;">';
    $.ajax({
        url:"itemcodes",
        type:"get",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        data:{
            id:query,
        },
        success:function(data){
            var datas = $.map(data, function(value, index) {
                return [value];
            });
            datas.forEach(value => {
                ul+='<li style="color:black" id="lirepdesc">'+value.item.toUpperCase()+'</li>';
            });
            $('#repdesclist').fadeIn();  
            $('#repdesclist').html(ul);
        }
    });
});

$(document).on('keyup', '#repdesc', function(){ 
    $('#in_sub_Btn').prop('disabled', true);
    desc = '';
    var query = trdata.category_id;
    var ul = '<ul class="dropdown-menu" style="display:block; position:relative;overflow: scroll;height: 13em;z-index: 200;">';
    $.ajax({
        url:"itemcodes",
        type:"get",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        data:{
            id:query,
            item:$(this).val()
        },
        success:function(data){
            var datas = $.map(data, function(value, index) {
                return [value];
            });
            datas.forEach(value => {
                ul+='<li style="color:black" id="lirepdesc">'+value.item.toUpperCase()+'</li>';
            });
            $('#repdesclist').fadeIn();  
            $('#repdesclist').html(ul);
        }
    });
});

$(document).on('click', 'li', function(){  
    var select = $(this).text();
    var id = $(this).attr('id');
    if (id == 'lirepdesc') {
        $('#repdesc').val($(this).text());
        $('#repdesclist').fadeOut();  
        desc = $(this).text();
        console.log(desc);
        $('#repserial').prop('disabled', false);
        $('#in_sub_Btn').prop('disabled', true);
    }
});

$(document).on('click', '.in_sub_Btn', function(e){
    if ($('#intype').val()) {
        if ($('#intype').val() == 'service-unit') {
            if (status != '') {
                if(confirm('Please make sure you input the correct item and serial number.\nClick CANCEL to review your entry.\nClick OK if you are sure that your entry is correct to SUBMIT.')) {
                    e.preventDefault();
                    $('#service-inModal').toggle();
                    $('#loading').show();
                    console.log($('#indescid').val());
                    //return false;
                    $.ajax({
                        url: 'service-in',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                        },
                        dataType: 'json',
                        type: 'PUT',
                        data: {
                            stat: 'sunit',
                            id: $('#indescid').val(),
                            serial: $('#inserial').val(),
                            status: status,
                            custid: trdata.customer_branches_id,
                            remarks: 'service'
                        },
                        success:function(data)
                        {
                            console.log(data);
                            if (data == "bawal") {
                                alert('You are trying to SERVICE-IN in less than 10 minutes from the time you SERVICE-OUT. Please SERVICE-OUT before you leave the office. \nKindly try again later.');
                                location.reload();
                            }else{
                                location.reload();
                            }
                        },
                        error: function (data) {
                            alert(data.responseText);
                        }
                    });
                }
            }
        }else if ($('#intype').val() == 'replacement') {
            console.log(desc);
            if (desc != '' && $('#repserial').val() != "") {
                if(confirm('Please make sure you input the correct item and serial number. Click CANCEL to review your entry. Click OK if you are sure that your entry is correct to SUBMIT.')) {
                    e.preventDefault();
                    $('#service-inModal').toggle();
                    $('#loading').show();
                    $.ajax({
                        url: 'rep-update',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                        },
                        dataType: 'json',
                        type: 'PUT',
                        async: false,
                        data: {
                            stat: 'replacement',
                            id: $('#indescid').val(),
                            ids: desc,
                            serial: $('#repserial').val(),
                            status: 'defective',
                            custid: trdata.customer_branches_id,
                            remarks: 'service',

                        },
                        success:function(data)
                        {
                            // console.log(data);
                            if (data == "bawal") {
                                alert('You are trying to SERVICE-IN in less than 10 minutes from the time you SERVICE-OUT. Please SERVICE-OUT before you leave the office.\nKindly try again later.');
                                location.reload();
                            }else if (data == "error") {
                                alert('An unexpected error occurred. Please contact your system administrator.');
                            }else{
                                // console.log(data);
                                location.reload();
                            }
                        },
                        error: function (data) {
                            alert(data.responseText);
                        }
                    });
                }
            }
        }
    }
});

$(document).on('click', '.service-unit', function(){
    $('#outOptionModal .out-close').click();
    $('.def').show();
    $('.gud').show();
    $('#inOptionModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '.replacement', function () {
    $('#outOptionModal .out-close').click();
    $('#inOptionModal').modal({backdrop: 'static', keyboard: false});
});