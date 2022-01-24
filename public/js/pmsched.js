var table;
var go ='no';
var branchCode;
$(function() {
    var datesched = $("#datesched").datepicker({
        onSelect: function(dateText, inst) { 
            var ul = '<ul class="dropdown-menu" style="display:block; position:relative;overflow: scroll;height: 13em;z-index: 200;">';
            $.ajax({
                url:"getfsr",
                type:"get",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                },
                data:{
                    date:$('#datesched').val(),
                    branchCode: branchCode,
                    type: $('#option').val()
                },
                success:function(data){
                    console.log(data);
                    var datas = $.map(data, function(value, index) {
                        return [value];
                    });

                    datas.forEach(value => {
                        ul+='<li style="color:black">'+value.fsr_num+'</li>';
                    });
                    if (data.length > 0) {
                        $('#fsrno').val(data[0].fsr_num);
                        $('#saveBtn').val('SAVE');
                    }else{
                        $('#fsrno').val('');
                        swal('','You need to upload first the FSR in the FSR System before you could post here. Make sure you choose the correct branch and date of the PM for the correct FSR Number to appear.', 'error');
                    }
                }
            });
        },
        format: 'YYYY-MM-DD',
        minViewMode: 1,
        autoclose: true,
        maxDate: 0,
        minDate: -75,
    });

    var convdatesched = $("#convdatesched").datepicker({
        onSelect: function(){
            $('#datesched').hide();
            $('#fsrno').hide();
            $('.labl').hide();
            $('#saveBtn').val('CONVERSION');
        },
        format: 'YYYY-MM-DD',
        minViewMode: 1,
        autoclose: true,
        maxDate: 0,
        minDate: -75
    });
    
    $(window).resize(function() {
        datesched.datepicker('hide');
        $('.datesched').blur();
        convdatesched.datepicker('hide');
        $('.convdatesched').blur();    
    });
});
$(document).ready(function()
{
    $('#pmTable thead tr:eq(0) th').each( function () {
        var title = $(this).text().trim();
        $(this).html( '<input type="text" style="width:100%" placeholder="Search '+title+'" class="column_search" />' );
    });
    $('.dtsched').hide();
    $('.fsr').hide();
    table =
    $('table.pmTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "emptyTable": 'No data found.',
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span>'
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: 'scheduled',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'client',render: function ( data, type, row ) {
                if (row.Conversion != null) {
                    return data+' (Conversion Date: '+row.Conversion+')';
                }else{
                    return data;
                }
            }},
            { data: 'lastpm', name:'lastpm'}
        ]
    });

    $('#pmTable thead').on( 'keyup', ".column_search",function () {
        table
            .column( $(this).parent().index() )
            .search( this.value )
            .draw();
    });
});

$("#datesched").on("click", function() {
    var offsetModal = $('#schedModal').offset().top;
    var offsetInput = $(this).offset().top;
    var inputHeight = $(this).height();
    var customPadding = 17; //custom modal padding (bootstrap modal)! 
    var topDatepicker = (offsetInput + inputHeight + customPadding) - offsetModal;
    console.log(topDatepicker);
    $("#ui-datepicker-div").css({top: topDatepicker});
});

$(document).on("click", "#convdatesched", function() {
// $(".convdatesched").on("click", function() {
    var offsetModal = $('#schedModal').offset().top;
    var offsetInput = $(this).offset().top;
    var inputHeight = $(this).height();
    var customPadding = 17; //custom modal padding (bootstrap modal)! 
    var topDatepicker = (offsetInput + inputHeight + customPadding) - offsetModal;
    console.log(topDatepicker);
    $("#ui-datepicker-div").css({top: topDatepicker});
});
$(document).on("click", "#schedBtn", function() {
    $('#schedModal').modal('show');
});

$(document).on("change", "#option", function() {
    $('.dtsched').show();
    $('.fsr').show();
    $('#fsrno').val('');
    if ($(this).val() == "P") {
        $('#datesched').attr('placeholder', 'Select PM Date');
        $('.labl').html('PM Date:');
    }else if ($(this).val() == "C") {
        $('#datesched').attr('placeholder', 'Select Conversion Date');
        $('.labl').html('Conversion Date:');
    }else if ($(this).val() == "N") {
        $('#datesched').attr('placeholder', 'Select Opening Date');
        $('.labl').html('Opening Date:');
    }
    $('#datesched').val('');
});

$(document).on('click', '#clientdiv', function () {
   $('#client').prop('disabled', false);
   if ($('#client').is(':disabled')) { 
        clientselected = 'no';
   }
});

$(document).on('click', '#prevBtn', function () {
   window.location.href = '/pmlist';
});

// $(document).on('keyup', '#customer', function(){ 
//     var withclient = 'no';
//     var clientname = "";
//     $('#clientlist').fadeOut();  
//     if ($('#client').is(':enabled')) {
//         if ($('#client').val()) {
//             withclient = 'yes';
//             clientname = $('#client').val();
//             if (clientselected != "yes") {
//                 alert("Incorrect Client Name!");
//             }
//         }else{
//             $('#client').val('');
//         }
//     }
//     var query = $(this).val();
//     var ul = '<ul class="dropdown-menu" style="display:block; position:relative;overflow: scroll;height: 13em;z-index: 200;">';
//     if(query != ''){
//         $.ajax({
//             url:"hint",
//             type:"get",
//             headers: {
//                 'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
//             },
//             data:{
//                 hint:query,
//                 withclient: withclient,
//                 clientname: clientname,
//             },
//             success:function(data){
//                 var datas = $.map(data, function(value, index) {
//                     return [value];
//                 });
//                 datas.forEach(value => {
//                     ul+='<li style="color:black" id="licustomer">'+value.customer_branch+'</li>';
//                 });
//                 console.log(ul);
//                 $('#branchlist').fadeIn();  
//                 $('#branchlist').html(ul);
//                 $('#saveBtn').prop('disabled', true);
//                 go = 'no';
//             }
//         });
//     }
// });


$(document).on('keyup', '.fsrno', function () {
    var ul = '<ul class="dropdown-menu" style="display:block; position:relative;overflow: scroll;height: 13em;z-index: 200;">';
    if(query != ''){
        $.ajax({
            url:"hint",
            type:"get",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                hint:query,
                withclient: 'no'
            },
            success:function(data){
                var datas = $.map(data, function(value, index) {
                    return [value];
                });
                datas.forEach(value => {
                    ul+='<li style="color:black" count="'+count+'">'+value.customer_branch+'</li>';
                });
                $('#branchlist'+count).fadeIn();  
                $('#branchlist'+count).html(ul);
                $('#out_sub_Btn').prop('disabled', true);
                $('#client'+count).val('');  
            }
        });
    }
});


$(document).on('click', '#pmTable tbody tr', function () {
    var trdata = table.row(this).data();
    branchCode = trdata.customer_branches_code;
    console.log(trdata);
    $('#customer').val(trdata.client.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#schedModal').modal('show');
});
$(document).on('click', '#convdatesched', function () {
    console.log('test');
    
});
$(document).on('click', '#saveBtn', function () {
    if ($('#datesched').val() != "" && $('#fsrno').val() != "" && $('#fsrno').val().length == 10) {
        $('#loading').show();
        $.ajax({
            url: 'checkfsr',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'get',
            data: {
                fsrno: $('#fsrno').val()
            },
            success:function(data)
            {
                if (data == "meron") {
                    alert('Invalid FSR number, already exist');
                    $('#loading').hide();
                }else{
                    $('#schedModal').modal('hide');
                    $.ajax({
                        url: 'schedule',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                        },
                        dataType: 'json',
                        type: 'POST',
                        data: {
                            schedule: $('#datesched').val(),
                            customer: $('#customer').val(),
                            customer_code: branchCode,
                            fsrno: $('#fsrno').val(),
                            type: 'P'
                        },
                        success:function(data)
                        {
                            location.reload();
                        },
                        error: function (data) {
                            alert(data.responseText);
                        }
                    });
                }
            },
            error: function (data) {
                alert(data.responseText);
            }
        });
    }else{
        if ($('#datesched').val() == "") {
            alert('PM Date is Required.');
        }else if ($('#fsrno').val() == "") {
            alert('You need to upload first the FSR in the FSR System before you could post here. Make sure you choose the correct branch and date of the PM for the correct FSR Number to appear.');
        }else if ($('#fsrno').val().length != 10) {
            alert('Invalid FSR number.');
        }
    }
});

$(document).on('click', '.cancel', function(){
    location.reload();
});

$(document).on('click', 'li', function(){  
    var select = $(this).text();
    var id = $(this).attr('id');
    if (id == 'licustomer') {
        $('#customer').val($(this).text());  
        $('#branchlist').fadeOut();  
        $.ajax({
            url:"hint",
            type:"get",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                client:'yes',
                branch: select.trim()
            },
            success:function(data){
                if (data) {
                    $('#client').val(data);  
                    $('#saveBtn').prop('disabled', false);
                    go = 'yes';
                    if ($('#datesched').val()) {
                        $('#saveBtn').prop('disabled', false);
                    }else{
                        $('#saveBtn').prop('disabled', true);
                    }
                }else{
                    $('#client').val('');  
                    go = 'no';
                    $('#saveBtn').prop('disabled', true);
                }
            }
        });
    }else{
        clientselected = "yes";
        $('#client').val($(this).text());  
        $('#clientlist').fadeOut();
        go = 'no';
        $('#saveBtn').prop('disabled', true);
    }
    
});
$(document).on('keyup', '#client', function(){ 
    var query = $(this).val();
    clientselected = 'no';
    $('#branchlist').fadeOut();  
    $('#out_sub_Btn').prop('disabled', true);
    var ul = '<ul class="dropdown-menu" style="display:block; position:relative;overflow: scroll;height: 13em;z-index: 200;">';
    if(query != ''){
        $.ajax({
            url:"getclient",
            type:"get",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                hint:query,
            },
            success:function(data){
                var datas = $.map(data, function(value, index) {
                    return [value];
                });
                datas.forEach(value => {
                    ul+='<li style="color:black" id="liclient">'+value.customer+'</li>';
                });
                $('#clientlist').fadeIn();  
                $('#clientlist').html(ul);
                $('#customer').val('');  
                go = 'no';
                $('#saveBtn').prop('disabled', true);
            }
        });
    }
});
