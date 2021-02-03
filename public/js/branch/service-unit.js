var sunit;
var service = '';
var serial = '';
var desc = '';
var status = '';
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
    window.location.href = 'service-unit';
});

$(document).on('click', '.close', function(){
    window.location.href = 'service-unit';
});

$(document).on("click", "#sUnitTable tr", function () {
    var trdata = sunit.row(this).data();
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
            itemop+='<option selected value="select" disabled>select item description</option>';
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
        $('#repdesc').val('select');
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
    if ($('#intype').val()) {
        if ($('#intype').val() == 'service-unit') {
            if (status != '') {
                $('#service-inModal').toggle();
                $('#loading').show();
                $.ajax({
                    url: 'service-in',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'PUT',
                    data: {
                        id: $('#indescid').val(),
                        serial: $('#inserial').val(),
                        status: status
                    },
                    success:function()
                    {
                        window.location.href = 'service-unit';
                    },
                });
            }
        }else if ($('#intype').val() == 'replacement') {
            if (desc != '' && $('#repserial').val() != "") {
                $('#service-inModal').toggle();
                $('#loading').show();
                $.ajax({
                    url: 'rep-update',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'PUT',
                    async: false,
                    data: {
                        id: $('#repdesc').val(),
                        serial: $('#repserial').val(),
                        status: 'defective',
                    },
                    success:function()
                    {
                        window.location.href = 'service-unit';
                    },
                    error: function (data) {
                        alert(data.responseText);
                    }
                });
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