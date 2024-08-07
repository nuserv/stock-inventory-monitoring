var table;
var go ='no';
var branchCode, custid;
$(document).ready(function()
{
    $("#datesched").datepicker({
        onSelect: function(dateText, inst) { 
            if (!$('#fsrno').val()) {
                $('#saveBtn').prop('disabled', true);
            }else{
                $('#saveBtn').prop('disabled', false);
            }
        },
        format: 'YYYY-MM-DD',
        minViewMode: 1,
        autoclose: true,
        maxDate: 0,
        minDate: -30
    });

    $('#pmTable thead tr:eq(0) th').each( function () {
        var title = $(this).text().trim();
        $(this).html( '<input type="text" style="width:100%" placeholder="Search '+title+'" class="column_search" />' );
    });

    table =
    $('table.pmTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "emptyTable": " ",
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
            { data: 'area', name:'area'},
            { data: 'branch', name:'branch'},
            { data: 'client', name:'client'},
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

$(document).on('click', '#pmTable tbody tr', function () {
    var trdata = table.row(this).data();
    branchCode = trdata.customer_branches_code;
    custid = branchCode;
    $('#customer_code').prop('readonly', true);
    $('#customer_code').val(branchCode);
    $('#customer_name').val(trdata.client.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#branches').val(trdata.branch_id);
    $('#branchesDiv').show();
    $('#head-title').html('UPDATE PM BRANCH')
    $('#subBtn').val('UPDATE');
    $('#PmModal').modal('show');
});

$(document).on("click", "#schedBtn", function() {
    $('#schedModal').modal('show');
});

$(document).on('click', '#addPMBtn', function () {
    console.log('show')
    $('#branchesDiv').hide();
    $('#head-title').html('ADD PM BRANCH')
    $('#subBtn').val('ADD');
    $('#subBtn').prop('disabled', true);
    $('#branches').val('')
    $('#customer_name').val('')
    $('#customer_code').val('');
    $('#customer_code').prop('readonly', false);
    $('#PmModal').modal('show');
});


$(document).on('keyup', '#customer_code', function(){
    if($(this).attr('readonly')){
        return false;
    }
    var inputField = document.getElementById("customer_code");
    inputField.addEventListener("input", function(event) {
    var value = inputField.value;
    var newValue = "";
    for (var i = 0; i < value.length; i++) {
        if (!isNaN(value[i])) {
        newValue += value[i];
        }
    }
    inputField.value = newValue.slice(0, 6);
    });
    var id = $(this).val();
    custid;
    $('#subBtn').prop('disabled', true);
    $.ajax({
        type:'get',
        url:'getPm-client',
        async: false,
        data:{
            'id':id
        },
        success:function(data)
        {   
            $('#note').html('');
            $('#branches').val('');
            $('#branchesDiv').hide();
            if (data == 'none') {
                $('#customer_name').val('CUSTOMER NOT FOUND!');
            }else{
                custid = data.code;
                $('#customer_name').val(data.customer_branch);
                $.ajax({
                    type:'get',
                    url:'checkPm-client',
                    async: false,
                    data:{
                        'id':custid
                    },
                    success:function(data)
                    {   
                        if (data == 'none') {
                            $('#branchesDiv').show();
                        }else{
                            $('#note').html('Note:The selected branch has been already assigned to '+data.branch+' office.');
                        }
                    },
                });
            }
        },
    });
});
$(document).on('change', '#branches', function () {
    $('#subBtn').prop('disabled', false);
});

$(document).on('click', '#subBtn', function () {
    $('#PmModal').modal('hide');
    $('#loading').show();
    setTimeout(() => {
        if ($(this).val() == "ADD") {
            $.ajax({
                type:'get',
                url:'checkPm-client',
                async: false,
                data:{
                    'id':custid,
                    'type':'add',
                    'branch_id': $('#branches').val()
                },
                success:function(data)
                {   
                    $('#loading').hide();
                    Swal.fire({
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
            });
        }
        else{
            $.ajax({
                type:'get',
                url:'checkPm-client',
                async: false,
                data:{
                    'id':custid,
                    'type':'update',
                    'branch_id': $('#branches').val()
                },
                success:function(data)
                {   
                    $('#loading').hide();
                    Swal.fire({
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
            });
        }
    }, 300);
})

$(document).on("keyup", "#fsrno", function() {
    if ($('#datesched').val() && $(this).val()) {
        $('#saveBtn').prop('disabled', false);
    }else{
        $('#saveBtn').prop('disabled', true);
    }
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

$(document).on('keyup', '#customer', function(){ 
    var withclient = 'no';
    var clientname = "";
    $('#clientlist').fadeOut();  
    if ($('#client').is(':enabled')) {
        if ($('#client').val()) {
            withclient = 'yes';
            clientname = $('#client').val();
            if (clientselected != "yes") {
                alert("Incorrect Client Name!");
            }
        }else{
            $('#client').val('');
        }
    }
    var query = $(this).val();
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
                withclient: withclient,
                clientname: clientname,
            },
            success:function(data){
                var datas = $.map(data, function(value, index) {
                    return [value];
                });
                datas.forEach(value => {
                    ul+='<li style="color:black" id="licustomer">'+value.customer_branch+'</li>';
                });
                console.log(ul);
                $('#branchlist').fadeIn();  
                $('#branchlist').html(ul);
                $('#saveBtn').prop('disabled', true);
                go = 'no';
            }
        });
    }
});


$(document).on('click', '#saveBtn', function () {

   if ($('#datesched').val() != "" && $('#fsrno').val() != "") {

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
                fsrno: $('#fsrno').val()
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
