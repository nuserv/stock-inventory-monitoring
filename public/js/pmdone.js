var table;
var year = 2021;
var dt = new Date();
var curmonth = dt.getMonth()+1;
var curyear = dt.getFullYear();
var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
var go ='no';
$(document).ready(function()
{
    $('#pmTable thead tr:eq(0) th').each( function () {
        var title = $(this).text().trim();
        $(this).html( '<input type="text" style="width:100%" placeholder="Search '+title+'" class="column_search" />' );
    });
    table =
    $('table.pmTable').DataTable({ 
        "dom": 'rtip',
        "language": {
            "emptyTable": " ",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span>'
        },
        processing: true,
        serverSide: false,
        ajax: {
            url: 'pmlistdata',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'date', name:'date'},
            { data: 'area', name:'area'},
            { data: 'service_center', name:'service_center'},
            { data: 'fsrno', name:'fsrno'},
            { data: 'client', name:'client'},
            { data: 'branch', name:'branch'},
            { data: 'user', name:'user'}
        ]
    });
    $('#pmTable thead').on( 'keyup', ".column_search",function () {
    table
        .column( $(this).parent().index() )
        .search( this.value )
        .draw();
    });
    var yearoption = '<option selected disabled>select year</option>';
    for (let index = year; index <= curyear; index++) {
        yearoption += '<option value="'+index+'">'+index+'</option>';
    }
    $("#yearselect").find('option').remove().end().append(yearoption);
    $('#genBtn').prop('disabled', true);
});

$(document).on('change', '#yearselect', function(){
    var monthoption = '<option selected disabled>select month</option>';
    if ($(this).val() == year) {
        for (let index = 9; index <= curmonth ; index++) {
            monthoption += '<option value="'+index+'">'+months[index-1]+'</option>';
        }
    }else if ($(this).val() == curyear) {
        for (let index = 1; index <= curmonth ; index++) {
            monthoption += '<option value="'+index+'">'+months[index-1]+'</option>';
        }
    }else{
        for (let index = 1; index <= 12 ; index++) {
            monthoption += '<option value="'+index+'">'+months[index-1]+'</option>';
        }
    }
    $("#monthselect").find('option').remove().end().append(monthoption);
    $('#monthselect').prop('disabled', false);
    $('#monthselect').val('select month');
    $('#monthto').val('select month');
    $('#monthto').prop('disabled', true);
    $('#genBtn').prop('disabled', true);
    $('#select_area').prop('disabled', true);
    $('#select_area').val('select area');
    $('#select_branch').prop('disabled', true);
    $('#select_branch').val('select branch');
});

$(document).on('change', '#monthselect', function(){
    var monthoption = '<option selected disabled>select month</option>';
    for (let index = $(this).val(); index <= curmonth ; index++) {
        monthoption += '<option value="'+index+'">'+months[index-1]+'</option>';
    }
    $("#monthto").find('option').remove().end().append(monthoption);
    $('#monthto').prop('disabled', false);
    $('#monthto').val('select month');
    $('#genBtn').prop('disabled', true);
    $('#select_area').prop('disabled', true);
    $('#select_area').val('select area');
    $('#select_branch').prop('disabled', true);
    $('#select_branch').val('select branch');
});

$(document).on('change', '#select_area', function(){
    var branchOp = " ";
    $.ajax({
        url: 'getbranch',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        type: 'get',
        data: {
            areaid: $(this).val()
        },
        success: function(data){
            var branch = $.map(data, function(value, index) {
                return [value];
            });
            branchOp+='<option selected disabled>select branch</option>';
            branch.forEach(value => {
                if (value.branch != "Conversion") {
                    if (value.branch != "Test Branch") {
                        branchOp+='<option value="'+value.id+'">'+value.branch+'</option>';
                    }
                }
            });
            $("#select_branch").find('option').remove().end().append(branchOp);
            $('#select_branch').prop('disabled', false);
            $('#genBtn').prop('disabled', true);
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
            return false;
        }
    });
});

$(document).on('change', '#monthto', function(){
    if ($('#userlevel').val() == "Manager" || $('#userlevel').val() == "Editor") {
        $('#select_area').prop('disabled', false);
        $('#select_area').val('select area');
        $('#select_branch').prop('disabled', true);
        $('#select_branch').val('select branch');
        $('#genBtn').prop('disabled', true);
    }else{
        $('#genBtn').prop('disabled', false);
    }
});
$(document).on('change', '#select_branch', function(){
    $('#genBtn').prop('disabled', false);
});

$(document).on('click', '#genBtn', function(){
    $('#loading').show();
    if ($('#userlevel').val() == "Manager" || $('#userlevel').val() == "Editor") {
        window.location.href = 'export?year='+$('#yearselect').val()+'&from='+$('#monthselect').val()+'&to='+$('#monthto').val()+'&branch='+$('#select_branch').val();
        $('#exportModal').modal('hide');
        setTimeout(function(){
            $('#loading').hide();
            location.reload();
        },1500);
    }else{
        window.location.href = 'export?year='+$('#yearselect').val()+'&from='+$('#monthselect').val()+'&to='+$('#monthto').val();
        $('#exportModal').modal('hide');
        setTimeout(function(){
            $('#loading').hide();
            location.reload();
        },1500);
    }
});


$(document).on('click', '.cancel', function(){
    location.reload();
});

$(document).on('change', '#client', function(){
    $('table.pmTable').dataTable().fnDestroy();
    $(".modal-footer").empty().append('<input type="button" class="btn btn-primary cancel mr-auto" value="Cancel">');
    var year = $('#yearselect').val();
    var month = $('#monthselect').val();
    var customer_id = $(this).val();
    table =
        $('table.pmTable').DataTable({ 
            "dom": 'Brtip',
            "language": {
                "emptyTable": " ",
                "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span>'
            },
            processing: true,
            serverSide: false,
            async: false,
            ajax: {
                url: 'genpm',
                data:{
                    customer_id:customer_id,
                    year:year,
                    month:month
                },
                error: function(data) {
                    if(data.status == 401) {
                        window.location.href = '/login';
                    }
                }
            },
            columns: [
                { data: 'date', name:'date'},
                { data: 'fsrno', name:'fsrno'},
                { data: 'client', name:'client'},
                { data: 'branch', name:'branch'},
                { data: 'user', name:'user'}
            ],
            buttons: {
                dom: {
                    button: {
                        className: 'btn btn-primary' //Primary class for all buttons
                    }
                },
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        title: months[month-1].toUpperCase()+' '+year+' \n'+$('#branchname').val().toUpperCase()+' PM REPORTS',
                        filename: months[month-1].toUpperCase()+' '+year+' \n'+$('#branchname').val().toUpperCase()+' PM REPORTS',
                        text: '<span class="icon text-white-50"><i class="fa fa-save" style="color:white"></i></span><span> DOWNLOAD</span>',
                        exportOptions: {
                            modifier: {
                                page: 'current'
                            },
                            columns: [ 0,1,3,4 ]
                        },
                        orientation : 'landscape',
                        customize: function (doc) {
                            console.log(doc.content);
                            console.log(doc.document);
                            doc.defaultStyle.fontSize = 10;
                            doc.styles.tableHeader.fontSize = 12;
                            doc.styles.tableHeader.alignment = 'left';
                            doc.content[1].table.widths = ['13%','10%','*','20%'];
                            doc.pageMargins = [10,20,10,10];
                                // Splice the image in after the header, but before the table
                            doc.content.splice( 0, 0, {
                                margin: [ 0, 0, 0, 12 ],
                                alignment: 'center',
                                image: 'data:image/png;base64,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'
                            });
                            // $(doc.content[0].body)
                            //     .prepend('<img style="position:absolute; top:10; left:20;width:100;margin-botton:50px" src="'+window.location.origin+'/idsi.png">')
                                
                        }
                    }
                ]
            }
        });
    table.buttons().container().appendTo('.modal-footer');
    $('#genBtn').prop('disabled', false);
});


$(document).on("click", "#schedBtn", function() {
    $('#schedModal').modal({
        backdrop: 'static',
        keyboard: false
    });
});

$(document).on('click', '#clientdiv', function () {
   $('#client').prop('disabled', false);
   if ($('#client').is(':disabled')) { 
        clientselected = 'no';
   }
});
$(document).on('click', '#listBtn', function () {
    $('#exportModal').modal({
        backdrop: 'static',
        keyboard: false
    });
});


$(document).on('click', '#sadadlistBtn', function () {
    if ($(this).val() == "CREATE LIST") {
        var rowcount = table.data().count();
        var rows = table.rows( '.selected' ).data();
        var idss = new Array();
        if (rows.length > 0) {
            $('#loading').show();
            $(this).val('EXPORT');
            $('#listBtn').hide();
            $('table.pmTable').dataTable().fnDestroy();
            table =
                $('table.pmTable').DataTable({ 
                    "dom": 'Blrtip',
                    "language": {
                        "emptyTable": " ",
                        "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span>'
                    },
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: 'pmlistdata',
                        error: function(data) {
                            if(data.status == 401) {
                                window.location.href = '/login';
                            }
                        }
                    },
                    columns: [
                        { data: 'date', name:'date'},
                        { data: 'client', name:'client'},
                        { data: 'branch', name:'branch'},
                        { data: 'user', name:'user'}
                    ],
                    buttons: {
                        dom: {
                            button: {
                                className: 'btn btn-primary' //Primary class for all buttons
                            }
                        },
                        buttons: [
                            {
                                extend: 'pdfHtml5',
                                title: $('#branchname').val().toUpperCase()+' PM REPORTS',
                                text: '<span class="icon text-white-50"><i class="fa fa-save" style="color:white"></i></span><span> SAVE TO DOWNLOADS</span>',
                                exportOptions: {
                                    modifier: {
                                        page: 'current'
                                    },
                                    columns: [ 1,2,3,4 ]
                                },
                                orientation : 'landscape',
                                customize: function (doc) {
                                    console.log(doc.content);
                                    console.log(doc.document);
                                    doc.defaultStyle.fontSize = 10;
                                    doc.styles.tableHeader.fontSize = 12;
                                    doc.styles.tableHeader.alignment = 'left';
                                    doc.content[1].table.widths = ['20%','20%','*','20%'];
                                    doc.pageMargins = [10,20,10,10];
                                      // Splice the image in after the header, but before the table
                                    doc.content.splice( 0, 0, {
                                        margin: [ 0, 0, 0, 12 ],
                                        alignment: 'center',
                                        image: 'data:image/png;base64,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'
                                    });
                                    // $(doc.content[0].body)
                                    //     .prepend('<img style="position:absolute; top:10; left:20;width:100;margin-botton:50px" src="'+window.location.origin+'/idsi.png">')
                                        
                                }
                            }
                        ]
                    }
                });
            setTimeout(function() {
                for(var i=0;i<rows.length;i++){
                    idss.push(rows[i].id);
                }
                var ids = new Array();
                for(var i=0;i<rowcount;i++){
                    if ($.inArray(table.rows( i ).data()[0].id, idss) == -1)
                    {
                        ids.push(i);
                    }
                }
                table.rows( ids ).remove().draw();
                if ($('#listBtn').val() == 'EXPORT') {
                    $('#listBtn').prop('disabled', false);
                }
                $('#loading').hide();
                table.buttons().container().appendTo('#exportBtn');
            }, 800);
            
        }
    }else if ($(this).val() == 'EXPORT') {
        var rowcount = table.data().count();
        var exportids = new Array();
        for(var i=0;i<rowcount;i++){
            exportids.push(table.rows( i ).data()[0].id);
        }
        $.ajax({
            url: 'export',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'get',
            data: {
                exportids: exportids
            },
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
                alert(data.responseText);
                return false;
            }
        });
        
    }
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

$(document).on('click', '#saveBtn', function(){ 
    if ($('#datesched').val()) {
        $.ajax({
            url: 'schedule',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'POST',
            async: false,
            data: {
                schedule: $('#datesched').val(),
                customer : $('#customer').val()
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
