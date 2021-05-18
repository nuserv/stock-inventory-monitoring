var sunit;
var service = '';
var serial = '';
var desc = '';
var status = '';
var pmsid;
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
        ajax: 'pmsUnit',
        "columnDefs": [
        {
            "targets": [1],
            "render": function (data, type, row) {
                return data.split(",").join("<br/>");
            }
        }],
        columns: [
            { data: 'date', name:'date', "width": "14%"},
            { data: 'client', name:'client', "width": "30%"},
            { data: 'category', name:'category'},
            { data: 'description', name:'description'},
            { data: 'serial', name:'serial'},
            { data: 'serviceby', name:'serviceby'}
        ],
        select: {
            style: 'single'
        }
    });
});

$(document).on('click', '#out_Btn', function(){
    $('#service-unitModal').modal({backdrop: 'static', keyboard: false});
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
    var trdata = sunit.row(this).data();
    pmsid = trdata.id;
    $('#service-inModal').modal({backdrop: 'static', keyboard: false});
    $('#inclient').val(trdata.client_name);
    $('#incustomer').val(trdata.customer_name);
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
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
    $.ajax({
        type:'get',
        url:'pmcustomer-autocomplete',
        data:{
            customer_ids: trdata.customer_ids
        },
        success:function(data)
        {
            var customerdata = $.map(data, function(value, index) {
                return [value];
            });
            var op = ' ';
            op+='<option selected disabled>select client branch</option>';
            customerdata.forEach(value => {
                op+='<option value="'+value.id+'">'+value.customer.toUpperCase()+' - '+value.customer_branch.toUpperCase()+'</option>';
                //op+='<option data-value="'+value.id+'" value="'+value.customer+' - '+value.customer_branch+'"></option>';
            });
            $("#incustomer").find('option').remove().end().append(op);
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
    $('#indesc').val(trdata.description);
    $('#indescid').val(trdata.id);
    $('#inserial').hide();
    $('#inserial').val(trdata.serial);
    $('#inserial').prop('disabled', true);
    $('#repserial').prop('disabled', true);
    $('#repserial').show();
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

    }else if ($(this).val() == 'replacement') {
        $('#indesc').hide();
        $('#repdesc').val('select item description');
        $('#repdesc').show();
        $('#repdesc').prop('disabled', false);
        $('#inserial').prop('disabled', true);
        $('#repserial').prop('disabled', false);
        $('#repserial').show();
        $('#repserial').val('');
        $('#inserial').hide();
        $('#instatus').prop('disabled', true);
        $('#instatus').hide();
        $('#instatus').val('select item status');
        $('#repstatus').show();
        status = '';
        desc = '';
    }
});

$(document).on('change', '#instatus', function(){
    if ($(this).val() == 'good') {
        status = 'in';
    }else if ($(this).val() == 'defective') {
        status = $(this).val();
    }
});

$(document).on('change', '#repdesc', function(){
    desc = $(this).val();
});

$(document).on('click', '.in_sub_Btn', function(){
    if ($("#incustomer").val()) {
        if ($('#intype').val()) {
            if ($('#intype').val() == 'service-unit') {
                if (status != '') {
                    $('#service-inModal').toggle();
                    $('#loading').show();
                    $.ajax({
                        url: 'pmservice-in',
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
                            customerid: $("#incustomer").val(),
                            pmid: pmsid,
                            remarks: 'pm'
                        },
                        success:function()
                        {
                            location.reload();
                        },
                        error: function (data) {
                            if(data.status == 401) {
                                window.location.href = '/login';
                            }
                            alert(data.responseText);
                        }
                    });
                }
            }else if ($('#intype').val() == 'replacement') {
                if (desc != '' && $('#repserial').val() != "") {
                    $('#service-inModal').toggle();
                    $('#loading').show();
                    $.ajax({
                        url: 'PMrep-update',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                        },
                        dataType: 'json',
                        type: 'PUT',
                        async: false,
                        data: {
                            stat: 'replace',
                            id: $('#indescid').val(),
                            ids: $('#repdesc').val(),
                            serial: $('#repserial').val(),
                            status: 'defective',
                            customerid: $("#incustomer").val(),
                            pmid: pmsid,
                            remarks: 'pm'
                        },
                        success:function()
                        {
                            location.reload();
                        },
                        error: function (data) {
                            if(data.status == 401) {
                                window.location.href = '/login';
                            }
                            alert(data.responseText);
                        }
                    });
                }
            }
        }
    }else{
        alert('Please select Client branch!');
    }
});
$(document).on('keyup', '#repserial', function(){
    if ($(this).val() && $(this).val().length >= 3) {
        if ($(this).val().toLowerCase().includes('n/a') ==  "n/a" || $(this).val().toLowerCase() ==  "faded" || $(this).val().toLowerCase() ==  "none") {
            $.ajax({
                url: 'checkserial',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                },
                dataType: 'json',
                type: 'get',
                async: false,
                data: {
                    item: $('#repdesc').val(),
                },
                success: function (data) {
                    if (data != "allowed") {
                        $('#in_sub_Btn').prop('disabled', true);
                    }else{
                        $('#in_sub_Btn').prop('disabled', false);
                    }
                },
                error: function (data) {
                    alert(data.responseText);
                    return false;
                }
            });
        }else{
            $('#in_sub_Btn').prop('disabled', false);
        }
    }else{
        $('#in_sub_Btn').prop('disabled', true);
    }
    $(this).val($(this).val().replace('-',''));
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
